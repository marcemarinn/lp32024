@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1>
                    Editar Ciudad
                </h1>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">

    @include('adminlte-templates::common.errors')

    <div class="card">

        {!! Form::model($ciudades,
        ['route' => ['ciudades.update', $ciudades->id_ciudad],
        'method' => 'patch']) !!}

        <div class="card-body">
            @include('flash::message')

            <div class="row">
                @include('ciudads.fields')
            </div>
        </div>

        <div class="card-footer">
            {!! Form::submit('Grabar', ['class' => 'btn btn-primary']) !!}
            <a href="{{ route('ciudades.index') }}" class="btn btn-default"> Cancelar </a>
        </div>

        {!! Form::close() !!}

    </div>
</div>

@endsection
