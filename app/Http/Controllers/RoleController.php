<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('permission:roles index')->only('index');
        // $this->middleware('permission:roles create')->only('create', 'store');
        // $this->middleware('permission:roles edit')->only('edit', 'update');
        // $this->middleware('permission:roles destroy')->only('destroy');
    }

    public function index()
    {
        $roles = Role::paginate(10);

        ##confirmacion para ventana de borrado alert
        confirmDelete("Atención", "Desea borrar el rol?");

        return view('roles.index')->with('roles', $roles);
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();

        return view('roles.create')->with('permissions', $permissions);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $input['guard_name'] = 'web';

        ##capturar lo seleccionado de la tabla permisos
        $permissions = $request->get('permission', []);
        ##insertar roles
        $roleId = DB::table('roles')->insertGetId([
            'name' => $input['name'],
            'guard_name' => $input['guard_name']
        ]);
        // Paso 2: Obtener el modelo del rol y sincronizar los permisos
        $role = Role::find($roleId); // Asumiendo que tu modelo se llama Role
        ##insertar el array de la variable $permissions
        $role->permissions()->sync($permissions);


        alert()->success("Exíto", "Roles guardado correctamente");

        return redirect(route('roles.index'));
    }

    public function edit($id)
    {
        $roles = Role::find($id);

        if (empty($roles)) {
            alert()->error('Atención', 'Registro no encontrado');

            return redirect(route('roles.index'));
        }

        $permissions = Permission::orderBy('name')->get();

        return view('roles.edit')->with('role', $roles)
        ->with('permissions', $permissions);
    }

    public function update($id, Request $request)
    {
        $roles = DB::table('roles')->where('id', $id)->first();
        $input = $request->all();

        if (empty($roles)) {
            alert()->error('Atención', 'Registro no encontrado');

            return redirect(route('roles.index'));
        }

        ##capturar lo seleccionado de la tabla permisos
        $permissions = $request->get('permission', []);
        ##update roles
        DB::table('roles')->where('id', $id)->update([
            'name' => $input['name'],
        ]);
        // Paso 2: Obtener el modelo del rol y sincronizar los permisos
        $role = Role::find($id); // Role modelo de la libreria Spatie
        ##insertar el array de la variable $permissions
        $role->permissions()->sync($permissions);

        alert()->success("Exíto", "Roles actualizado correctamente");

        return redirect(route('roles.index'));
    }

    public function destroy($id)
    {
        $roles = DB::table('roles')->where('id', $id)->first();

        if (empty($roles)) {
            alert()->error('Atención', 'Registro no encontrado');

            return redirect(route('roles.index'));
        }

        DB::delete("DELETE FROM roles where id = ?", [$id]);

        alert()->success("Exíto", "Roles borrado.!");
        return redirect(route('roles.index'));
    }
}