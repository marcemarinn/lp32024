<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="compras-table">
            <thead>
            <tr>
                <th>Descripcion</th>
                <th>Proveedor</th>
                <th>Sucursal</th>
                <th>Fecha</th>
                <th>Vendedor</th>
                <th>Condicion</th>
                <th>Total</th>
                <th>Cuotas</th>
                <th>Plazo</th>
                {{-- <th>Estado</th> --}}
                
                <th colspan="3">Accion</th>
            </tr>
            </thead>
            <tbody>
            @foreach($compras as $compra)
                <tr>
                    <td>{{ $compra->com_descripcion }}</td>
                    <td>{{ $compra->proveedor }}</td>
                    <td>{{ $compra->sucursal }}</td>
                    <td>{{ $compra->com_fecha }}</td>
                    <td>{{ $compra->comprador }}</td>
                    <td>{{ $compra->com_condicion }}</td>
                    <td>{{ $compra->com_total }}</td>
                    <td>{{ $compra->com_cant_cuo }}</td>
                    <td>{{ $compra->com_plazo }}</td>
                    {{-- <td>{{ $compra->com_estado }}</td> --}}
                    <td  style="width: 120px">
                        {{-- {!! Form::open(['route' => ['compras.destroy', $compra->id], 'method' => 'delete']) !!} --}}
                        <div class='btn-group'>
                            {{-- <a href="{{ route('compras.show', [$compra->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a> --}}
                            {{-- <a href="{{ route('compras.edit', [$compra->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-edit"></i>
                            </a> --}}
                            {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
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
            @include('adminlte-templates::common.paginate', ['records' => $compras])
        </div>
    </div>
</div>
