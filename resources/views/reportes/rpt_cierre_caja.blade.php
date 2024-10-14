@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Generar Reporte de Cierre de Caja</h1>
    <form action="{{ route('cierre-caja.generar') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="fecha_cierre">Fecha de Cierre:</label>
            <input type="date" class="form-control" id="fecha_cierre" name="fecha_cierre" required>
        </div>
        <button type="submit" class="btn btn-primary">Generar Reporte</button>
    </form>
</div>
@endsection