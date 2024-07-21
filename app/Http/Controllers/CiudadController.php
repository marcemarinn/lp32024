<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CiudadController extends Controller
{
    public function index(){

        
            $ciudades = DB :: table('ciudad') -> get();
            if(!empty($ciudades)){
            //return view (('ciudads.index'),Compact('ciudades'));
            return view ('ciudads.index')-> with ('ciudad',$ciudades);



        }
    }

    
    public function create(Request $request){

        $input = $request->all();
        $this -> validate
                ($request, 
                ['ciu_descripcion' => 'required']);
        
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
