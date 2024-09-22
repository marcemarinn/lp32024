@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Usuarios</h1>
            </div>
            <div class="col-sm-6">
                <a class="btn btn-primary float-right" href="{{ route('usuarios.create') }}">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </a>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">

    @include('sweetalert::alert')

    <div class="clearfix">
        @includeIf('layouts.buscador' )
    </div>

    <div class="card" id="tabla-container">
        @include('usuarios.table')
    </div>
</div>

@endsection

@push('page_scripts')
<script type="text/javascript">
    $(document).ready(function() {
            /** bucador mediante peticiones fetch*/
            $('#buscar').on('keyup', function() {
                var query = this.value; // valor del input buscar
                //obtener el data-url del paramatro input
                var url= this.getAttribute('data-url');
                // Fetch para realizar peticion de busqueda
                fetch(url + '?buscar='
                    + encodeURIComponent(query), {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest' // Este encabezado indica una solicitud AJAX
                    }
                })
                // respuesta del servidor
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    // se espera un HTML como respuesta
                    return response.text();
                })
                .then(data => {
                    // cargar devuelta el html tabla según lo filtrado
                    document.getElementById('tabla-container').innerHTML = data;
                })
                .catch(error => {// manejar si hay errores en la consulta
                    console.error('Hubo un problema con la solicitud Fetch:', error);
                });
            });
    });
</script>
@endpush
