<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="usuarios-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Usuario</th>
                <th>UserName</th>
                <th>Nro CI</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Teléfono</th>
                <th colspan="3">Operaciones</th>
            </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->ci }}</td>
                        <td>{{ $usuario->rol }}</td>
                        <!-- Aquí haces la verificación para mostrar true/false -->
                        <td>{{ $usuario->isactive == 1 ? 'Activo' : 'Inactivo' }}</td>
                        <td>{{ $usuario->telefono }}</td>
                        <td style="width: 120px">
                            {!! Form::open(['route' => ['usuarios.destroy', $usuario->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('usuarios.edit', [$usuario->id]) }}" class='btn btn-default btn-sm'>
                                    <i class="far fa-edit"></i>
                                </a>
                                {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-sm', 'onclick' => "return confirm('Desea Inactivar el Usuario?')"]) !!}
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
            @include('adminlte-templates::common.paginate', ['records' => $usuarios])
        </div>
    </div>
</div>
