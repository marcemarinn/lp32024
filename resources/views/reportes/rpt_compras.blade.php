@extends('layouts.app')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Reporte de Compras</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        <div class="card">
            <div class="card-body p-3">
                <div class="row">              
                    <div class="form-group col-sm-3">
                        {!! Form::label('proveedor', 'Proveedor:') !!}
                        {!! Form::select('proveedor', $proveedores, request()->get('proveedor', null), [
                            'class' => 'form-control',
                            'placeholder' => 'Seleccione',
                            'id' => 'proveedor',
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
                        <button class="btn btn-success" type="button" title="Generar Reporte" id="btn-consultar" style="margin-top:30px">
                            <i class="fas fa fa-search"></i> Buscar
                        </button>
                        <button class="btn btn-primary" id="btn-exportar" type="button" title="Exportar" style="margin-top:30px">
                            <i class="fas fa-print"></i> Exportar a PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table" id="compras-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Proveedor</th>
                                <th>Sucursal</th>
                                <th>Condición</th>
                                <th>Total</th>
                                <th>Cuotas</th>
                                <th>Plazo</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                           @if($compras->isEmpty())
    <tr>
        <td colspan="10" class="text-center">No se encontraron registros</td>
    </tr>
@else
    @foreach ($compras as $compra)
        <tr>
            <td>{{ $compra->compra_id }}</td>
            <td>{{ \Carbon\Carbon::parse($compra->com_fecha)->format('d/m/Y') }}</td>
            <td>{{ $compra->prov_nombre }}</td>
            <td>{{ $compra->suc_descri }}</td>
            <td>{{ $compra->com_condicion }}</td>
            <td>{{ number_format($compra->com_total, 0, ',', '.') }}</td>
            {{-- <td>{{ number_format($compra->com_total, 2) }}</td> --}}
            <td>{{ $compra->com_cant_cuo }}</td>
            <td>{{ $compra->com_plazo }}</td>
            <td>{{ $compra->com_estado }}</td>
            <td>{{ $compra->name }}</td>
        </tr>
    @endforeach
@endif
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
                window.location.href = '{{ url('reportes/rpt_compras') }}?proveedor=' + $("#proveedor").val() +
                    '&desde=' + $("#desde").val() + '&hasta=' + $("#hasta").val();
            });

            $("#btn-exportar").click(function(e) {
                e.preventDefault();
                window.open('{{ url('reportes/rpt_compras') }}?proveedor=' + $("#proveedor").val() +
                    '&desde=' + $("#desde").val() + '&hasta=' + $("#hasta").val() + '&exportar=pdf', '_blank');
            });
        });
    </script>
@endpush
