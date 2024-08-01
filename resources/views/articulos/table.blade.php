<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="articulos-table">
            <thead>
            <tr>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Precio</th>
                <th>Imagen</th>
                <th>Iva</th>
                <th colspan="3">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($articulos as $articulo)
                <tr>
                    <td>{{ $articulo->mar_cod }}</td>
                    <td>{{ $articulo->art_descripcion }}</td>
                    <td>{{ $articulo->art_precio }}</td>
                    <td>{{ $articulo->art_imagen }}</td>
                    <td>{{ $articulo->art_iva }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['articulos.destroy', $articulo->id_articulo], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('articulos.edit', [$articulo->id_articulo]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-edit"></i>
                            </a>
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
            @include('adminlte-templates::common.paginate', ['records' => $articulos])
        </div>
    </div>
</div>
