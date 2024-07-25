<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ClienteController extends Controller
{
    public function index()
    {
        $clientes  = DB :: table ('clientes')
        ->select(
            'cli.*',
            'c.ciu_descripcion',
            'd.dep_descripcion'
        )
        ->join('ciudad as c', 'c.id_ciudad', '=', 'cli.id_ciudad')
        ->leftJoin('departamento as d', 'd.id_departamento', '=', 'cli.id_ciudad')
        ->orderBy('cli.id_cliente', 'desc')
        ->get();
        return view('clientes.index')->with('clientes', $clientes);

    }

    public function create()
    {

    }

    public function store()
    {

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
