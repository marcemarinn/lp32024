<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Flash;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = DB::table('clientes')
            ->select(
                'clientes.*',
                'ciudad.ciu_descripcion',
                'departamento.dep_descripcion'
            )
            ->leftJoin('ciudad', 'ciudad.id_ciudad', 'clientes.id_ciudad')
            ->leftJoin(
                'departamento',
                'departamento.id_departamento',
                'clientes.id_departamento'
            )
            ->orderBy('id_cliente', 'desc')
            ->paginate(10);

        return view('clientes.index')->with('clientes', $clientes);
    }

    public function create()
    {
        ## Cargar ciudad y departamento en blade create utilizando pluck
        $ciudades = DB::table('ciudad')->pluck('ciu_descripcion', 'id_ciudad');

        $departamentos = DB::table('departamento')->pluck('dep_descripcion', 'id_departamento');

        # Cargar array de sexo que serian datos
        $genero = array("M" => "MASCULINO", "F" => "FEMENINO", "O" => "OTROS");


        $genero2 = ["M" => "MASCULINO", "F" => "FEMENINO", "O" => "OTROS"];


        # Retornar a la vista las variables ciudades y deparmentos
        return view('clientes.create')
            ->with('ciudad', $ciudades)
            ->with('departamento', $departamentos)
            ->with('genero', $genero);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $fecha = Carbon::parse($input['cli_fnac']);
        $actual = Carbon::now();
        ## reemplazar el punto o la coma por valor vacio al campo cli_ci
        $cedula = str_replace([",", "."], "", $input['cli_ci']);

        $this->validate($request, [
            'cli_ci'     => 'required|numeric',
            'cli_nombre' => 'required|string',
            'cli_apellido' => 'required|string',
            'cli_fnac'   => 'required|date',
            'cli_sexo'   => 'required|alpha',
        ], [
            'cli_ci.required' => 'El campo CI es requerido.',
            'cli_ci.numeric' => 'El campo CI solo puede contener números.',
            'cli_nombre.required' => 'El campo nombre es requerido.',
            'cli_nombre.alpha' => 'El campo nombre solo puede contener letras.',
            'cli_apellido.required' => 'El campo apellido es requerido.',
            'cli_apellido.alpha' => 'El campo apellido solo puede contener letras.',
            'cli_fnac.required' => 'El campo fecha de nacimiento es requerido.',
            'cli_fnac.date' => 'El campo fecha de nacimiento debe ser una fecha válida.',
            'cli_sexo.required' => 'El campo sexo es requerido.',
            'cli_sexo.alpha' => 'El campo sexo solo puede contener letras.',
        ]);

        ##validar edad del cliente
        $edad = $actual->diffInYears($fecha);
        if ($edad < 18) {
            Flash::error('El cliente debe ser mayor de 18 años.');
            return redirect(route('clientes.create'))->withInput();
        }

        ##validar cantidad de digitos del campo ci
        $ci = strlen($input['cli_ci']);
        if ($ci > 8) {##mayor a 8 caracteres
            Flash::error('El nro de cedula solo podra contener 8 digítos.');
            return redirect(route('clientes.create'))->withInput();
        }


        $cliente = DB::table('clientes')->where('cli_ci', $input['cli_ci'])->first();

        if (!empty($cliente)) {
            Flash::error('El cliente con el CI ingresado ya existe!');
            return redirect(route('clientes.create'))->withInput();
        }

        ##insert cliente
        DB::table('clientes')->insert(
            [
                'cli_ci' => $cedula,
                'cli_nombre' => strtoupper($input['cli_nombre']),
                'cli_apellido' => strtoupper($input['cli_apellido']),
                'cli_fnac' => $input['cli_fnac'],
                'cli_sexo' => $input['cli_sexo'],
                'cli_direccion' => strtoupper($input['cli_direccion']),
                'id_departamento' => $input['id_departamento'],
                'id_ciudad' => $input['id_ciudad'],
            ]
        );

        Flash::success("Cliente creado con exito..!");
        return redirect(route('clientes.index'));
    }

    public function edit($id)
    {
        $cliente = DB::table('clientes')->where('id_cliente', $id)->first();

        if (empty($cliente)) {
            Flash::error('El cliente no existe!');
            return redirect(route('clientes.index'))->withInput();
        }

        ## Cargar ciudad y departamento en blade create utilizando pluck
        $ciudades = DB::table('ciudad')->pluck('ciu_descripcion', 'id_ciudad');

        $departamentos = DB::table('departamento')->pluck('dep_descripcion', 'id_departamento');

        # Cargar array de sexo que serian datos
        $genero = array("M" => "MASCULINO", "F" => "FEMENINO", "O" => "OTROS");
        # Retornar a la vista las variables ciudades y deparmentos
        return view('clientes.edit')
            ->with('cliente', $cliente)
            ->with('ciudad', $ciudades)
            ->with('departamento', $departamentos)
            ->with('genero', $genero);
    }


    public function update($id, Request $request)
    {
        $this->validate($request, [
            'cli_ci'     => 'required|numeric',
            'cli_nombre' => 'required|string',
            'cli_apellido' => 'required|string',
            'cli_fnac'   => 'required|date',
            'cli_sexo'   => 'required|alpha',
        ], [
            'cli_ci.required' => 'El campo CI es requerido.',
            'cli_ci.numeric' => 'El campo CI solo puede contener números.',
            'cli_nombre.required' => 'El campo nombre es requerido.',
            'cli_nombre.alpha' => 'El campo nombre solo puede contener letras.',
            'cli_apellido.required' => 'El campo apellido es requerido.',
            'cli_apellido.alpha' => 'El campo apellido solo puede contener letras.',
            'cli_fnac.required' => 'El campo fecha de nacimiento es requerido.',
            'cli_fnac.date' => 'El campo fecha de nacimiento debe ser una fecha válida.',
            'cli_sexo.required' => 'El campo sexo es requerido.',
            'cli_sexo.alpha' => 'El campo sexo solo puede contener letras.',
        ]);

        $input = $request->all();
        $fecha = Carbon::parse($input['cli_fnac']);
        $actual = Carbon::now();

        ##validar edad del cleinte
        $edad = $actual->diffInYears($fecha);
        if ($edad < 18) {
            Flash::error('El cliente debe ser mayor de 18 años.');
            return redirect(route('clientes.edit', [$id]))->withInput();
        }

        ##validar cantidad de digitos del campo ci
        $ci = strlen($input['cli_ci']);
        if ($ci > 8) {
            Flash::error('El nro de cedula solo podra contener 8 digítos.');
            return redirect(route('clientes.edit', [$id]))->withInput();
        }


        $cliente = DB::table('clientes')->where('cli_ci', $input['cli_ci'])
        ->whereNotIn('id_cliente', [$id])
        ->first();

        if (!empty($cliente)) {
            Flash::error('El cliente con el CI ingresado ya existe!');
            return redirect(route('clientes.edit', [$id]))->withInput();
        }
        ## Update de clientes
        DB::table('clientes')->where('id_cliente', $id)
            ->update([
                'cli_ci' => $input['cli_ci'],
                'cli_nombre' => strtoupper($input['cli_nombre']),
                'cli_apellido' => strtoupper($input['cli_apellido']),
                'cli_fnac' => $input['cli_fnac'],
                'cli_sexo' => $input['cli_sexo'],
                'cli_direccion' => strtoupper($input['cli_direccion']),
                'id_departamento' => $input['id_departamento'],
                'id_ciudad' => $input['id_ciudad'],
            ]);

        Flash::success("Cliente actualizado con exito..!");
        return redirect(route('clientes.index'));
    }

    public function destroy($id)
    {
        $cliente = DB::table('clientes')->where('id_cliente', $id)->first();

        if (empty($cliente)) {
            Flash::error('El cliente no existe!');
            return redirect(route('clientes.index'))->withInput();
        }

        # 1 forma
        DB::table('clientes')->where('id_cliente', $id)->delete();

        #DB::delete('delete from clientes where id_cliente = ?', [$id]);


        Flash::success("Cliente eliminado con exito..!");
        return redirect(route('clientes.index'));
    }
}