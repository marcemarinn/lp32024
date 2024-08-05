<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="articulos-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Imagen</th>
                    <th>Marca</th>
                    <th>Iva</th>
                    <th colspan="3">Operaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($articulos as $articulo)
                    <tr>
                        <td>{{ $articulo->id_articulo }}</td>
                        <td>{{ $articulo->art_descripcion }}</td>
                        <td>{{ number_format($articulo->art_precio, 0, ',', '.') }}</td>
                        <td>
                            @if (!empty($articulo->art_imagen))
                                <img src="{{ asset('img/articulos/' . $articulo->art_imagen) }}"
                                    alt="{!! $articulo->art_imagen !!}"  width="90px" height="90px">
                            @endif
                        </td>
                        <td>{{ $articulo->mar_descrip }}</td>
                        <td>{{ $articulo->art_iva }}</td>
                        <td style="width: 120px">
                            {!! Form::open(['route' => ['articulos.destroy', $articulo->id_articulo], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('articulos.edit', [$articulo->id_articulo]) }}"
                                    class='btn btn-default btn-xs'>
                                    <i class="far fa-edit"></i>
                                </a>
                                {!! Form::button('<i class="far fa-trash-alt"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'onclick' => "return confirm('Are you sure?')",
                                ]) !!}
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
            @include('adminlte-templates::common.paginate', ['records' => $articulos])
        </div>
    </div>
</div>
