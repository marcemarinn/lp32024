<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="clientes-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Departamento</th>
                <th>Ci</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Sexo</th>
                <th>Fecha nacimiento</th>
                <th>Direccion</th>
                <th>Telefono</th>
                <th colspan="3">Operaciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->id_cliente }}</td>
                    <td>{{ $cliente->id_ciudad }}</td>
                    <td>{{ $cliente->id_departamento }}</td>
                    <td>{{ $cliente->cli_ci }}</td>
                    <td>{{ $cliente->cli_nombre }}</td>
                    <td>{{ $cliente->cli_apellido }}</td>
                    <td>{{ $cliente->cli_sexo }}</td>
                    <td>{{ $cliente->cli_fnac }}</td>
                    <td>{{ $cliente->cli_direccion }}</td>
                    <td>{{ $cliente->cli_telefono }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['clientes.destroy', $cliente->id_cliente], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                           
                            <a href="{{ route('clientes.edit', [$cliente->id_cliente]) }}"
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
            {{--@include('adminlte-templates::common.paginate', ['records' => $clientes])--}}
        </div>
    </div>
</div>
