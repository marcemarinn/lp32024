<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
        ->leftJoin('departamento',
            'departamento.id_departamento', 'clientes.id_departamento')
        ->orderBy('id_cliente', 'desc')
        ->paginate(10);

        return view('clientes.index')->with('clientes', $clientes);
    }

    public function create()
    {
        $ciudades = DB::table('ciudad')->pluck('ciu_descripcion', 'id_ciudad');
    $departamentos = DB::table('departamento')->pluck('dep_descripcion', 'id_departamento');
    $genero = array("M" => "MASCULINO", "F" => "FEMENINO", "O" => "OTROS");

    return view('clientes.create')
                ->with('ciudades', $ciudades)
                ->with('departamentos', $departamentos)
                ->with('genero', $genero);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $fecha = Carbon::parse($input['cli_fnac']);
        $actual = Carbon::now();

        $messages = [
            'cli_ci.required' => 'Por favor, ingrese el número de cédula.',
            'cli_nombre.required' => 'El nombre es obligatorio.',
            'cli_apellido.required' => 'El apellido es obligatorio.',
            'cli_fnac.required' => 'La fecha de nacimiento es obligatoria.',
            'cli_sexo.required' => 'Por favor, seleccione el sexo.',
        ];
    
        $this->validate($request, [
            'cli_ci' => 'required',
            'cli_nombre' => 'required',
            'cli_apellido' => 'required',
            'cli_fnac' => 'required',
            'cli_sexo' => 'required',
        ], $messages);
    
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
                'cli_telefono' => $input['cli_telefono'],
            ]
        );

        Flash::success("Cliente creado con exito!");
        return redirect(route('clientes.index'));
    }

    public function edit($id_cliente)
    {
        // Obtener el cliente a editar
        $cliente = DB::table('clientes')->where('id_cliente', $id_cliente)->first();
        
        if (empty($cliente)) {
            Flash::error('Cliente no encontrado');
            return redirect(route('clientes.index'));
        }
    
        // Obtener las ciudades y departamentos para las listas desplegables
        $ciudades = DB::table('ciudad')->pluck('ciu_descripcion', 'id_ciudad');
        $departamentos = DB::table('departamento')->pluck('dep_descripcion', 'id_departamento');
        $genero = ["M" => "MASCULINO", "F" => "FEMENINO", "O" => "OTROS"];
    
        // Retornar la vista de edición con los datos necesarios
        return view('clientes.edit')
            ->with('cliente', $cliente)
            ->with('ciudades', $ciudades)
            ->with('departamentos', $departamentos)
            ->with('genero', $genero);
    }
    
    public function update(Request $request, $id_cliente)
{
    // Validar los datos de entrada
    $this->validate($request, [
        'cli_ci'   => 'required|max:8',
        'cli_nombre' => 'required',
        'cli_apellido' => 'required',
        'cli_fnac' => 'required|date',
        'cli_sexo' => 'required',
    ]);

    $input = $request->all();

    // Verificar si el cliente existe
    $cliente = DB::table('clientes')->where('id_cliente', $id_cliente)->first();
    
    if (empty($cliente)) {
        Flash::error('Cliente no encontrado');
        return redirect(route('clientes.index'));
    }

    // Actualizar los datos del cliente
    DB::table('clientes')->where('id_cliente', $id_cliente)->update([
        'cli_ci' => $input['cli_ci'],
        'cli_nombre' => strtoupper($input['cli_nombre']),
        'cli_apellido' => strtoupper($input['cli_apellido']),
        'cli_fnac' => $input['cli_fnac'],
        'cli_sexo' => $input['cli_sexo'],
        'cli_direccion' => strtoupper($input['cli_direccion']),
        'id_departamento' => $input['id_departamento'],
        'id_ciudad' => $input['id_ciudad'],
        'cli_telefono' => $input['cli_telefono'],

        
    ]);

    Flash::success('Cliente actualizado correctamente.');
    return redirect(route('clientes.index'));
}


public function destroy($id_cliente)
{
    // Verificar si el cliente existe
    $cliente = DB::table('clientes')->where('id_cliente', $id_cliente)->first();

    if (empty($cliente)) {
        Flash::error('Cliente no encontrado');
        return redirect(route('clientes.index'));
    }

    // Eliminar el cliente
    DB::table('clientes')->where('id_cliente', $id_cliente)->delete();

    Flash::success('Cliente eliminado correctamente.');
    return redirect(route('clientes.index'));
}

public function show ($id_cliente) {

      // Obtener el cliente a editar
      $cliente = DB::table('clientes')->where('id_cliente', $id_cliente)->first();
        
      if (empty($cliente)) {
          Flash::error('Cliente no encontrado');
          return redirect(route('clientes.index'));
      }
  
      // Obtener las ciudades y departamentos para las listas desplegables
      $ciudades = DB::table('ciudad')->pluck('ciu_descripcion', 'id_ciudad');
      $departamentos = DB::table('departamento')->pluck('dep_descripcion', 'id_departamento');
      $genero = ["M" => "MASCULINO", "F" => "FEMENINO", "O" => "OTROS"];
  
      // Retornar la vista de edición con los datos necesarios
      return view('clientes.show')
          ->with('cliente', $cliente)
          ->with('ciudades', $ciudades)
          ->with('departamentos', $departamentos)
          ->with('genero', $genero);

}


}
