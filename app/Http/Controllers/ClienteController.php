<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;




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
        ->leftJoin('departamento',
            'departamento.id_departamento', 'clientes.id_departamento')
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

        $this->validate($request, [
            'cli_ci'   => 'required',
            'cli_nombre' => 'required',
            'cli_apellido' => 'required',
            'cli_fnac' => 'required',
            'cli_sexo' => 'required',
        ]);

        ##validar edad del cleinte
        $edad = $actual->diffInYears($fecha);
        if ($edad < 18) {
            Flash::error('El cliente debe ser mayor de 18 años.');
            return redirect(route('clientes.create'));
        }

        ##validar cantidad de digitos del campo ci
        $ci = strlen($input['cli_ci']);
        if($ci > 8){
            Flash::error('El nro de cedula solo podra contener 8 digítos.');
            return redirect(route('clientes.create'));
        }


        $cliente = DB::table('clientes')->where('cli_ci', $input['cli_ci'])->first();

        if (!empty($cliente)) {
            Flash::error('El cliente con el CI ingresado ya existe!');
            return redirect(route('clientes.create'));
        }


        ##insert cliente
        DB::table('clientes')->insert(
            [
                'cli_ci' => $input['cli_ci'],
                'cli_nombre' => strtoupper($input['cli_nombre']),
                'cli_apellido' => strtoupper($input['cli_apellido']),
                'cli_fnac' => $input['cli_fnac'],
                'cli_sexo' => $input['cli_sexo'],
                'cli_direccion' => strtoupper($input['cli_direccion']),
                'id_departamento' => $input['id_departamento'],
                'id_ciudad' => $input['id_ciudad'],
            ]
        );

        Flash::error("Cliente creado con exito..!");
        return redirect(route('clientes.index'));
    }

    public function edit()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }


}
