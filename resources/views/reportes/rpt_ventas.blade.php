@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Reportes Ventas</h1>
                </div>

            </div>
        </div>
    </section>

    <div class="content px-3">

        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="form-group col-sm-3">
                        {!! Form::label('cliente', 'Clientes:') !!}
                        {!! Form::select('cliente', $clientes, request()->get('clientes', null), [
                            'class' => 'form-control',
                            'placeholder' => 'Seleccione',
                            'id' => 'clientes',
                        ]) !!}
                    </div>

                    <div class="form-group col-sm-3">
                        {!! Form::label('desde', 'Desde:') !!}
                        {!! Form::date('desde', request()->get('desde', null), ['class' => 'form-control', 'id' => 'desde']) !!}
                    </div>
                    <div class="form-group col-sm-3">
                        {!! Form::label('hasta', 'Hasta:') !!}
                        {!! Form::date('hasta', request()->get('hasta', null), ['class' => 'form-control', 'id' => 'hasta']) !!}
                    </div>

                    <div class="form-group col-sm-3">
                        <button class="btn btn-success" type="button" title="Generar Reporte" id="btn-consultar"
                            style="margin-top:30px">
                            <i class="fas fa fa-search"></i> Buscar
                        </button>
                        <button class="btn btn-primary" id="btn-exportar" type="button" data-toggle="tooltip"
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
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Condición</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Nro Factura</th>
                                <th>Sucursal</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ventas as $venta)
                                <tr>
                                    <td>{{ $venta->id_venta }}</td>
                                    <td>{{ $venta->cli_nombre . ' ' . $venta->cli_apellido }}</td>
                                    <td>{{ \Carbon\Carbon::parse($venta->ven_fecha)->format('d/m/Y') }}</td>
                                    <td>{{ $venta->ven_condicion }}</td>
                                    <td>{{ number_format($venta->ven_total, 0, ',', '.') }}</td>
                                    <td>{{ $venta->ven_estado }}</td>
                                    <td>{{ $venta->nro_factura }}</td>
                                    <td>{{ $venta->suc_descri }}</td>
                                    <td>{{ $venta->name }}</td>
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
                window.location.href = '{{ url('reportes/rpt_ventas') }}?clientes=' + $("#clientes").val()+'&desde='+$('#desde').val()+
                '&hasta='+$('#hasta').val();
            });

            $("#btn-exportar").click(function(e) {
                e.preventDefault();
                window.open('{{ url('reportes/rpt_ventas') }}?clientes=' + $("#clientes").val()+'&desde='+$('#desde').val()+
                '&hasta='+$('#hasta').val()+'&exportar=pdf', '_blank');
            });
        })
    </script>
@endpush
