@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Listar Datos de Ciudad</h1>
                </div>
                <div class="col-sm-6">
                    @can('ciudades create')
                        <a class="btn btn-primary float-right"
                        href="{{ route('ciudades.create') }}">
                            Nuevo
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('sweetalert::alert')

        <div class="clearfix"></div>

        <div class="card">
            @include('ciudads.table')
        </div>
    </div>

@endsection

@push('page_scripts')
    <script>
        $(documento).ready(function(){
            $("#departamento_id").on("change", function(e) {
                e.preventDefault();
                e.stopPropagation();

                let url  = "{{ url('buscar-ciudad') }}?departamento_id="+$("#departamento_id").val()
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(res => {
                    /** recuperar valores y mostrarlos */
                    // Si la respuesta es success True proceso el resultado devuelto por la funcion
                    if (res.success) {
                        console.log("datos de ciudad segun departamento", res.data);
                        // armar el select de ciudad
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: res.message,
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Hubo un problema al procesar la solicitud.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                });
            });


        });
    </script>
    
@endpush