<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="clientes-table">
            <thead>
            <tr>
                <th>Id Ciudad</th>
                <th>Id Departamento</th>
                <th>Cli Ci</th>
                <th>Cli Nombre</th>
                <th>Cli Apellido</th>
                <th>Cli Sexo</th>
                <th>Cli Fnac</th>
                <th>Cli Direccion</th>
                <th>Cli Telefono</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($clientes as $cliente)
                <tr>
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
                        {!! Form::open(['route' => ['clientes.destroy', $cliente->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('clientes.show', [$cliente->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('clientes.edit', [$cliente->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $clientes])
        </div>
    </div>
</div>
