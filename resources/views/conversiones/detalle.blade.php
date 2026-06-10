@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            {{-- Header con título --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="mb-1">
                        <i class="ri-file-text-line me-2"></i>
                        Detalle de Conversión #{{ $conversion->id }}
                    </h3>
                    <p class="text-muted mb-0">Información completa de la conversión realizada</p>
                </div>
                <div>
                    <a href="{{ route('conversiones.historial') }}" class="btn btn-secondary me-2">
                        <i class="ri-arrow-left-line me-1"></i>
                        Volver al Historial
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="ri-printer-line me-1"></i>
                        Imprimir
                    </button>
                </div>
            </div>
        </div>

        {{-- Información General --}}
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ri-information-line me-1"></i>
                        Información General
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="info-item">
                                <label class="form-label text-muted mb-1">
                                    <i class="ri-calendar-line me-1"></i> Fecha y Hora
                                </label>
                                <div class="fw-bold">
                                    {{ \Carbon\Carbon::parse($conversion->created_at)
                                        ->timezone('America/Mexico_City')
                                        ->format('d/m/Y h:i:s A') }}
                                </div>
                                <small class="text-muted">{{ $conversion->created_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="info-item">
                                <label class="form-label text-muted mb-1">
                                    <i class="ri-user-line me-1"></i> Usuario
                                </label>
                                <div class="fw-bold">{{ $conversion->user->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $conversion->user->email ?? '' }}</small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="info-item">
                                <label class="form-label text-muted mb-1">
                                    <i class="ri-building-line me-1"></i> Sucursal
                                </label>
                                <div class="fw-bold">{{ $conversion->branche->nombre ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="info-item">
                                <label class="form-label text-muted mb-1">
                                    <i class="ri-checkbox-circle-line me-1"></i> Estado
                                </label>
                                <div>
                                    @if($conversion->estado == 'completada')
                                        <span class="badge bg-success fs-6">
                                            <i class="ri-checkbox-circle-line me-1"></i>
                                            Completada
                                        </span>
                                    @else
                                        <span class="badge bg-warning fs-6">
                                            <i class="ri-close-circle-line me-1"></i>
                                            Revertida
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detalles de la Conversión --}}
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ri-refresh-line me-1"></i>
                        Detalles de la Conversión
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Producto Origen --}}
                        <div class="col-md-5">
                            <div class="conversion-card origin-card">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">
                                        <i class="ri-arrow-left-circle-line me-1"></i>
                                        Producto Origen (Descontado)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="product-info">
                                        <h5 class="fw-bold">{{ $conversion->producto_origen_nombre }}</h5>
                                        <div class="product-details">
                                            <p class="mb-2">
                                                <strong>Código:</strong>
                                                <span class="badge bg-light text-dark">{{ $conversion->producto_origen_codigo }}</span>
                                            </p>
                                            <p class="mb-2">
                                                <strong>Unidad:</strong>
                                                <span class="text-muted">{{ $conversion->producto_origen_unidad }}</span>
                                            </p>
                                            <div class="stock-info">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label class="form-label text-muted">Stock Anterior</label>
                                                        <div class="fw-bold fs-5">{{ number_format($conversion->stock_origen_anterior) }}</div>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label text-muted">Stock Actual</label>
                                                        <div class="fw-bold fs-5">{{ number_format($conversion->stock_origen_nuevo) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="conversion-amount mt-3">
                                                <div class="text-center p-3 bg-danger text-white rounded">
                                                    <div class="fs-4 fw-bold">-{{ number_format($conversion->cantidad_origen) }}</div>
                                                    <div>{{ $conversion->producto_origen_unidad }} descontadas</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Flecha de Conversión --}}
                        <div class="col-md-2">
                            <div class="conversion-arrow">
                                <div class="text-center h-100 d-flex flex-column justify-content-center">
                                    <div class="conversion-symbol">
                                        <i class="ri-arrow-right-line" style="font-size: 3rem; color: #007bff;"></i>
                                    </div>
                                    <div class="conversion-factor mt-2">
                                        <div class="badge bg-primary fs-6">
                                            Factor: {{ number_format($conversion->factor_conversion) }}x
                                        </div>
                                        <div class="small text-muted mt-1">
                                            {{ number_format($conversion->cantidad_origen) }} × {{ number_format($conversion->factor_conversion) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Producto Destino --}}
                        <div class="col-md-5">
                            <div class="conversion-card destination-card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="ri-arrow-right-circle-line me-1"></i>
                                        Producto Destino (Generado)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="product-info">
                                        <h5 class="fw-bold">{{ $conversion->producto_destino_nombre }}</h5>
                                        <div class="product-details">
                                            <p class="mb-2">
                                                <strong>Código:</strong>
                                                <span class="badge bg-light text-dark">{{ $conversion->producto_destino_codigo }}</span>
                                            </p>
                                            <p class="mb-2">
                                                <strong>Unidad:</strong>
                                                <span class="text-muted">{{ $conversion->producto_destino_unidad }}</span>
                                            </p>
                                            <div class="stock-info">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label class="form-label text-muted">Stock Anterior</label>
                                                        <div class="fw-bold fs-5">{{ number_format($conversion->stock_destino_anterior) }}</div>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label text-muted">Stock Actual</label>
                                                        <div class="fw-bold fs-5">{{ number_format($conversion->stock_destino_nuevo) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="conversion-amount mt-3">
                                                <div class="text-center p-3 bg-success text-white rounded">
                                                    <div class="fs-4 fw-bold">+{{ number_format($conversion->total_unidades_generadas) }}</div>
                                                    <div>{{ $conversion->producto_destino_unidad }} generadas</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resumen de la Operación --}}
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ri-calculator-line me-1"></i>
                        Resumen de la Operación
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="calculation-summary">
                                <h6 class="fw-bold mb-3">Fórmula de Conversión:</h6>
                                <div class="calculation-formula p-4 bg-light rounded border">
                                    <div class="text-center">
                                        <span class="fs-5">
                                            <strong>{{ number_format($conversion->cantidad_origen) }}</strong>
                                            {{ $conversion->producto_origen_unidad }}
                                        </span>
                                        <span class="mx-3 fs-4 text-primary">×</span>
                                        <span class="fs-5">
                                            <strong>{{ number_format($conversion->factor_conversion) }}</strong>
                                        </span>
                                        <span class="mx-3 fs-4 text-success">=</span>
                                        <span class="fs-5 text-success fw-bold">
                                            <strong>{{ number_format($conversion->total_unidades_generadas) }}</strong>
                                            {{ $conversion->producto_destino_unidad }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="operation-summary">
                                <h6 class="fw-bold mb-3">Cambios en Inventario:</h6>
                                <div class="summary-item mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-danger">
                                            <i class="ri-arrow-down-line"></i> Descontado:
                                        </span>
                                        <strong class="text-danger">
                                            -{{ number_format($conversion->cantidad_origen) }}
                                        </strong>
                                    </div>
                                </div>
                                <div class="summary-item mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-success">
                                            <i class="ri-arrow-up-line"></i> Generado:
                                        </span>
                                        <strong class="text-success">
                                            +{{ number_format($conversion->total_unidades_generadas) }}
                                        </strong>
                                    </div>
                                </div>
                                <hr>
                                <div class="summary-item">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Eficiencia:</span>
                                        <strong class="text-primary">
                                            {{ number_format($conversion->factor_conversion) }}:1
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Observaciones --}}
        @if($conversion->observaciones)
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ri-sticky-note-line me-1"></i>
                        Observaciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        {{ $conversion->observaciones }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Estilos adicionales --}}
<style>
.conversion-card {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    height: 100%;
}

.conversion-card .card-header {
    border-radius: 6px 6px 0 0;
}

.product-info h5 {
    color: #333;
    margin-bottom: 1rem;
}

.stock-info {
    background: rgba(0,0,0,0.05);
    padding: 1rem;
    border-radius: 6px;
    margin: 1rem 0;
}

.conversion-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    min-height: 300px;
}

.calculation-formula {
    font-size: 1.2rem;
}

.info-item {
    padding: 1rem;
    background: rgba(0,0,0,0.02);
    border-radius: 6px;
    height: 100%;
}

.summary-item {
    padding: 0.5rem 0;
}

@media print {
    .btn {
        display: none !important;
    }

    .card {
        border: 1px solid #000 !important;
        break-inside: avoid;
    }

    .conversion-arrow {
        min-height: auto;
    }
}

@media (max-width: 768px) {
    .conversion-arrow {
        min-height: auto;
        padding: 2rem 0;
    }

    .conversion-arrow i {
        transform: rotate(90deg);
    }
}
</style>

@endsection
