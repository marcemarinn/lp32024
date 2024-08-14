<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Flash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = DB::table('ventas')->select(
            'ventas.*',
            DB::raw("concat(clientes.cli_nombre,' ',clientes.cli_apellido) AS clientes"),
            'sucursal.suc_descri as sucursales',
            'users.name as vendedor'
        )
            ->join('clientes', 'clientes.id_cliente', 'ventas.id_cliente')
            ->join('sucursal', 'sucursal.cod_suc', 'ventas.cod_suc')
            ->join('users', 'users.id', 'ventas.usu_cod')
            ->paginate(10);

        return view('ventas.index')->with('ventas', $ventas);
    }

    public function create()
    {
        $condicion = ["CONTADO" => "CONTADO", "CREDITO" => "CREDITO"];
        ## Cargar select clientes
        $clientes = DB::table('clientes')->select(
            DB::raw("concat(cli_nombre, ' ', cli_apellido) as clientes,
            id_cliente")
        )
            ->pluck('clientes', 'id_cliente');

        ## Cargar select sucursales
        $sucursales = DB::table('sucursal')->pluck('suc_descri', 'cod_suc');

        return view('ventas.create')->with('clientes', $clientes)
            ->with('sucursales', $sucursales)
            ->with('condicion', $condicion);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make(
            $input,
            [
                'id_cliente' => 'required',
                'cod_suc' => 'required',
                'ven_fecha' => 'required',
                'ven_condicion' => 'required',
                'nro_factura' => 'required|max:13',
            ],
            [
                'id_cliente.required' => 'El campo "Cliente" es obligatorio.',
                'cod_suc.required' => 'El campo "Código de Sucursal" es obligatorio.',
                'ven_fecha.required' => 'El campo "Fecha de Venta" es obligatorio.',
                'ven_condicion.required' => 'El campo "Condición de Venta" es obligatorio.',
                'nro_factura.required' => 'El campo "Número de Factura" es obligatorio.',
                'nro_factura.max' => 'El campo "Número de Factura" no puede tener más de 13 caracteres.',
            ]
        );

        if ($validator->fails()) {
            Flash::error("Verificar Datos..!");
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        ## Validar que la factura nro sea unica
        $validarFactura = DB::table('ventas')->where('nro_factura', $input['nro_factura'])->first();

        if (!empty($validarFactura)) {
            Flash::error('El número de factura ya existe.!');
            return redirect(route('ventas.create'))->withInput();
        }

        ## Validar Fecha
        $fecha  = Carbon::parse($input['ven_fecha'])->format('Y-m-d');
        ## Caputurar fecha actual
        $actual = Carbon::now()->format('Y-m-d');
        ## Comparar fecha actual con la fecha recibida del formulario
        if ($fecha > $actual) {
            Flash::error("La fecha venta no puede ser mayor a la fecha actual.!");
            return redirect(route('ventas.create'))->withInput();
        }

        ## Utilizar transacciones para grabar las ventas
        DB::beginTransaction();
        try {
            $input['ven_total'] = !empty($input['ven_total']) ? str_replace(".", "", $input['ven_total']) : 0;
            $input['cant_cuo']  = !empty($input['cant_cuo']) ? $input['cant_cuo'] : 1;

            ## Insercion de cabecera ventas
            $insertVenta = DB::table('ventas')->insertGetId([
                'user_id'    => auth()->user()->id,
                'id_cliente' => $input['id_cliente'],
                'ven_fecha' => $input['ven_fecha'],
                'ven_condicion' => $input['ven_condicion'],
                'ven_total' => $input['ven_total'],
                'ven_estado' => "CONCRETADO",
                'cant_cuo' => $input['cant_cuo'],
                'nro_factura' => $input['nro_factura'],
                'cod_suc' => $input['cod_suc'],
                'intervalo' => $input['intervalo'],
            ], 'id_venta');

            ## Insertar los detalles
            $subtotal = 0;
            if ($request->has('codigo')) { ##el campo es un array de id_articulo
                foreach ($input['codigo'] as $key => $value) {
                    Log::info("codigo venta::::" . $insertVenta);
                    ##hacemos un str_replace del precio unitario para quitar el separador de miles
                    $amount   = str_replace(".", "", $input['precio'][$key]);
                    ##acumulamos el total de la venta multiplicando el dato precio unitario * cantidad
                    $subtotal += (int)$amount * $input['cantidad'][$key];

                    ##creamos el insert de detalle de ventas
                    DB::insert(
                        "INSERT INTO det_venta(id_articulo, id_venta, det_precio_unit, det_cantidad, det_subtotal)
                        VALUES(?, ?, ?, ?, ?)",
                        [
                            $value, ##seria el codigo del articulo recibido
                            $insertVenta, ##id de ventas
                            $amount,
                            $input['cantidad'][$key],
                            $subtotal
                        ]
                    );

                    ##actualizar nuestra tabla de stock deacuerdo al articulo vendido y la sucursal
                    DB::update(
                        'UPDATE stock SET cantidad = cantidad - ? where id_articulo = ? and cod_suc = ?',
                        [
                            $input['cantidad'][$key],
                            $value,
                            $input['cod_suc']
                        ]
                    );
                }
            }

            ## Si todo esta bien creamos la ventas
            DB::commit();
        } catch (\Exception $ex) {
            ## si los datos no fueron insertados correctament
            DB::rollBack();
            Log::error("ERROR DE CREACION DE VENTAS:::::::::" . $ex->getMessage());

            Flash::error("Error en la creacion de ventas.!");
            return redirect()->back()->withInput($input);
        }

        Flash::success("Venta generada correctamente.!");

        return redirect(route('ventas.index'));
    }

    public function buscarProducto(Request $request)
    {
        $query = strtoupper($request->get('query'));
        $cod_suc = $request->get('cod_suc');

        ## Si query es vacio mostrar todo los productos utilizando un limitador
        if ($query) {
            $productos = DB::table('articulos')
                ->select(
                    'articulos.*',
                    'stock.cantidad',
                    'stock.cod_suc'
                )
                ->join('stock', 'articulos.id_articulo', 'stock.id_articulo')
                ->whereRaw("(articulos.id_articulo::text like '%" . $query . "%'
                    or articulos.art_descripcion::text like '%" . $query . "%')")
                ->where('stock.cod_suc', $cod_suc) //filtrar por sucursal el stocks
                ->take(20) //limitador de resultados limit
                ->get();
            #dd($productos);
        } else {
            ## Cargar los primeros 20 productos si no hay búsqueda
            $productos = DB::table('articulos')
                ->select(
                    'articulos.*',
                    'stock.cantidad',
                    'stock.cod_suc'
                )
                ->join('stock', 'articulos.id_articulo', 'stock.id_articulo')
                ->where('stock.cod_suc', $cod_suc) //filtrar por sucursal el stocks
                ->take(20)
                ->get(); // Cargar todos los productos si no hay búsqueda
        }
        dd($productos);

        #return response()->json($productos);
        ## Retornar la variable productos segun el filtro a nuestro html de buscar_productos
        return view('ventas.buscar_producto')->with('productos', $productos);
    }

    public function show($id)
    {
        ## Visualizar venta y para ello realizar consulta de la cabecera y detalle
        $venta = DB::table('ventas')->select(
            'ventas.*',
            'clientes.cli_nombre',
            'clientes.cli_apellido',
            'users.name as vendedor',
            'sucursal.suc_descri as sucursal'
        )
            ->join('clientes', 'clientes.id_cliente', 'ventas.id_cliente')
            ->join('users', 'users.id', 'ventas.usu_cod')
            ->join('sucursal', 'sucursal.cod_suc', 'ventas.cod_suc')
            ->where('ventas.id_venta', $id)
            ->first();

        if (empty($venta)) {
            Flash::error("La venta no existe.!");
            return redirect(route('ventas.index'));
        }

        $detalle = DB::table('det_venta')->select(
            'det_venta.*',
            'articulos.art_descripcion',
        )
            ->join('articulos', 'articulos.id_articulo', 'det_venta.id_articulo')
            ->where('det_venta.id_venta', $id)
            ->get();

        return view('ventas.show')->with('ventas', $venta)->with('detalle', $detalle);
    }

    public function destroy($id)
    {
        $ventas = DB::table('ventas')->where('id_venta', $id)->first();

        if (empty($ventas)) {
            Flash::error("La venta no existe.!");
            return redirect(route('ventas.index'));
        }

        ## ANULAR LA VENTA
        DB::table('ventas')->where('id_venta', $id)->update([
            'ven_estado' => "ANULADO"
        ]);

        ## Borrar las cuentas que fueron generadas si es a credito
        if($ventas->ven_condicion == "CREDITO"){
            ## Borrar las cuentas de cobrar asociadas a la venta
            $ctas = DB::table('ctas_cobrar')->where('id_venta', $ventas->id_venta)->delete();
        }

        Flash::success('La venta se ha anulado correctamente..!');
        return redirect(route('ventas.index'));
    }
}
