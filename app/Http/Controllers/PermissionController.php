<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:permissions index')->only('index');
        // $this->middleware('permission:permissions Crear una nueva venta')->only('create', 'store');
        // $this->middleware('permission:permissions edit')->only('edit', 'update');
        // $this->middleware('permission:permissions destroy')->only('destroy');
        // $this->middleware('auth');
    }

    public function index()
    {
        $permisos = DB::table('permissions')->paginate(10);

        ##alerta para borrar confirmDelete
        confirmDelete("Atención", "Desea borrar el permiso?");

        return view('permisos.index')->with('permisos', $permisos);
    }

    public function create()
    {
        return view('permisos.create');
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $input['guard_name'] = "web";

        DB::insert(
            "INSERT INTO permissions(name, guard_name) values(?, ?)",
            [
                $input['name'],
                $input['guard_name']
            ]
        );

        alert()->success("Exíto", "Permiso guardado correctamente.!");

        return redirect(route('permissions.index'));
    }

    public function edit($id)
    {
        $permissions = DB::table('permissions')->where('id', $id)->first();

        if(empty($permissions)){
            alert()->error("Atención", "Registro no encontrado.!");

            return redirect(route('permissions.index'));
        }

        return view('permissions.edit')->with('permissions', $permissions);
    }

    public function update($id, Request $request)
    {
        $permissions = DB::table('permissions')->where('id', $id)->first();
        $input = $request->all();

        if (empty($permissions)) {
            alert()->error("Atención", "Registro no encontrado.!");

            return redirect(route('permissions.index'));
        }

        DB::update("UPDATE permissions SET name = ? Where id = ?",
        [
            $input['name'],
            $id
        ]);

        alert()->success("Exíto", "Permiso actualizado correctamente.");

        return redirect(route('permissions.index'));
    }

    public function destroy($id)
    {
        $permissions = DB::table('permissions')->where('id', $id)->first();

        if (empty($permissions)) {
            alert()->error("Atención", "Registro no encontrado.!");

            return redirect(route('permissions.index'));
        }

        DB::delete("DELETE FROM permissions WHERE id = ?", [$permissions->id]);

        alert()->success("Atención", "Registro borrado.!");

        return redirect(route('permissions.index'));

    }
}