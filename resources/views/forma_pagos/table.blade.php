<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="forma_pagos-table">
            <thead>
            <tr>
                <th>Descripcion</th>
                <th colspan="3">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($forma_pagos as $formaPago)
                <tr>
                    <td>{{ $formaPago->descripcion }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['forma_pagos.destroy', $formaPago->id_forma], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('forma_pagos.show', [$formaPago->id_forma]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('forma_pagos.edit', [$formaPago->id_forma]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $forma_pagos])
        </div>
    </div>
</div>
