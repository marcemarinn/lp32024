<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="ciudads-table">
            <thead>
            <tr>
                <th>Descripcion</th>
                <th colspan="3">Accion</th>
            </tr>
            </thead>
            <tbody>
            @foreach($ciudad as $ciu)
                <tr>
                    <td>{{ $ciu->ciu_descripcion }}</td>
                    <td>{{ $value->ciu_descripcion }}</td> 

                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['ciudads.delete', $ciudad->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('ciudads.get', [$ciudad->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('ciudads.put', [$ciudad->id]) }}"
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
            {{-- @include('adminlte-templates::common.paginate', ['records' => $ciudad]) --}}
        </div>
    </div>
</div>
