<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Flash;

class ArticuloController extends Controller
{
    public function index()
    {
        $articulo = DB::table('articulos')
        ->select(
            'articulos.*',
            'marcas.mar_descrip',
        )
        ->leftJoin('marcas', 'marcas.mar_cod', 'articulos.mar_cod')
        ->orderBy('id_articulo', 'desc')
        ->paginate(10);

        return view('articulos.index')->with('articulos', $articulo);

    }

    public function create()
    {
        $marcas = DB::table('marcas')->pluck('mar_descrip', 'mar_cod');
        $iva = ['0' => 'Exenta', '5' => 'Gravada 5%', '10' => 'Gravada 10%' ];

        return view('articulos.create')
       -> with('marcas',$marcas)
       -> with('iva',$iva);

    }

    public function store(Request $request)
    {
            
        ##recibir los datos del formulario
        $input = $request->all();

        ##validar datos
        $articulo = DB::table('articulos')
            ->where('art_descripcion', '=', strtoupper($input['art_descripcion']))
            ->first();
    $marcas = DB::table('marcas')->pluck('mar_descrip', 'mar_cod');


        if (!empty($articulo)) {
            Flash::error('El articulo ya existe!');
            return redirect(route('articulos.create'))->withInput();
        }

        ##sql
        ##insert into ciudad(ciu_descripcion) values ('capiata);
        ##usar db para guardar los datos en la bd
        DB::table('articulos')->insert(
            [
                'art_descripcion' => strtoupper($input['art_descripcion']),
                'art_precio' => strtoupper($input['art_precio']),
                'art_imagen' => strtoupper($input['art_imagen']),
                'art_iva' => strtoupper($input['art_iva']),
                'mar_cod' => strtoupper($input['mar_cod'])
            ]
            
        );

             Flash::success('Registro creado correctamente!');
               return redirect(route('articulos.index'));
    }

    public function edit($id_articulo)
    {
        // Obtener el cliente a editar
        $articulo = DB::table('articulos')->where('id_articulo', $id_articulo)->first();
        
        if (empty($articulo)) {
            Flash::error('Articulo no encontrado');
            return redirect(route('articulos.index'));
        }
    
        $marcas = DB::table('marcas')->pluck('mar_descrip', 'mar_cod');
        $iva = ['0' => 'Exenta', '5' => 'Gravada 5%', '10' => 'Gravada 10%' ];

       
    
        // Retornar la vista de edición con los datos necesarios
        return view('articulos.edit')
            ->with('articulos', $articulo)
            -> with('marcas',$marcas)
            -> with('iva',$iva);
    }

    public function update(Request $request, $id_articulo)
    {
       
    
        $input = $request->all();
    
        // Verificar si el cliente existe
        $articulos = DB::table('articulos')->where('id_articulo', $id_articulo)->first();
        
        if (empty($articulos)) {
            Flash::error('Articulo no encontrado');
            return redirect(route('articulos.index'));
        }
    
        // Actualizar los datos del cliente
        DB::table('articulos')->where('id_articulo', $id_articulo)->update([
            'art_descripcion' => strtoupper($input['art_descripcion']),
                'art_precio' => strtoupper($input['art_precio']),
                'art_imagen' => strtoupper($input['art_imagen']),
                'art_iva' => strtoupper($input['art_iva']),
                'mar_cod' => strtoupper($input['mar_cod'])
    
            
        ]);
    
        Flash::success('Articulo actualizado correctamente.');
        return redirect(route('articulos.index'));
    }
    
    public function destroy($id_articulo)
{
    // Verificar si el cliente existe
    $articulo = DB::table('articulos')->where('id_articulo', $id_articulo)->first();

    if (empty($articulo)) {
        Flash::error('Articulo no encontrado');
        return redirect(route('clientes.index'));
    }

    // Eliminar el cliente
    DB::table('articulos')->where('id_articulo', $id_articulo)->delete();

    Flash::success('Articulo eliminado correctamente.');
    return redirect(route('articulos.index'));
}
}
