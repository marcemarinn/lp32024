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
        DB::table('compras')->insert([
            'prov_id' => $input['prov_id'],
            'cod_suc' => $input['cod_suc'],
            'com_fecha' => $input['com_fecha'],
            'com_user_id' => auth()->user()->id,
            'com_condicion' => $input['com_condicion'],
            'com_total' => $input['com_total'],
            'com_descripcion' => $input['com_descripcion'] ?? null, // Nueva descripción
            'com_cant_cuo' => $input['com_cat_cuo'] ?? null,
            'com_plazo' => $input['com_plazo'] ?? null,
            'com_estado' => 'Pendiente',

        ]);

        return redirect()->route('compras.index')->with('success', 'Compra creada exitosamente.');
    }
}
