<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="departamentos-table">
            <thead>
            <tr>
                <th>Descripcion</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($departamentos as $departamento)
                <tr>
                    <td>{{ $departamento->dep_descripcion }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['departamento.destroy', $departamento->id_departamento], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('departamento.show', [$departamento->id_departamento]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('departamento.edit', [$departamento->id_departamento]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $departamentos])
        </div>
    </div>
</div>
