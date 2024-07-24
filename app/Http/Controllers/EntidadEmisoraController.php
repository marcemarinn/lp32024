<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class EntidadEmisoraController extends Controller
{
    public function index(){

        
        $entidadEmisoras = DB :: table('entidad_emisora') -> get();
        if(!empty($entidadEmisoras)){
        //return view (('ciudads.index'),Compact('ciudades'));
        return view ('entidad_emisoras.index')-> with ('entidadEmisoras',$entidadEmisoras);

    }
}

public function create(){

    return view('entidad_emisoras.create');

    
}
public function store(){
    
}

public function put(){
    
}

public function delete(){
    
}

public function get(){
    
}


}
