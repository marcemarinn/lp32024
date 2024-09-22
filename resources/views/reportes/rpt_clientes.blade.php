@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Reportes Clientes</h1>
                </div>

            </div>
        </div>
    </section>

    <div class="content px-3">

        <div class="card">
            <div class="card-body p-3">

                <h3>Filtros</h3>
                <div class="row">
                    <div class="form-group col-sm-6">
                        {!! Form::label('ciudad', 'Ciudad:') !!}
                        {!! Form::select('ciudad', $ciudad, request()->get('ciudad', null), [
                            'class' => 'form-control',
                            'placeholder' => 'Seleccione',
                            'id' => 'ciudad',
                        ]) !!}
                    </div>

                    <div class="form-group col-sm-6">
                        <button class="btn btn-success" type="button" title="Generar Reporte" 
                            id="btn-consultar"
                            style="margin-top:30px">
                            <i class="fas fa fa-search"></i> Buscar
                        </button>
                        <button class="btn btn-primary" id="btn-exportar" type="button" 
                            data-toggle="tooltip"
                            title="Exportar" style="margin-top:30px">
                            <i class="fas fa-print"></i> Exportar a PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table" id="clientes-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nro Documento</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Sexo</th>
                                <th>Fecha Nac</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th>Departamento</th>
                                <th>Ciudad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                <tr>
                                    <td>{{ $cliente->id_cliente }}</td>
                                    <td>{{ $cliente->cli_ci }}</td>
                                    <td>{{ $cliente->cli_nombre }}</td>
                                    <td>{{ $cliente->cli_apellido }}</td>
                                    <td>{{ ($cliente->cli_sexo == 'M' ? 'MASCULINO' : 'FEMENINO') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($cliente->cli_fnac)->format('d/m/Y') }}</td>
                                    <td>{{ $cliente->cli_direccion }}</td>
                                    <td>{{ $cliente->cli_telefono }}</td>
                                    <td>{{ $cliente->dep_descripcion }}</td>
                                    <td>{{ $cliente->ciu_descripcion }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#btn-consultar").click(function(e) {
                e.preventDefault();
                //alert('hola');
                window.location.href = '{{ url('reportes/rpt_clientes') }}?ciudad=' + $("#ciudad").val();
            });

            $("#btn-exportar").click(function(e) {
                e.preventDefault();
                window.open('{{ url('reportes/rpt_clientes') }}?ciudad=' + $("#ciudad").val() +
                    '&exportar=pdf', '_blank');
            });
        })
    </script>
@endpush
