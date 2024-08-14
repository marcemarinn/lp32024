<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UsuarioController extends Controller
{
   
public function index ()
{
    $usuario = DB :: table('users') -> paginate (10);

    return view ('usuarios.index')->with('usuarios',$usuario);
}

public function create () 
{
    $estado = ["Activo" => "Activo", "Inactivo" => "Inactivo'" ];
    return view ('usuarios.create') -> with ('estado',$estado);
    
}

}
