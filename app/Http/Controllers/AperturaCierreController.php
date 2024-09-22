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
        # Realizar un replace del monto apertura para sacar el separador de miles
        $input['monto_apertura'] = !empty($input['monto_apertura']) ?
                                        str_replace(".", "", $input['monto_apertura']) : 0;

        # Realizar un insert en apertura
        DB::insert(
            'INSERT INTO apertura_cierre(caj_cod, user_id,
        ape_fecha, ape_monto_inicial, ape_estado)
        VALUES(?, ?, ?, ?, ?)',
            [
                $input['caj_cod'],
                auth()->user()->id,
                $input['fecha_apertura'],
                $input['monto_apertura'], ##solo para datos numericos
                'Abierta'
            ]
        );

        alert()->success("Exito", "Apertura de caja realizado correctamente.!");
        return redirect(route('ventas.index'));
    }

    public function editCierre($nro_ape)
    {
        $apertura_cierre = DB::table('apertura_cierre')
            ->where('ape_nro', $nro_ape)
            ->where('ape_estado', 'Abierta')
            ->first();

        Log::info('apertura nro:::'. $nro_ape);
        
        if (empty($apertura_cierre)) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ]);
        }

        # Sumamos todos los cobros de la caja del dia segun las ventas, para ello utilizamos sum()
        $totalCobros = DB::table('cobros')
            ->join('ventas', 'ventas.id_venta', 'cobros.id_venta')
            ->where('ventas.ape_nro', $apertura_cierre->ape_nro)
            ->sum('cobros.cob_importe');

        # Retorno el resultado como json
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
                alert()->error("Atención", "Registro no encontrado.!");

                return redirect(route('ventas.index'));
            }

            $input['monto_cierre'] = str_replace(".", "", $input['monto_cierre']);

            DB::update(
                "UPDATE apertura_cierre SET
                ape_mon_cierre = ?,
                ape_estado = ?
            WHERE ape_nro = ?",
                [
                    $input['monto_cierre'],
                    'Cerrada',
                    $apertura_cierre->ape_nro
                ]
            );
        }

        ##consulta de caja cerrada
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

        $resultado_apertura = DB::table('cobro')
            ->select(
                'cobros.*',
                'forma_pagos.descripcion as forma_pago',
                'forma_pagos.id_forma',
            )
            ->join('ventas', 'ventas.id_venta', 'cobros.id_venta')
            ->join('forma_pagos', 'forma_pagos.id_forma', 'cobros.id_forma')
            ->join('sucursal', 'sucursal.cod_suc', 'ventas.cod_suc')
            ->join('users', 'users.id', 'ventas.user_id')
            ->where('ventas.ape_nro', $nro_ape)
            ->get();


        $pdf = PDF::loadView('ventas.cierre_caja_pdf', compact('resultado_apertura', 'caja_cerrada'));

        return $pdf->stream('cierre_caja.pdf');
    }
}