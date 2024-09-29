<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Flash;

class CobroController extends Controller
{
    public function __construct()
    {
        ## para confirmar que el usuario este logueado para cargar la pagina
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        ## recibir datos del formulario
        $input = $request->all();

        ## validar que exista forma de pago cargados
        if (!$request->has('forma_pago') || count($input['forma_pago']) === 0) {
            alert()->error("Atención", "Debe especificar al menos 1 forma de pago.!");
            return redirect()->back()->withInput($input);
        }

        ##utilizar transacciones en laravel
        DB::beginTransaction();
        ##utilizar bloque try catch para manejo de errores
        try {
            if ($request->has('forma_pago')) { ##el campo es un array de id_articulo
                foreach ($input['forma_pago'] as $key => $value) {
                    $amount   = str_replace(".", "", $input['importe'][$key]);
                    DB::table('cobros')->insert([
                        'id_venta' => $input['id_venta'],
                        'user_id' => auth()->user()->id,
                        'id_forma' => $value,
                        'cob_fecha' => Carbon::now()->format('Y-m-d'),
                        'cob_importe' => $amount,
                        'cob_estado' => 'PAGADO',
                        'nro_voucher' => $input['nro_voucher'][$key]
                    ]);
                }

                ##actualiza el estado de ventas a pagado
                DB::table('ventas')->where('id_venta', $input['id_venta'])->update([
                    'ven_estado' => 'PAGADO'
                ]);
            }
            DB::commit();
        } catch (\Exception $ex) {
            ## si los datos no fueron insertados correctament
            DB::rollBack();
            Log::error("ERROR DE CREACION DE COBROS:::::::::" . $ex->getMessage());

            alert()->error("Atención", "Error en la insercion de pagos.!");
            return redirect()->back()->withInput($input);
        }

        alert()->success("Exíto", "Cobro guardado correctamente.!");
        return redirect(route('ventas.index'));
    }
}
