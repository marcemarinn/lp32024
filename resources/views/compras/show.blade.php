@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <h2 class="mb-4">Detalle de Compra #{{ $compra->compra_id }}</h2>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Información de la Compra</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Proveedor:</dt>
                        <dd class="col-sm-8">{{ $compra->prov_nombre }}</dd>

                        <dt class="col-sm-4">Sucursal:</dt>
                        <dd class="col-sm-8">{{ $compra->suc_descri }}</dd>

                        <dt class="col-sm-4">Fecha:</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($compra->com_fecha)->format('d/m/Y') }}</dd>

                        <dt class="col-sm-4">Condición:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $compra->com_condicion == 'CONTADO' ? 'success' : 'warning' }}">
                                {{ $compra->com_condicion }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Total:</dt>
                        <dd class="col-sm-8">{{ number_format($compra->com_total, 2, ',', '.') }} Gs.</dd>

                        <dt class="col-sm-4">Estado:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $compra->com_estado == 'ACTIVO' ? 'success' : 'danger' }}">
                                {{ $compra->com_estado }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Comprador:</dt>
                        <dd class="col-sm-8">{{ $compra->comprador }}</dd>

                        @if($compra->com_condicion == 'CREDITO')
                            <dt class="col-sm-4">Cuotas:</dt>
                            <dd class="col-sm-8">{{ $compra->com_cant_cuo }}</dd>

                            <dt class="col-sm-4">Plazo:</dt>
                            <dd class="col-sm-8">{{ $compra->com_plazo }} días</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Descripción</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $compra->com_descripcion ?? 'No se proporcionó descripción.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="card-title mb-0">Detalles de la Compra</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Artículo</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Precio Unitario</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->art_descripcion }}</td>
                                <td class="text-center">{{ $detalle->cantidad }}</td>
                                <td class="text-end">{{ number_format($detalle->precio_unit, 2, ',', '.') }} Gs.</td>
                                <td class="text-end">{{ number_format($detalle->cantidad * $detalle->precio_unit, 2, ',', '.') }} Gs.</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th class="text-end">{{ number_format($compra->com_total, 2, ',', '.') }} Gs.</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('compras.edit', $compra->compra_id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar
        </a>
        <form action="{{ route('compras.destroy', $compra->compra_id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta compra?')">
                <i class="fas fa-trash-alt"></i> Eliminar
            </button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    .badge {
        font-size: 0.9em;
    }
    dt {
        font-weight: 600;
    }
    .table th {
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
@endpush