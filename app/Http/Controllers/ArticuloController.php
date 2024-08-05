<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Flash;
use Illuminate\Support\Facades\DB;

class ArticuloController extends Controller
{
    public function index()
    {
        $articulo = DB::table('articulos')->select(
            'articulos.*',
            'marcas.mar_descrip'
        )
            ->join('marcas', 'marcas.mar_cod', 'articulos.mar_cod')
            ->orderBy('articulos.id_articulo')
            ->paginate(10);

        return view('articulos.index')->with('articulos', $articulo);
    }

    public function create()
    {
        ## Consulta para llenar el select de marcas
        $marcas = DB::table('marcas')->pluck('mar_descrip', 'mar_cod');

        ## Armar array de ivas
        $iva = ['0' => 'Exenta', '5' => 'Gravada 5%', '10' => 'Gravada 10%'];

        return view('articulos.create')
            ->with('marcas', $marcas)
            ->with('iva', $iva);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        ## Definir Path para grabar mi archivo recibido
        $newPath = public_path() . "/img/articulos/";

        ##validar que el archivo sea una imagen
        $validator = Validator::make(
            $input,
            [
                'art_imagen' => 'mimes:jpeg,png,jpg,bmp|max:2048',
                'art_descripcion' => 'required',
                'art_precio' => 'required',
                'art_iva' => 'required|numeric',
                'mar_cod' => 'required'
            ],
            [
                'art_imagen.mimes' => 'El archivo debe ser una imagen de tipo: jpeg, png, jpg, bmp.',
                'art_imagen.max' => 'El archivo excede el tamaño máximo permitido de 2MB.',
                'art_descripcion.required' => 'La descripción del articulo es requerido',
                'art_precio.required' => 'El precio del articulo es requerido',
                'art_iva.required' => 'El iva del articulo es requerido',
                'art_iva.numeric' => 'El iva del articulo debe ser un número',
                'mar_cod.required' => 'La marca del articulo es requerida'
            ]
        );

        ##imprimir el error
        if ($validator->fails()) {
            Flash::error("Error al subir la imagen. Verifica los requisitos.");
            return back()->withErrors($validator)->withInput();
        }

        ##validar con hasFile que exista eese dato
        if ($request->hasFile('art_imagen')) {
            ##obtener nombre del archivo de imagen con getClientOriginalName
            $imagen = $request->file('art_imagen')->getClientOriginalName();

            ##mover el archivo imagen al path defenido
            $request->file('art_imagen')->move($newPath, $imagen);
        }
        ##sobre escribir el atributo art_imagen de la variable $input
        ##validar con isset() para verificar que exista la variable $imagen
        $input['art_imagen'] = isset($imagen) ? $imagen : null;

        ##quitar separador de miles a precio antes de grabar
        $input['art_precio'] = str_replace('.', '', $input['art_precio']);

        DB::insert(
            "INSERT INTO articulos(mar_cod, art_descripcion, art_precio, art_imagen, art_iva)
                VALUES(?, ?, ?, ?, ?)",
            [
                $input['mar_cod'],
                $input['art_descripcion'],
                $input['art_precio'],
                $input['art_imagen'],
                $input['art_iva']
            ]
        );

        Flash::success('Registro guardado correctamente.!');

        return redirect(route('articulos.index'));
    }


    public function edit($id)
    {
        $articulos = DB::table('articulos')->where('id_articulo', $id)->first();

        ##validad
        if (empty($articulos)) {
            Flash::error('Registro no encontrado.!');

            return redirect(route('articulos.index'));
        }

        $marcas = DB::table('marcas')->pluck('mar_descrip', 'mar_cod');
        $iva = ['0' => 'Exenta', '5' => 'Gravada 5%', '10' => 'Gravada 10%'];

        return view('articulos.edit')
            ->with('articulo', $articulos)
            ->with('marcas', $marcas)
            ->with('iva', $iva);
    }

    public function update($id, Request $request)
    {
        $articulos = DB::table('articulos')->where('id_articulo', $id)->first();
        $newPath   = public_path() . "/img/articulos/";

        ##valido
        if (empty($articulos)) {
            Flash::error('Registro no encontrado.!');

            return redirect(route('articulos.index'));
        }

        $input = $request->all();

       ##validar que el archivo sea una imagen
        $validator = Validator::make(
            $input,
            [
                'art_imagen' => 'mimes:jpeg,png,jpg,bmp|max:2048',
                'art_descripcion' => 'required',
                'art_precio' => 'required',
                'art_iva' => 'required|numeric',
                'mar_cod' => 'required'
            ],
            [
                'art_imagen.mimes' => 'El archivo debe ser una imagen de tipo: jpeg, png, jpg, bmp.',
                'art_imagen.max' => 'El archivo excede el tamaño máximo permitido de 2MB.',
                'art_descripcion.required' => 'La descripción del articulo es requerido',
                'art_precio.required' => 'El precio del articulo es requerido',
                'art_iva.required' => 'El iva del articulo es requerido',
                'art_iva.numeric' => 'El iva del articulo debe ser un número',
                'mar_cod.required' => 'La marca del articulo es requerida'
            ]
        );

        ##imprimir el error
        if ($validator->fails()) {
            Flash::error("Error al subir la imagen. Verifica los requisitos.");
            return back()->withErrors($validator)->withInput();
        }


        ##validar con hasFile que exista eese dato
        if ($request->hasFile('art_imagen')) {
            ##obtener nombre del archivo de imagen con getClientOriginalName
            $imagen = $request->file('art_imagen')->getClientOriginalName();

            ##mover el archivo imagen al path defenido
            $request->file('art_imagen')->move($newPath, $imagen);
        }
        $input['art_imagen'] = isset($imagen) ? $imagen : $articulos->art_imagen;
        ##quitar separador de miles a precio antes de grabar
        $input['art_precio'] = str_replace(".", "", $input['art_precio']);

        DB::update(
            'UPDATE articulos SET
            mar_cod = ?,
            art_descripcion = ?,
            art_precio = ?,
            art_imagen = ?,
            art_iva = ?
        WHERE id_articulo = ?',
            [
                $input['mar_cod'],
                $input['art_descripcion'],
                $input['art_precio'],
                $input['art_imagen'],
                $input['art_iva'],
                $id
            ]
        );

        Flash::success('Registro actualizado correctamente.!');

        return redirect(route('articulos.index'));
    }

    public function destroy($id)
    {
        $articulos = DB::table('articulos')->where('id_articulo', $id)->first();

        ##validad
        if (empty($articulos)) {
            Flash::error('Error', 'Registro no encontrado.!');

            return redirect(route('articulos.index'));
        }

        DB::delete('DELETE FROM articulos where id_articulo = ?', [$id]);

        Flash::success('Exíto', 'Registro borrado.!');

        return redirect(route('articulos.index'));
    }
}