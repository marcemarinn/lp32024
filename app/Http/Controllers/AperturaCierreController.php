<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class AperturaCierreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        
        // Realizar un replace del monto apertura para sacar el separador de miles
        $input['monto_apertura'] = !empty($input['monto_apertura']) ? str_replace(".", "", $input['monto_apertura']) : 0;

        // Realizar un insert en apertura
        DB::insert(
            'INSERT INTO apertura_cierre(caj_cod, user_id, ape_fecha, ape_monto_inicial, ape_estado)
            VALUES(?, ?, ?, ?, ?)',
            [
                $input['caj_cod'],
                auth()->user()->id,
                $input['fecha_apertura'],
                $input['monto_apertura'], // solo para datos numéricos
                'Abierta'
            ]
        );

        alert()->success("Éxito", "Apertura de caja realizada correctamente.");
        return redirect(route('ventas.index'));
    }

    public function editCierre($nro_ape)
    {
        $apertura_cierre = DB::table('apertura_cierre')
            ->where('ape_nro', $nro_ape)
            ->where('ape_estado', 'Abierta')
            ->first();

        Log::info('Apertura número: '. $nro_ape);

        if (empty($apertura_cierre)) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ]);
        }

        // Sumar todos los cobros de la caja del día según las ventas
        $totalCobros = DB::table('cobros')
            ->join('ventas', 'ventas.id_venta', 'cobros.id_venta')
            ->where('ventas.ape_nro', $apertura_cierre->ape_nro)
            ->sum('cobros.cob_importe');

        return response()->json([
            'success' => true,
            'total'    => $totalCobros,
            'apertura' => $apertura_cierre,
        ]);
    }

    public function cerrar_caja($nro_ape, Request $request)
    {
        $input = $request->all();

        $existe = DB::table('apertura_cierre')
            ->where('ape_nro', $nro_ape)
            ->where('ape_estado', 'Cerrada')
            ->exists();

        if (!$existe) {
            $apertura_cierre = DB::table('apertura_cierre')
                ->where('ape_nro', $nro_ape)
                ->where('ape_estado', 'Abierta')
                ->first();

            if (empty($apertura_cierre)) {
                alert()->error("Atención", "Registro no encontrado.");
                return redirect(route('ventas.index'));
            }

            // Remover puntos del monto de cierre
            $input['monto_cierre'] = str_replace(".", "", $input['monto_cierre']);

            // Actualizar la caja como cerrada
            DB::update(
                "UPDATE apertura_cierre SET ape_mon_cierre = ?, ape_estado = ? WHERE ape_nro = ?",
                [
                    $input['monto_cierre'],
                    'Cerrada',
                    $apertura_cierre->ape_nro
                ]
            );
        }

        // Obtener datos de la caja cerrada
        $caja_cerrada = DB::table('apertura_cierre')->select(
            'apertura_cierre.*',
            'sucursal.suc_descri',
            'users.name as usuario',
            'caja.caj_descri as caja'
        )
            ->join('caja', 'caja.caj_cod', 'apertura_cierre.caj_cod')
            ->join('sucursal', 'sucursal.cod_suc', 'caja.cod_suc')
            ->join('users', 'users.id', 'apertura_cierre.user_id')
            ->where('ape_nro', $nro_ape)
            ->where('ape_estado', 'Cerrada')
            ->first();

        // Obtener el detalle de los cobros
        $resultado_apertura = DB::table('cobros')
            ->select(
                'cobros.*',
                'forma_pagos.descripcion as forma_pago',
                'forma_pagos.id_forma'
            )
            ->join('ventas', 'ventas.id_venta', 'cobros.id_venta')
            ->join('forma_pagos', 'forma_pagos.id_forma', 'cobros.id_forma')
            ->where('ventas.ape_nro', $nro_ape)
            ->get();

        // Generar el PDF y pasar las variables necesarias a la vista
        $pdf = PDF::loadView('ventas.cierre_caja_pdf', [
            'resultado_apertura' => $resultado_apertura,
            'caja_cerrada' => $caja_cerrada
        ]);

        // Retornar el PDF
        return $pdf->stream('cierre_caja.pdf');
    }
}
