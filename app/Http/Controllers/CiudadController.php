<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Flash;



class CiudadController extends Controller
{
    public function index()
    {
        ## la consulta que genera el codigo
        ## "select * from ciudad"
        $ciudades = DB::table('ciudad')->get();

        ##retorna un html que se genero en resources/views
        ## la referencia carpeta punto el nombre del archivo
        return view('ciudads.index')->with('ciudad', $ciudades);
    }
    ##para la creacion de datos
    public function create()
    {
        return view('ciudads.create');
    }

    ##para guardado
    public function store(Request $request)
    {
        ##recibir los datos del formulario
        $input = $request->all();

        ##validar datos
        $ciudad = DB::table('ciudad')
            ->where('ciu_descripcion', '=', strtoupper($input['ciu_descripcion']))
            ->first();

        if (!empty($ciudad)) {
            Flash::error('La ciudad ya existe!');
            return redirect(route('ciudades.create'))->withInput();
        }

        ##sql
        ##insert into ciudad(ciu_descripcion) values ('capiata);
        ##usar db para guardar los datos en la bd
        DB::table('ciudad')->insert(
            [
                'ciu_descripcion' => strtoupper($input['ciu_descripcion'])
            ]
        );

        ##imprimir mensaje
        Flash::success('Registro creado correctamente!');
        ##redireccionar a la lista de ciudades una ves guardado el dato
        return redirect(route('ciudades.index'));
    }

    public function edit($id)
    {
        $ciudad = DB::table('ciudad')->where('id_ciudad', $id)->first();

        if (empty($ciudad)) {
            Flash::error('La ciudad no existe!');
            return redirect(route('ciudades.index'));
        }

        return view('ciudads.edit')->with('ciudades', $ciudad);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $ciudad = DB::table('ciudad')->where('id_ciudad', $id)->first();

        if (empty($ciudad)) {
            Flash::error('La ciudad no existe!');
            return redirect(route('ciudades.index'));
        }

        ##validar datos
        $ciudad = DB::table('ciudad')
            ->where('ciu_descripcion', '=', strtoupper($input['ciu_descripcion']))
            ->whereNotIn('id_ciudad', [$id])
            ->first();
        ##SELECT * FROM ciudad WHERE id_ciudad not in(1)

        if (!empty($ciudad)) {
            Flash::error('La ciudad ya existe!');
            return redirect(route('ciudades.edit', [$id]))->withInput();
        }

        ##forma 1
        DB::table('ciudad')->where('id_ciudad', $id)
            ->update([
                'ciu_descripcion' => strtoupper($input['ciu_descripcion'])
            ]);

        ##forma 2
        /*DB::update(
            'update ciudad set descripcion = ? where id_ciudad = ?',
            [
                strtoupper($input['ciu_descripcion']),
                $id
            ]
        );*/

        Flash::success('Registro actualizado correctamente!');

        return redirect(route('ciudades.index'));
    }

    public function destroy($id)
    {
        $ciudad = DB::table('ciudad')->where('id_ciudad', $id)->first();

        if (empty($ciudad)) {
            Flash::error('La ciudad no existe!');
            return redirect(route('ciudades.index'));
        }

        DB::table('ciudad')->where('id_ciudad', $id)->delete();
        #DB::delete('delete from ciudad where id = ?', array('id' => $id));

        Flash::success('Registro eliminado correctamente!');

        return redirect(route('ciudades.index'));
    }

}
