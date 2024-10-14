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
        @includeIf('layouts.buscador', ['url' => url()->current()])
    </div>

    <div class="card tabla-container">
        @include('usuarios.table')
    </div>
</div>

@endsection

@push('page_scripts')
<script type="text/javascript">
    
</script>
@endpush
