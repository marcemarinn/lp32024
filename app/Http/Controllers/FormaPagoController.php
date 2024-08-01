<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Flash;

use Illuminate\Http\Request;

class FormaPagoController extends Controller
{
    public function index()
    {
        ## la consulta que genera el codigo
        ## "select * from ciudad"
        $forma_pagos = DB::table('forma_pagos')->paginate(10);

        ##retorna un html que se genero en resources/views
        ## la referencia carpeta punto el nombre del archivo
        return view('forma_pagos.index')->with('forma_pagos', $forma_pagos);
    }
    ##para la creacion de datos
    public function create()
    {
        return view('forma_pagos.create');
    }

    ##para guardado
    public function store(Request $request)
    {
        ##recibir los datos del formulario
        $input = $request->all();

        ##validar datos
        $forma_pagos = DB::table('forma_pagos')
            ->where('descripcion', '=', strtoupper($input['descripcion']))
            ->first();

        if (!empty($forma_pagos)) {
            Flash::error('La forma pago ya existe!');
            return redirect(route('forma_pagos.create'))->withInput();
        }

        ##sql
        ##insert into ciudad(ciu_descripcion) values ('capiata);
        ##usar db para guardar los datos en la bd
        DB::table('forma_pagos')->insert(
            [
                'descripcion' => strtoupper($input['descripcion'])
            ]
        );

        ##imprimir mensaje
        Flash::success('Registro creado correctamente!');
        ##redireccionar a la lista de ciudades una ves guardado el dato
        return redirect(route('forma_pagos.index'));
    }

    public function edit($id)
    {
        $formaPago= DB::table('forma_pagos')->where('id_forma', $id)->first();

        if (empty($formaPago)) {
            Flash::error('La ciudad no existe!');
            return redirect(route('forma_pagos.index'));
        }

        return view('forma_pagos.edit')->with('forma_pagos', $formaPago);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $forma_pagos= DB::table('forma_pagos')->where('id_forma', $id)->first();

        if (empty($forma_pagos)) {
            Flash::error('La $forma_pagos no existe!');
            return redirect(route('forma_pagos.index'));
        }

        ##validar datos
        $forma_pagos = DB::table('forma_pagos')
            ->where('descripcion', '=', strtoupper($input['descripcion']))
            ->whereNotIn('id_forma', [$id])
            ->first();
        ##SELECT * FROM ciudad WHERE id_ciudad not in(1)

        if (!empty($forma_pagos)) {
            Flash::error('La forma_pagos ya existe!');
            return redirect(route('forma_pagos.edit', [$id]))->withInput();
        }

        ##forma 1
        DB::table('forma_pagos')->where('id_forma', $id)
            ->update([
                'descripcion' => strtoupper($input['descripcion'])
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

        return redirect(route('forma_pagos.index'));
    }

    public function destroy($id)
    {
        $forma_pagos = DB::table('forma_pagos')->where('id_forma', $id)->first();

        if (empty($forma_pagos)) {
            Flash::error('La $forma_pagos no existe!');
            return redirect(route('forma_pagos.index'));
        }

        DB::table('forma_pagos')->where('id_forma', $id)->delete();
        #DB::delete('delete from ciudad where id = ?', array('id' => $id));

        Flash::success('Registro eliminado correctamente!');

        return redirect(route('forma_pagos.index'));
    }

    public function show($id_forma)
    {
        $forma_pagos = DB::table('forma_pagos')->where('id_forma', $id_forma)->first();

        if (empty($forma_pagos)) {
            Flash::error('La entidad emisora no existe!');
            return redirect(route('forma_pagos.index'));
        }
    
        return view('forma_pagos.show')->with('forma_pagos', $forma_pagos);

    }
}
