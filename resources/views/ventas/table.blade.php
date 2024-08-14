<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="ventas-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Sucursal</th>
                <th>Fecha</th>
                <th>Condición</th>
                <th>Total</th>
                <th>Nro Factura</th>
                <th>Vendedor</th>
                <th>Estado</th>
                <th colspan="3">Operaciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($ventas as $venta)
                <tr>
                    <td>{{ $venta->id_venta }}</td>
                    <td>{{ $venta->clientes }}</td>
                    <td>{{ $venta->sucursales }}</td>
                    <td>{{ \Carbon\Carbon::parse($venta->ven_fecha)->format('d/m/Y') }}</td>
                    <td>{{ $venta->ven_condicion }}</td>
                    <td>{{ number_format($venta->ven_total, 0, ',', '.') }}</td>
                    <td>{{ $venta->nro_factura }}</td>
                    <td>{{ $venta->vendedor }}</td>
                    <td>{{ $venta->ven_estado }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['ventas.destroy', $venta->id_venta],
                        'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('ventas.show', [$venta->id_venta]) }}"
                               class='btn btn-default btn-sm'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('ventas.edit', [$venta->id_venta]) }}"
                               class='btn btn-default btn-sm'>
                                <i class="far fa-edit"></i>
                            </a>
                            {!! Form::button('<i class="far fa-trash-alt"></i>',
                            ['type' => 'submit',
                            'class' => 'btn btn-danger btn-sm',
                            'onclick' => "return confirm('Desea Anular la factura?')"]) !!}
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $ventas])
        </div>
    </div>
</div>
