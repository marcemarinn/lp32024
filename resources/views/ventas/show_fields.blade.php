<!-- Ven Fecha Field -->
<div class="col-sm-4">
    {!! Form::label('ven_fecha', 'Fecha Venta:') !!}
    <p>{{ \Carbon\Carbon::parse($ventas->ven_fecha)->format('d/m/Y') }}</p>
</div>

<!-- Nro Factura Field -->
<div class="col-sm-4">
    {!! Form::label('nro_factura', 'Nro Factura:') !!}
    <p>{{ $ventas->nro_factura }}</p>
</div>

<!-- Usu Cod Field -->
<div class="col-sm-4">
    {!! Form::label('usu_cod', 'Vendedor:') !!}
    <p>{{ $ventas->vendedor }}</p>
</div>

<!-- Id Cliente Field -->
<div class="col-sm-4">
    {!! Form::label('id_cliente', 'Cliente:') !!}
    <p>{{ $ventas->cli_nombre . ' '. $ventas->cli_apellido }}</p>
</div>

<!-- Cod Suc Field -->
<div class="col-sm-4">
    {!! Form::label('cod_suc', 'Sucursal:') !!}
    <p>{{ $ventas->sucursal }}</p>
</div>

<!-- Ven Condicion Field -->
<div class="col-sm-4">
    {!! Form::label('ven_condicion', 'Condición:') !!}
    <p>{{ $ventas->ven_condicion }}</p>
</div>

<!-- Ven Estado Field -->
<div class="col-sm-4">
    {!! Form::label('ven_estado', 'Estado:') !!}
    <p>{{ $ventas->ven_estado }}</p>
</div>

<!-- Cant Cuo Field -->
<div class="col-sm-4">
    {!! Form::label('cant_cuo', 'Cantidad Cuota:') !!}
    <p>{{ $ventas->cant_cuo }}</p>
</div>


<!-- Intervalo Field -->
<div class="col-sm-4">
    {!! Form::label('intervalo', 'Intervalo:') !!}
    <p>{{ $ventas->intervalo }}</p>
</div>

<!-- Ven Total Field -->
<div class="col-sm-4">
    {!! Form::label('ven_total', 'Total Factura:') !!}
    <p>{{ number_format($ventas->ven_total, 0, ',', '.') }}</p>
</div>

<div class="col-sm-12">
    <hr>
</div>

<div class="table-responsive">
    <table class="table item-table">
        <thead>
            <tr>
                <th>#</th>
                <th style="width:35%;min-width:240px;">Producto</th>
                <th class="text-center" style="width:10%;">Cantidad</th>
                <th class="text-center">Precio</th>
                <th class="text-center">Subtotal</th>
            </tr>
        </thead>

        <tbody>
            @if($detalle->count())
                @foreach ($detalle as $det)
                    <tr>
                        <td>{{ $det->id_articulo }}</td>
                        <td>{{ $det->art_descripcion }}</td>
                        <td>{{ $det->det_cantidad }}</td>
                        <td>{{ number_format($det->det_precio_unit, 0, ',', '.') }}</td>
                        <td>{{ number_format($det->det_precio_unit * $det->det_cantidad, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
