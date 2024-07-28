<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Flash;
use Laracasts\Flash\Flash as FlashFlash;

class DepartamentoController extends Controller
{
    public function index()
    {
        ## la consulta que genera el codigo
        ## "select * from ciudad"
        $departamentos = DB::table('departamento')->paginate(10);

        ##retorna un html que se genero en resources/views
        ## la referencia carpeta punto el nombre del archivo
        return view('departamentos.index')->with('departamentos', $departamentos);
    }
    ##para la creacion de datos
    public function create()
    {
        return view('departamentos.create');
    }

    ##para guardado
    public function store(Request $request)
    {
        ##recibir los datos del formulario
        $input = $request->all();

        ##validar datos
        $departamento = DB::table('departamento')
            ->where('dep_descripcion', '=', strtoupper($input['dep_descripcion']))
            ->first();

        if (!empty($departamento)) {
            Flash::error('El departamento ya existe!');
            return redirect(route('departamento.create'))->withInput();
        }

        ##sql
        ##insert into ciudad(ciu_descripcion) values ('capiata);
        ##usar db para guardar los datos en la bd
        DB::table('departamento')->insert(
            [
                'dep_descripcion' => strtoupper($input['dep_descripcion'])
            ]
        );

        ##imprimir mensaje
        FlashFlash::success('¡Cliente creado con éxito!')->important();

        ##redireccionar a la lista de ciudades una ves guardado el dato
        return redirect(route('departamento.index'));
    }

    public function edit($id_departamento)
    {
        $departamento = DB::table('departamento')->where('id_departamento', $id_departamento)->first();

        if (empty($departamento)) {
            Flash::error('El departamento no existe!');
            return redirect(route('departamento.index'));
        }

        return view('departamento.edit')->with('departamento',$departamento);
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


        DB::table('ciudad')->where('id_ciudad', $id)->delete();
        #DB::delete('delete from ciudad where id = ?', array('id' => $id));

        Flash::success('Registro eliminado correctamente!');

        return redirect(route('ciudades.index'));
    }

}
