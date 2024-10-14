<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Flash;
use RealRashid\SweetAlert\Facades\Alert;


class CiudadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ciudades index')->only('index');
        $this->middleware('permission:ciudades create')->only('create', 'store');
        $this->middleware('permission:ciudades edit')->only('edit', 'update');
        $this->middleware('permission:ciudades destroy')->only('destroy');
        #$this->middleware('permission:buscar')->only('buscar');
    }


    public function index()
    {
        ## la consulta que genera el codigo
        ## "select * from ciudad"
        $ciudades = DB::table('ciudad')->orderBy('id_ciudad', 'DESC')->paginate(5);

        ## Alerta para confirmacion de eliminado
        confirmDelete('Borrar', "Desea eliminar el registro.?");

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
            ##especificando que es un error
            #Alert::error('Atención', 'La ciudad ya existe');

            Alert::alert('Atención', 'La ciudad ya existe', 'error');

            #alert()->error('Atención', 'La ciudad ya existe!');
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
        alert()->success('Exito', 'Registro creado correctamente!');
        ##redireccionar a la lista de ciudades una ves guardado el dato
        return redirect(route('ciudades.index'));
    }

    public function edit($id)
    {
        $ciudad = DB::table('ciudad')->where('id_ciudad', $id)->first();

        if (empty($ciudad)) {
            alert()->error('Atención', 'La ciudad no existe!');
            return redirect(route('ciudades.index'));
        }

        return view('ciudads.edit')->with('ciudades', $ciudad);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $ciudad = DB::table('ciudad')->where('id_ciudad', $id)->first();

        if (empty($ciudad)) {
            alert()->error('Atención', 'La ciudad no existe!');
            return redirect(route('ciudades.index'));
        }

        ##validar datos
        $ciudad = DB::table('ciudad')
            ->where('ciu_descripcion', '=', strtoupper($input['ciu_descripcion']))
            ->whereNotIn('id_ciudad', [$id])
            ->first();
        ##SELECT * FROM ciudad WHERE id_ciudad not in(1)

        if (!empty($ciudad)) {
            alert()->error('Atención', 'La ciudad ya existe!');
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

        alert()->success('Atención', 'Registro actualizado correctamente!');

        return redirect(route('ciudades.index'));
    }

    public function destroy($id)
    {
        $ciudad = DB::table('ciudad')->where('id_ciudad', $id)->first();

        if (empty($ciudad)) {
            alert()->error('Atención', 'La ciudad no existe!');
            return redirect(route('ciudades.index'));
        }

        DB::table('ciudad')->where('id_ciudad', $id)->delete();
        #DB::delete('delete from ciudad where id = ?', array('id' => $id));

        alert()->success('Exito', 'Registro eliminado correctamente.!');

        return redirect(route('ciudades.index'));
    }

    public function ciudadDepartamento(Request $request)
    {
        $departamento = $request->get('departamento_id');
        
        if(empty($departamento)){
            return response()->json([
                'success' =>false,
                'mensaje' =>"Variable vacia" 
            ]);
        }

        $ciudad = DB::table('ciudad')->where('departamento_id', $departamento)->get();

        return response()->json([
            'success' => true,
            'data' => $ciudad
        ]);
    }
}