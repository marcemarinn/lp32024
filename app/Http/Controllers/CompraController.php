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
    }

    // Función para listar las compras
    public function index()
{
    // Realizamos la consulta para obtener los datos necesarios
    $compras = DB::table('compras')
        ->select(
            'compras.*',
            DB::raw("concat(proveedores.prov_nombre) AS proveedor"),
            'sucursal.suc_descri as sucursal',
            'users.name as comprador'
        )
        ->join('proveedores', 'proveedores.prov_id', '=', 'compras.prov_id')
        ->join('sucursal', 'sucursal.cod_suc', '=', 'compras.cod_suc')
        ->join('users', 'users.id', '=', 'compras.com_user_id')
        ->orderBy('compras.compra_id', 'desc')
        ->paginate(10);

    return view('compras.index', compact('compras'));
}


   // Función para mostrar el formulario de creación de compras
    public function create()
    {
    $proveedores = DB::table('proveedores')->pluck('prov_nombre', 'prov_id');
    $sucursales = DB::table('sucursal')->pluck('suc_descri', 'cod_suc');
    $condiciones = ['CONTADO' => 'CONTADO', 'CREDITO' => 'CREDITO'];

    // Obtener artículos existentes de la base de datos
    $articulos = DB::table('articulos')->select('id_articulo', 'art_descripcion', 'art_precio')->get();

    return view('compras.create', compact('proveedores', 'sucursales', 'condiciones', 'articulos'));
    }


    public function store(Request $request)
    {
        $input = $request->all();
    
        $validator = Validator::make($input, [
            'prov_id' => 'required',
            'cod_suc' => 'required',
            'com_fecha' => 'required|date',
            'com_condicion' => 'required|in:CONTADO,CREDITO', // Validamos CONTADO o CREDITO
            'com_total' => 'required|numeric',
            'com_cat_cuo' => 'required_if:com_condicion,CREDITO|numeric|min:1', // Requerido si es CREDITO
            'com_plazo' => 'required_if:com_condicion,CREDITO|numeric|min:1', // Requerido si es CREDITO
            'com_descripcion' => 'nullable|string|max:255',
        ], [
            'com_condicion.required' => 'El campo "Condición de Compra" es obligatorio.',
            'com_condicion.in' => 'La "Condición de Compra" debe ser CONTADO o CREDITO.',
            'com_cat_cuo.required_if' => 'El campo "Cantidad de Cuotas" es obligatorio para compras a crédito.',
            'com_plazo.required_if' => 'El campo "Plazo de Pago" es obligatorio para compras a crédito.',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Almacenar compra
        $compra_id = DB::table('compras')->insertGetId([
            'prov_id' => $input['prov_id'],
            'cod_suc' => $input['cod_suc'],
            'com_fecha' => $input['com_fecha'],
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

}