<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CobroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $input = $request->all();

        if (!$request->has('forma_pago') || count($input['forma_pago']) === 0) {
            alert()->error("Atención", "Debe especificar al menos 1 forma de pago!");
            return redirect()->back()->withInput($input);
        }

        $ventas = DB::table('ventas')->where('id_venta', $input['id_venta'])->first();

        if(empty($ventas)){
            alert()->error("Atención", "Registro no encontrado!");
            return redirect(route('ventas.index'));
        }
        $cta_cobrar = DB::table('ctas_cobrar')->where('id_venta', $input['id_venta'])->first();
    
    if(empty($cta_cobrar)){
        alert()->error("Atención", "No se encontró una cuenta por cobrar para esta venta!");
        return redirect()->back()->withInput($input);
    }

    $cta_id = $cta_cobrar->cta_id;

        DB::beginTransaction();
        $totales = 0;
        try {
            if ($request->has('forma_pago')) {
                foreach ($input['forma_pago'] as $key => $value) {
                    $amount = str_replace(".", "", $input['importe'][$key]);
                    $totales += $amount;
                    
                    $cobroData = [
                        'id_venta' => $input['id_venta'],
                        'user_id' => auth()->user()->id,
                        'id_forma' => $value,
                        'cob_fecha' => Carbon::now()->format('Y-m-d'),
                        'cob_importe' => $amount,
                        'cob_estado' => 'PAGADO',
                        'nro_voucher' => $input['nro_voucher'][$key],
                        'cta_id' => $cta_id
                    ];

                    // Solo añadimos id_cobro si no es nulo
                    if (isset($input['id_cobro'][$key]) && $input['id_cobro'][$key] !== null) {
                        $cobroData['id_cobro'] = $input['id_cobro'][$key];
                    }

                    DB::table('cobros')->insert($cobroData);
                }

                if((int)$totales !== (int)$ventas->ven_total){
                    DB::rollBack();
                    alert()->info("Atención", "Los totales de los pagos no coinciden con el total de la venta!");
                    return redirect()->back()->withInput($input);
                }

                DB::table('ventas')->where('id_venta', $input['id_venta'])->update([
                    'ven_estado' => 'PAGADO'
                ]);
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error("ERROR DE CREACION DE COBROS: " . $ex->getMessage());
            alert()->error("Atención", "Error en la inserción de pagos: " . $ex->getMessage());
            return redirect()->back()->withInput($input);
        }

        alert()->success("Éxito", "Cobro guardado correctamente!");
        return redirect(route('ventas.index'));
    }
}