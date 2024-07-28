@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Detalle del Cliente</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <strong>CI:</strong>
                        <p>{{ $cliente->cli_ci }}</p>
                    </div>
                    <div class="col-sm-6">
                        <strong>Nombre:</strong>
                        <p>{{ $cliente->cli_nombre }}</p>
                    </div>
                    <div class="col-sm-6">
                        <strong>Apellido:</strong>
                        <p>{{ $cliente->cli_apellido }}</p>
                    </div>
                    <div class="col-sm-6">
                        <strong>Sexo:</strong>
                        <p>{{ $cliente->cli_sexo }}</p>
                    </div>
                    <div class="col-sm-6">
                        <strong>Fecha de Nacimiento:</strong>
                        <p>{{ $cliente->cli_fnac }}</p>
                    </div>
                    <div class="col-sm-6">
                        <strong>Dirección:</strong>
                        <p>{{ $cliente->cli_direccion }}</p>
                    </div>
                    <div class="col-sm-6">
                        <strong>Teléfono:</strong>
                        <p>{{ $cliente->cli_telefono }}</p>
                    </div>
                    <div class="col-sm-6">
                        <strong>Ciudad:</strong>
                        <p>{{ $ciudades[$cliente->id_ciudad] }}</p>
                    </div>
                    <div class="col-sm-6">
                        <strong>Departamento:</strong>
                        <p>{{ $departamentos[$cliente->id_departamento] }}</p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('clientes.index') }}" class="btn btn-default">Volver</a>
            </div>
        </div>
    </div>
@endsection
