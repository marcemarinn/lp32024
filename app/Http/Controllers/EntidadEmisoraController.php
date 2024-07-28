<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Flash;


use Illuminate\Http\Request;

class EntidadEmisoraController extends Controller
{
    public function index()
    {
        ## la consulta que genera el codigo
        ## "select * from ciudad"
        $entidadEmisora = DB::table('entidad_emisora')->paginate(10);

        ##retorna un html que se genero en resources/views
        ## la referencia carpeta punto el nombre del archivo
        return view('entidad_emisoras.index')->with('entidademisoras', $entidadEmisora);
    }

    public function create()
    {
        return view('entidad_emisoras.create');

    }

    public function store(Request $request)
    {
            
        ##recibir los datos del formulario
        $input = $request->all();

        ##validar datos
        $entidadEmisora = DB::table('entidad_emisora')
            ->where('enti_descri', '=', strtoupper($input['enti_descri']))
            ->first();

        if (!empty($entidadEmisora)) {
            Flash::error('La entidad emisora ya existe!');
            return redirect(route('entidad_emisoras.create'))->withInput();
        }

        ##sql
        ##insert into ciudad(ciu_descripcion) values ('capiata);
        ##usar db para guardar los datos en la bd
        DB::table('entidad_emisora')->insert(
            [
                'enti_descri' => strtoupper($input['enti_descri'])
                
            ]
            
        );

        ##imprimir mensaje
        Flash::success('Registro creado correctamente!');
       
        ##redireccionar a la lista de ciudades una ves guardado el dato
        return redirect(route('entidademisora.index'));
    }

    public function edit($enti_cod)
    {
        $entidadEmisora = DB::table('entidad_emisora')->where('enti_cod', $enti_cod)->first();

        if (empty($entidadEmisora)) {
            Flash::error('La entidadEmisora no existe!');
            return redirect(route('entidademisora.index'));
        }

        return view('entidad_emisoras.edit')->with('entidad_emisora', $entidadEmisora);

        
    }


    public function update(Request $request, $enti_cod)
    {
        $input = $request->all();

        $entidadEmisora = DB::table('entidad_emisora')
        ->where('enti_cod', $enti_cod)
        ->first();

        if (empty($entidadEmisora)) {
            Flash::error('La ciudad no existe!');
            return redirect(route('entidad_emisoras.index'));
        }

        ##validar datos
        $ciudad = DB::table('entidad_emisora')
            ->where('enti_descri', '=', strtoupper($input['enti_descri']))
            ->whereNotIn('enti_cod', [$enti_cod])
            ->first();
        ##SELECT * FROM ciudad WHERE id_ciudad not in(1)


        ##forma 1
        DB::table('entidad_emisora')->where('enti_cod', $enti_cod)
            ->update([
                'enti_descri' => strtoupper($input['enti_descri'])
            ]);


        Flash::success('Registro actualizado correctamente!');

        return redirect(route('entidad_emisora.index'));
    }

    public function destroy($enti_cod)
    {

    }


    public function show($enti_cod)
    {
        $entidadEmisora = DB::table('entidad_emisora')->where('enti_cod', $enti_cod)->first();

        if (empty($entidadEmisora)) {
            Flash::error('La entidad emisora no existe!');
            return redirect(route('entidademisora.index'));
        }
    
        return view('entidad_emisoras.show')->with('entidadEmisora', $entidadEmisora);

    }

}
