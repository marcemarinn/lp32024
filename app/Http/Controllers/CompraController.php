<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CompraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:supervisor')->only('approve'); // Middleware de rol para la función de aprobación

    }

    public function generarNumeroFactura()
    {
        // Bloques fijos o dinámicos (puedes cambiarlos según tu lógica de negocio)
        $bloque1 = '001';  // Puede representar la sucursal
        $bloque2 = '001';  // Puede representar el tipo de transacción

        // Obtener el último número de factura de la base de datos
        $ultimaCompra = DB::table('compras')->latest('compra_id')->first();

        if ($ultimaCompra) {
            // Extraer el último bloque secuencial del último número de factura
            $ultimoNumeroSecuencia = intval(substr($ultimaCompra->numero_factura, -7)); // Extraer los últimos 7 dígitos
        } else {
            // Si no hay compras previas, comenzamos con 0000001
            $ultimoNumeroSecuencia = 0;
        }

        // Incrementar el número secuencial
        $nuevoNumeroSecuencia = str_pad($ultimoNumeroSecuencia + 1, 7, '0', STR_PAD_LEFT); // Añadir ceros a la izquierda

        // Construir el nuevo número de factura
        $numeroFactura = $bloque1 . '-' . $bloque2 . '-' . $nuevoNumeroSecuencia;

        return $numeroFactura;
    }

    // Función para listar las compras
    public function index(Request $request)
{
    // Inicializar la consulta base
    $query = DB::table('compras')
        ->select(
            'compras.*',
            DB::raw("concat(proveedores.prov_nombre) AS proveedor"),
            'sucursal.suc_descri as sucursal',
            'users.name as comprador'
        )
        ->join('proveedores', 'proveedores.prov_id', '=', 'compras.prov_id')
        ->join('sucursal', 'sucursal.cod_suc', '=', 'compras.cod_suc')
        ->join('users', 'users.id', '=', 'compras.com_user_id');

    // Aplicar filtros si existen
    if ($request->has('proveedor') && $request->proveedor != null) {
        $query->where('compras.prov_id', $request->proveedor);
    }

    if ($request->has('desde') && $request->desde != null) {
        $query->whereDate('compras.com_fecha', '>=', $request->desde);
    }

    if ($request->has('hasta') && $request->hasta != null) {
        $query->whereDate('compras.com_fecha', '<=', $request->hasta);
    }

    // Obtener todos los registros si no se aplican filtros
    $compras = $query->orderBy('compras.compra_id', 'desc')->paginate(10);

    return view('compras.index', compact('compras'));
}


   // Función para mostrar el formulario de creación de compras
    public function create()
    {
        $proveedores = DB::table('proveedores')->pluck('prov_nombre', 'prov_id');
        $sucursales = DB::table('sucursal')->pluck('suc_descri', 'cod_suc');
        $condiciones = ['CONTADO' => 'CONTADO', 'CREDITO' => 'CREDITO'];
        $articulos = DB::table('articulos')->select('id_articulo', 'art_descripcion', 'art_precio')->get();
        $numeroFactura = $this->generarNumeroFactura();

    
        return view('compras.create', compact('proveedores', 'sucursales', 'condiciones', 'articulos', 'numeroFactura'));
    }
    


    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'prov_id' => 'required',
            'cod_suc' => 'required',
            'com_fecha' => 'required|date',
            'com_condicion' => 'required|in:CONTADO,CREDITO',
            'com_total' => 'required|numeric',
            'com_cat_cuo' => 'nullable|required_if:com_condicion,CREDITO|numeric|min:1', // Solo requerido si es CREDITO
            'com_plazo' => 'nullable|required_if:com_condicion,CREDITO|numeric|min:1',   // Solo requerido si es CREDITO
            'com_descripcion' => 'nullable|string|max:255',
        ], [
            'com_condicion.required' => 'El campo "Condición de Compra" es obligatorio.',
            'com_condicion.in' => 'La "Condición de Compra" debe ser CONTADO o CREDITO.',
            'com_cat_cuo.required_if' => 'El campo "Cantidad de Cuotas" es obligatorio para compras a crédito.',
            'com_plazo.required_if' => 'El campo "Plazo de Pago" es obligatorio para compras a crédito.',
        ]);

        $numeroFactura = $this->generarNumeroFactura();
        
        
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $input['com_total'] = str_replace(".", "", $input['com_total']); // Eliminar puntos
        $input['com_total'] = str_replace(",", ".", $input['com_total']); // Cambiar la coma por punto si es necesario
        
        // Almacenar compra
        $compra_id = DB::table('compras')->insertGetId([
            'prov_id' => $input['prov_id'],
            'cod_suc' => $input['cod_suc'],
            'com_fecha' => $input['com_fecha'],
            'numero_factura' => $numeroFactura,
            'com_user_id' => auth()->user()->id,
            'com_condicion' => $input['com_condicion'],
            'com_total' => $input['com_total'],
            'com_descripcion' => $input['com_descripcion'] ?? null,
            'com_cant_cuo' => $input['com_cat_cuo'] ?? null,
            'com_plazo' => $input['com_plazo'] ?? null,
            'com_estado' => 'Pendiente',
        ], 'compra_id'); // Usar 'compra_id' como clave primaria
    
        // Insertar detalle_compras
        if (!empty($input['detalle_compras'])) {
            foreach ($input['detalle_compras'] as $detalle) {
                DB::table('detalle_compras')->insert([
                    'compra_id' => $compra_id,
                    'id_articulo' => $detalle['id_articulo'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unit' => $detalle['precio_unit'],
                ]);
            }
        }
    
        return redirect()->route('compras.index')->with('success', 'Compra creada exitosamente.');
    }


    public function show($id)
    {
        $compra = DB::table('compras')
            ->select('compras.*', 'proveedores.prov_nombre', 'sucursal.suc_descri', 'users.name as comprador')
            ->join('proveedores', 'proveedores.prov_id', '=', 'compras.prov_id')
            ->join('sucursal', 'sucursal.cod_suc', '=', 'compras.cod_suc')
            ->join('users', 'users.id', '=', 'compras.com_user_id')
            ->where('compras.compra_id', $id)
            ->first();

        if (!$compra) {
            return redirect()->route('compras.index')->with('error', 'Compra no encontrada.');
        }

        $detalles = DB::table('detalle_compras')
            ->select('detalle_compras.*', 'articulos.art_descripcion')
            ->join('articulos', 'articulos.id_articulo', '=', 'detalle_compras.id_articulo')
            ->where('detalle_compras.compra_id', $id)
            ->get();

        return view('compras.show', compact('compra', 'detalles'));
    }

     // Función para aprobar la compra
     public function approve($id)
     {
         // Lógica para aprobar la compra
         DB::table('compras')->where('compra_id', $id)->update(['com_estado' => 'Aprobado']);
         
         return redirect()->route('compras.index')->with('success', 'Compra aprobada exitosamente.');
     }

}