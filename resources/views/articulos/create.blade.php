@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>
                    Crear Articulos
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::open(['route' => 'articulos.store', 'files' => true]) !!}

            <div class="card-body">
                @include('flash::message')

                <div class="row">
                    @include('articulos.fields')
                </div>

            </div>

            <form action="{{ route('articulos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Otros campos del formulario -->
                <div class="form-group">
                    <label for="art_imagen">Imagen:</label>
                    <input type="file" name="art_imagen" class="form-control">
                </div>            

            <div class="card-footer">
                {!! Form::submit('Grabar', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('articulos.index') }}" class="btn btn-default"> Cancelar </a>
            </div>
        </form>

            {!! Form::close() !!}

        </div>
    </div>
    @endsection


