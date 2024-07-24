<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="entidad-emisoras-table">
            <thead>
            <tr>
                <th>Enti Descri</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($entidadEmisoras as $entidadEmisora)
                <tr>
                        <td>{{ $entidadEmisora->enti_descri }}</td>
                        <td  style="width: 120px">
                            {!! Form::open(['route' => ['entidadEmisoras.destroy', $entidadEmisoras->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('entidadEmisoras.show', [$entidadEmisoras->id]) }}"
                                   class='btn btn-default btn-xs'>
                                    <i class="far fa-eye"></i>
                                </a>
                                <a href="{{ route('entidadEmisoras.edit', [$entidadEmisoras->id]) }}"
                                   class='btn btn-default btn-xs'>
                                    <i class="far fa-edit"></i>
                                </a>
                                {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
    
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            {{--@include('adminlte-templates::common.paginate', ['records' => $entidadEmisoras])--}}
            
        </div>
    </div>
</div>
