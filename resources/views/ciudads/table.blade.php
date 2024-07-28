<div class="card-body p-0">
    <!-- Mostrar el mensaje de flash si está presente -->
    @if(Session::has('success'))
        <div class="alert alert-success" id="flash-message">
            {{ Session::get('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table" id="ciudads-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Descripción</th>
                    <th colspan="3">Operaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ciudades as $value)
                    <tr>
                        <td>{{ $value->id_ciudad }}</td>
                        <td>{{ $value->ciu_descripcion }}</td>
                        <td style="width: 120px">
                            {!! Form::open(['route' => ['ciudades.destroy', $value->id_ciudad], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('ciudades.edit', [$value->id_ciudad]) }}" class='btn btn-default btn-sm'>
                                    <i class="far fa-edit"></i>
                                </a>
                                {!! Form::button('<i class="far fa-trash-alt"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'onclick' => "return confirm('Desea Eliminar el dato?')",
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
            @include('adminlte-templates::common.paginate', ['records' => $ciudades])
        </div>
    </div>
</div>

<!-- Agrega el script para ocultar el mensaje de flash -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(function() {
                flashMessage.style.display = 'none';
            }, 5000); // Ocultar después de 5 segundos
        }
    });
</script>
