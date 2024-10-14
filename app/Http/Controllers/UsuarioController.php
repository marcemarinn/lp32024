<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Flash;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        #dd($request->ajax());
        # Recibir campo buscar
        $buscar = $request->get('buscar');
        # Consulta a la tabla de usuario
        $usuario = DB::table('users')
        ->select(
            'users.*',
            'roles.name as rol'
        )
        ->leftJoin('roles', 'roles.id', 'users.role_id');

        // si la varibale buscar no esta vacio procedo con
        // la consulta utilizando ilike
        if (!empty($buscar)) {
            $usuario = $usuario->whereRaw("(users.email iLIKE '%{$buscar}%'
                or users.ci iLIKE '%{$buscar}%'
                or users.name iLIKE '%{$buscar}%'
                or roles.name iLIKE '%{$buscar}%')");
        }
        # Paginado de  consulta general
        $usuario = $usuario->paginate(10);

        ##si la accion es buscardor entonces significa que se debe recargar mediante ajax la tabla
        if ($request->ajax()) {
            //solo llmamamos a table.blade.php y mediante compact pasamos la variable users
            return view('usuarios.table')->with('usuarios', $usuario);
        }

        return view('usuarios.index')->with('usuarios', $usuario);
    }

    public function create()
    {
        $estado = ["ACTIVO" => "ACTIVO", "INACTIVO" => "INACTIVO"];

        ## Consultar roles
        $roles = DB::table('roles')->pluck('name', 'id');

        # Consultar sucursal
        $sucursal = DB::table('sucursal')->pluck('suc_descri', 'cod_suc');

        return view('usuarios.create')
            ->with('estado', $estado)
            ->with('sucursal', $sucursal)
            ->with('roles', $roles);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make(
            $input,
            [
                'name' => 'required',
                'email' => 'required',
                'ci' => 'required|numeric',
                'password' => 'required',
            ],
            [
                'name.required' => 'El nombre es requerido',
                'email.required' => 'El email es requerido',
                'ci.required' => 'El número de cedula es requerido',
                'ci.numeric' => 'El número de cedula debe ser un número',
                'password.required' => 'La contraseña es requerida',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $validarCi = DB::table('users')->where('ci', $input['ci'])->first();
        if (!empty($validarCi)) {
            alert()->error('Atención', 'Número de Cedula Identidad ya existe.!');

            return redirect(route('usuarios.create'))->withInput();
        }

        $validarUserName = DB::table('users')->where('email', $input['email'])->first();
        if (!empty($validarUserName)) {
            alert()->error('Atención', 'El nombre de usuario ya existe.!');

            return redirect(route('usuarios.create'))->withInput();
        }

        $input['direccion'] = !empty($input['direccion']) ? strtoupper($input['direccion']) : null;

        /*DB::insert(
            "INSERT INTO users(name, email, password, ci, direccion, telefono, estado, role_id)
            VALUES(?, ?, ?, ?, ?, ?, ?, ?)",
            [
                strtoupper($input['name']),
                $input['email'],
                Hash::make($input['password']),
                $input['ci'],
                $input['direccion'],
                $input['telefono'],
                $input['estado'],
                $input['role_id']
            ]
        );*/
        # Insercion de usuarios utilizando un modelo
        $user = new User;
        $user->name = strtoupper($input['name']);
        $user->email = $input['email'];
        $user->password = Hash::make($input['password']);
        $user->ci = $input['ci'];
        $user->direccion = $input['direccion'];
        $user->telefono = $input['telefono'];
        $user->estado = $input['estado'];
        $user->role_id = $input['role_id'];
        $user->cod_suc = $input['cod_suc'];
        $user->save();

        ##capturamos el dato de role_id
        $roles = $input['role_id'];

        ##guardamos los permisos para el usuario creado,
        #y se asgina los permisos en la tabla model_has_roles
        $user->roles()->sync([$roles]);


        alert()->success('Registro creado correctamente.!');

        return redirect(route('usuarios.index'));
    }

    public function edit($id)
    {
        $usuario = DB::table('users')->where('id', $id)->first();

        if (empty($usuario)) {
            Flash::error("El registro consultado no existe..");

            return redirect(route('usuarios.index'));
        }

        ## Consultar roles
        $roles = DB::table('roles')->pluck('name', 'id');

        # Consultar sucursal
        $sucursal = DB::table('sucursal')->pluck('suc_descri', 'cod_suc');

        $estado = ["ACTIVO" => "ACTIVO", "INACTIVO" => "INACTIVO"];

        return view('usuarios.edit')
            ->with('usuario', $usuario)
            ->with('roles', $roles)
            ->with('sucursal', $sucursal)
            ->with('estado', $estado);
    }

    public function update(Request $request, $id)
    {
        $usuario = User::where('id', $id)->first();

        if (empty($usuario)) {
            alert()->error("Atención", "El registro consultado no existe..");

            return redirect(route('usuarios.index'));
        }

        $input = $request->all();

        $validator = Validator::make(
            $input,
            [
                'name' => 'required',
                'email' => 'required',
                'ci' => 'required|numeric',
            ],
            [
                'name.required' => 'El nombre es requerido',
                'email.required' => 'El email es requerido',
                'ci.required' => 'El número de cedula es requerido',
                'ci.numeric' => 'El número de cedula debe ser un número',
            ]
        );

        if ($validator->fails()) {
            alert()->error("Error", "Verificar Datos..!");
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validarCi = DB::table('users')->where('ci', $input['ci'])
            ->whereNotIn('id', [$id])
            ->first();

        if (!empty($validarCi)) {
            alert()->error('Atención', 'Número de Cedula Identidad ya existe.!');

            return redirect(route('usuarios.edit', [$id]))->withInput();
        }

        $validarUserName = DB::table('users')->where('email', $input['email'])
            ->whereNotIn('id', [$id])
            ->first();

        if (!empty($validarUserName)) {
            alert()->error('Atención', 'El nombre de usuario ya existe.!');

            return redirect(route('usuarios.edit'[$id],))->withInput();
        }

        // Verificar si la contraseña ha cambiado
        if (!empty($input['password']) && !Hash::check($input['password'], $usuario->password)) {
            // Si la contraseña ha cambiado, hashearla
            $input['password'] = Hash::make($input['password']);
        } else {
            // Si no ha cambiado, mantener la contraseña actual
            unset($input['password']);
        }
        $input['direccion'] = !empty($input['direccion']) ? strtoupper($input['direccion']) : null;
        /*DB::update(
            'update users set
                name =?,
                email =?,
                password =?,
                ci =?,
                direccion =?,
                telefono =?,
                estado = ?,
                role_id = ?
                where id =?',
            [
                strtoupper($input['name']),
                $input['email'],
                $input['password'] ?? $usuario->password, ##validacion ternaria
                $input['ci'],
                strtoupper($input['direccion']),
                $input['telefono'],
                $input['estado'],
                $input['role_id'],
                $id
            ]
        );*/
        $usuario->name = strtoupper($input['name']);
        $usuario->email = $input['email'];
        if(isset($input['password'])){# verificar que exista el array password
            $usuario->password = $input['password'];
        }
        $usuario->ci = $input['ci'];
        $usuario->direccion = $input['direccion'];
        $usuario->telefono = $input['telefono'];
        $usuario->estado = $input['estado'];
        $usuario->role_id = $input['role_id'];
        $usuario->cod_suc = $input['cod_suc'];
        $usuario->save();

        $roles = $input['role_id']; ##capturamos el dato de role_id

        ##guardamos los permisos para el usuario creado, y se asgina los permisos en la tabla model_has_roles
        $usuario->roles()->sync([$roles]);


        alert()->success('Exito', 'Registro actualizado correctamente.!');

        return redirect(route('usuarios.index'));
    }

    public function destroy($id)
    {
        $usuario = DB::table('users')->where('id', $id)->first();

        if (empty($usuario)) {
            alert()->error("Error","El registro consultado no existe..");

            return redirect(route('usuarios.index'));
        }

        ## UPDATE DE ESTADO DE USUARIO
        DB::update('update users set estado = ? where id = ?', ['INACTIVO', $id]);

        alert()->success("Atención", "El registro se ha desactivado correctamente..");


        return redirect(route('usuarios.index'));
    }

    public function perfil()
    {
        return view('usuarios.profile');
    }

    public function cambiarPassword(Request $request)
    {
        $input = $request->all();

        ##validar datos de contraseña utilizando validate de laravel
        $validator = Validator::make(
            $request->all(),
            [
                'password' => 'required|min:6',
                'confirm-password' => 'required|same:password',
            ],
            [
                'password.required'         => 'La contraseña es requerida',
                'password.min'              => 'Debe tener al menos 6 digítos la contraseña',
                'confirm-password.required' => 'La confirmación de contraseña es requerida',
                'confirm-password.same'     => 'Las contraseñas no coinciden',
            ]
        );

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        #Actualizar la contraseña del usuario en session
        DB::table('users')->where('id', auth()->user()->id)
            ->update(['password' =>  Hash::make($input['password'])]);

        alert()->success('Exíto', 'Contraseña actualizada correctamente.!');

        return redirect()->back();
    }
}