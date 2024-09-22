<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Flash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Luecano\NumeroALetras\NumeroALetras;

class VentaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
            ->join('users', 'users.id', 'ventas.user_id')
            ->orderBy('ventas.id_venta', 'desc')
            ->paginate(10);

        ##consultar cajas para enviar variable al modal apertura
        $cajas = DB::table('caja')
            ->where('cod_suc', auth()->user()->cod_suc)
            ->pluck('caj_descri', 'caj_cod');

        ##validar que no exista una caja abierta para el usuario
        $cajaAbierta = DB::table('apertura_cierre')
            ->where('user_id', auth()->user()->id)
            ->where('ape_estado', 'Abierta')
            ->first();

        ## Ejemplo numeros a letras
        #$formatter = new NumeroALetras();
        #dd($formatter->toWords('12500000'));


        return view('ventas.index')->with('ventas', $ventas)
            ->with('cajas', $cajas)
            ->with('cajaAbierta', $cajaAbierta);
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
        $sucursales = DB::table('sucursal')
            ->where('cod_suc', auth()->user()->cod_suc)
            ->pluck('suc_descri', 'cod_suc');

        ##verificar la ultima factura impresa para generar la siguiente
        $caja = DB::table('apertura_cierre')->select(
            DB::raw("lpad('1', 3, '0') AS establecimiento"),
            DB::raw(
                "lpad(cast(caja.caj_punto_expedicion as character), 3, '0') as punto_expedicion"
            ),
            DB::raw(
                "lpad(cast(coalesce(max(caja.caj_ult_fac), 0) + 1 as character), 7, '0')
                    as nro_factura"
            ),
            'apertura_cierre.ape_fecha',
            'apertura_cierre.ape_nro',
            'apertura_cierre.ape_estado',
            'caja.cod_suc'
        )
            ->join('caja', 'caja.caj_cod', 'apertura_cierre.caj_cod')
            ->where('apertura_cierre.user_id', auth()->user()->id)
            ->where('apertura_cierre.ape_estado', 'Abierta')
            ->groupBy(
                'caja.caj_punto_expedicion',
                'apertura_cierre.ape_fecha',
                'apertura_cierre.ape_nro',
                'apertura_cierre.ape_estado',
                'caja.cod_suc'
            )
            ->first();

        # Validamos que exista la caja y que la fecha de apertura no sea menor a la fecha actual
        if (
            !empty($caja) &&
            Carbon::parse($caja->ape_fecha)->format('Y-m-d') < Carbon::now()->format('Y-m-d')
        ) {

            alert()->error("Atención", "Cerrar Caja de fecha anterior antes de continuar.!");
            return redirect(route('ventas.index'));
        }

        return view('ventas.create')->with('clientes', $clientes)
            ->with('sucursales', $sucursales)
            ->with('caja', $caja)
            ->with('condicion', $condicion);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        if (!$request->has('codigo') || count($input['codigo']) === 0) {
            # Si en envio es mediante ajax realizar un return
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe especificar el detalle.!'
                ]);
            }

            alert()->error("Atención", "Debe especificar el detalle.!");
            return redirect()->back()->withInput($input);
        }

        $validator = Validator::make(
            $input,
            [
                'id_cliente' => 'required',
                'cod_suc' => 'required',
                'ven_fecha' => 'required',
                'ven_condicion' => 'required',
                'nro_factura' => 'required|max:15',
            ],
            [
                'id_cliente.required' => 'El campo "Cliente" es obligatorio.',
                'cod_suc.required' => 'El campo "Código de Sucursal" es obligatorio.',
                'ven_fecha.required' => 'El campo "Fecha de Venta" es obligatorio.',
                'ven_condicion.required' => 'El campo "Condición de Venta" es obligatorio.',
                'nro_factura.required' => 'El campo "Número de Factura" es obligatorio.',
                'nro_factura.max' => 'El campo "Número de Factura" no puede tener más de 15 caracteres.',
            ]
        );

        if ($validator->fails()) {
            # Manejar los errores del validator
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verificar Datos..!',
                    'errors' => $validator->errors() // Incluye los errores del validator
                ]);
            }

            alert()->error("Atención", "Verificar Datos..!");
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        ## Validar que la factura nro sea unica
        $validarFactura = DB::table('ventas')->where('nro_factura', $input['nro_factura'])->first();

        if (!empty($validarFactura)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El número de factura ya existe.!',
                ]);
            }

            alert()->error('Atención', 'El número de factura ya existe.!');
            return redirect(route('ventas.create'))->withInput();
        }

        ## Validar Fecha
        $fecha  = Carbon::parse($input['ven_fecha'])->format('Y-m-d');
        ## Caputurar fecha actual
        $actual = Carbon::now()->format('Y-m-d');
        ## Comparar fecha actual con la fecha recibida del formulario
        if ($fecha > $actual) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha venta no puede ser mayor a la fecha actual.!',
                ]);
            }

            alert()->error("Atención", "La fecha venta no puede ser mayor a la fecha actual.!");
            return redirect(route('ventas.create'))->withInput();
        }

        ## Utilizar transacciones para grabar las ventas
        DB::beginTransaction();
        try {
            $input['ven_total'] = !empty($input['ven_total']) ? str_replace(".", "", $input['ven_total']) : 0;
            $input['cant_cuo']  = !empty($input['cant_cuo']) ? $input['cant_cuo'] : 0;

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

            ##actualizar ultima factura generada una ves insertado la cebecera y detalle venta
            ##consultamos la tabla apertura cierre y caja por el estado de la apertura y el usuario que realizo la venta
            $ultimaFactura = DB::table('caja')
                ->join('apertura_cierre', 'apertura_cierre.caj_cod', 'caja.caj_cod')
                ->where('apertura_cierre.ape_estado', 'Abierta')
                ->where('apertura_cierre.user_id', auth()->user()->id)
                ->first();

            if (!empty($ultimaFactura)) {
                ##realizamos un explode para extraer solo el nro de factura que seria los ultimos 7 digitos
                ##el explode convierte nuestra variable $input['nro_factura'] en un array segun el delimitador -
                ##y accedemos a la posicion 2 porque la factura por defecto es 001-001-0000010 y el dato que necesitamos seria el 10
                $factura = explode("-", $input['nro_factura'])[2];

                ##actualizamos la tabla caja el campo caj_ult_fac
                DB::update(
                    "UPDATE caja SET caj_ult_fac = ? where caj_cod = ?",
                    [
                        (int)$factura, ##ultimos 7 digitos de factura
                        $ultimaFactura->caj_cod ##codigo de caja a modificar
                    ]
                );
            }

            ## Si todo esta bien creamos la ventas
            DB::commit();
        } catch (\Exception $ex) {
            ## si los datos no fueron insertados correctament
            DB::rollBack();
            Log::error("ERROR DE CREACION DE VENTAS:::::::::" . $ex->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en la creacion de ventas.!',
                ]);
            }

            alert()->error("Atencinó", "Error en la creacion de ventas.!");
            return redirect()->back()->withInput($input);
        }

        # Si la carga fue correcta imprimo el mensaje de exito
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Venta generada correctamente.!',
            ]);
        }

        alert()->success("Exito", "Venta generada correctamente.!");

        return redirect(route('ventas.index'));
    }

    public function buscarProducto(Request $request)
    {
        $query   = strtoupper($request->get('query'));
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
                ->limit(20)
                ->get(); // Cargar todos los productos si no hay búsqueda
        }

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
            ->join('users', 'users.id', 'ventas.user_id')
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
        if ($ventas->ven_condicion == "CREDITO") {
            ## Borrar las cuentas de cobrar asociadas a la venta
            $ctas = DB::table('ctas_cobrar')->where('id_venta', $ventas->id_venta)->delete();
        }

        ## Actualizar el stock si anulamos la ventan
        $detalle = DB::table('det_venta')->where('id_venta', $id)->get();
        foreach ($detalle as $item) {
            DB::update(
                'UPDATE stock SET cantidad = cantidad + ? where id_articulo = ? and cod_suc = ?',
                [
                    $item->det_cantidad,
                    $item->id_articulo,
                    $ventas->cod_suc
                ]
            );
        }

        Flash::success('La venta se ha anulado correctamente..!');
        return redirect(route('ventas.index'));
    }

    public function edit($id)
    {
        $ventas = DB::table('ventas')->where('id_venta', $id)->first();

        ##validar que exista la venta
        if (empty($ventas)) {
            Flash::error('Registro no encontrado.!');

            return redirect(route('ventas.index'));
        }

        $condicion = ["CONTADO" => "CONTADO", "CREDITO" => "CREDITO"];
        ## Cargar select clientes
        $clientes = DB::table('clientes')->select(
            DB::raw("concat(cli_nombre, ' ', cli_apellido) as clientes,
            id_cliente")
        )
            ->pluck('clientes', 'id_cliente');

        ## Cargar select sucursales
        $sucursales = DB::table('sucursal')->pluck('suc_descri', 'cod_suc');

        ## Recuperar el detalle de la venta
        $detalle_venta = DB::table('det_venta')->select(
            'det_venta.*',
            'articulos.art_descripcion'
        )
            ->join('articulos', 'articulos.id_articulo', 'det_venta.id_articulo')
            ->where('det_venta.id_venta', $ventas->id_venta)
            ->get();


        return view('ventas.edit')->with('ventas', $ventas)
            ->with('det_venta', $detalle_venta)
            ->with('sucursales', $sucursales)
            ->with('clientes', $clientes)
            ->with('condicion', $condicion);
    }

    public function update(Request $request, $id)
    {
        $ventas = DB::table('ventas')->where('id_venta', $id)->first();

        $input = $request->all();

        ##validar que exista la venta
        if (empty($ventas)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ]);
            }

            alert()->error('Error', 'Registro no encontrado.!');
            return redirect(route('ventas.index'));
        }

        ##utilizar transacciones en laravel
        DB::beginTransaction();
        ##utilizar bloque try catch para manejo de errores
        try {
            ##buscar ctas si posee
            $searchCtas = DB::table('ctas_cobrar')
                ->where('id_venta', $ventas->id_venta)
                ->where('cta_estado', 'PAGADO')
                ->exists();

            ## si existe irmprimimos un mensaje de advertencia
            if ($searchCtas) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No puede actualizar la venta porque ya existe ctas pagadas'
                    ]);
                }

                alert()->error('Error', 'No puede actualizar la venta porque ya existe ctas pagadas.!');

                return redirect()->back()->withInput($input);
            }

            ##editar cabecera de venta solo algunos datos
            DB::update(
                "UPDATE ventas SET
                    ven_condicion = ?,
                    ven_total = ?,
                    cant_cuo = ?,
                    intervalo = ?
                where id_venta = ? ",
                [
                    $input['ven_condicion'],
                    $input['ven_total'],
                    $input['cant_cuo'],
                    $input['intervalo'],
                    $ventas->id_venta
                ]
            );

            $subtotal = 0;
            if ($request->has('codigo')) { ##el campo es un array de id_articulo
                foreach ($input['codigo'] as $key => $value) {
                    ##hacemos un str_replace del precio unitario para quitar el separador de miles
                    $amount   = str_replace(".", "", $input['precio'][$key]);
                    ##acumulamos el total de la venta multiplicando el dato precio unitario * cantidad
                    $subtotal   += (int)$amount * $input['cantidad'][$key];
                    ##utilazar updateOrcreate
                    ##actualizar si posee cambios caso contrario agrega
                    DB::table('det_venta')
                        ->updateOrCreate( ## es una funcion nueva de laravel si existe el dato actualiza si no crea nuevo registro
                            ['id_det_venta' => $input['id_det_venta'][$key]], ## se captura el id de la tabla det_venta
                            [
                                'id_venta'          => $ventas->id_venta,
                                'id_articulo'       => $value,
                                'det_precio_unit'   => $input['precio'][$key],
                                'det_cantidad'      => $input['cantidad'][$key],
                                'det_subtotal'      => $subtotal
                            ]
                        );
                    ##actualizar nuestra tabla de stock deacuerdo al articulo vendido y la sucursal
                    $stock = DB::table('stock')->where('id_articulo', $value)->first();
                    if (!empty($stock)) {
                        DB::update(
                            'UPDATE stock SET
                        cantidad = cantidad - ?
                        where id_articulo = ? and cod_suc = ?',
                            [
                                $input['cantidad'][$key],
                                $value,

                                $input['cod_suc']
                            ]
                        );
                    } else {
                        ##hacer el insert
                    }
                }
            }
            DB::commit();
        } catch (\Exception $ex) {
            ## si los datos no fueron insertados correctament
            DB::rollBack();
            Log::error("ERROR EN LA EDICION  DE VENTAS:::::::::" . $ex->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en la edicion de ventas.!'
                ]);
            }

            alert()->error('Error', 'Error en la edicion de ventas.!');

            return redirect()->back()->withInput($input);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Registro actualizado correctamente.!'
            ]);
        }

        alert()->success('Exíto', 'Registro actualizado correctamente.!');

        return redirect(route('ventas.index'));
    }

    public function imprimirFactura($idventa = null)
    {
        $ventas = DB::table('ventas')
            ->select(
                'ventas.*',
                'clientes.cli_nombre',
                'clientes.cli_apellido',
                'clientes.cli_ci',
                'clientes.cli_direccion',
                'clientes.cli_telefono'
            )
            ->join('clientes', 'clientes.id_cliente', 'ventas.id_cliente')
            ->where('ventas.id_venta', $idventa)
            ->first();

        ##validar que exista la venta
        if (empty($ventas)) {
            alert()->error('Error', 'Registro no encontrado.!');

            return redirect(route('ventas.index'));
        }

        $detalle_venta = DB::table('det_venta')->select(
            'det_venta.*',
            'articulos.art_descripcion'
        )
            ->join('articulos', 'articulos.id_articulo', 'det_venta.id_articulo')
            ->where('det_venta.id_venta', $ventas->id_venta)
            ->get();

        ## Ejemplo numeros a letras
        $formatter = new NumeroALetras();
        $totalLetras =$formatter->toWords($ventas->ven_total);



        return view('ventas.factura')
            ->with('ventas', $ventas)
            ->with('letras', $totalLetras)
            ->with('detalles', $detalle_venta);
    }
}