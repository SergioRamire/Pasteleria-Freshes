@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            {{-- Alertas de éxito y error --}}
            @if (session()->has('success'))
                <div id="alert-success" class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-text">{{ session('success') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            @if (session()->has('error'))
                <div id="alert-error" class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-text">{{ session('error') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            {{-- Header con título --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="mb-3">
                        <i class="ri-history-line me-2"></i>
                        Historial de Conversiones
                    </h3>
                    <p class="text-muted mb-0">Registro completo de todas las conversiones realizadas</p>
                </div>
                <div>
                    <a href="{{ route('conversiones.index') }}" class="btn btn-primary">
                        <i class="ri-arrow-left-line me-1"></i>
                        Volver a Conversiones
                    </a>
                </div>
            </div>
        </div>

        {{-- Filtros y búsqueda --}}
        <div class="col-lg-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ri-filter-3-line me-1"></i>
                        Filtros de Búsqueda
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('conversiones.historial') }}" method="get">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="row" class="form-label">
                                    <i class="ri-align-justify"></i> Registros por página
                                </label>
                                <select class="form-control" name="row" id="row" onchange="this.form.submit()">
                                    <option value="30" @if(request('row') == '30') selected @endif>30</option>
                                    <option value="35" @if(request('row') == '35') selected @endif>35</option>
                                    <option value="50" @if(request('row') == '50') selected @endif>50</option>
                                    <option value="100" @if(request('row') == '100') selected @endif>100</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="estado" class="form-label">
                                    <i class="ri-checkbox-circle-line me-1"></i> Estado
                                </label>
                                <select name="estado" id="estado" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todos</option>
                                    <option value="completada" @if(request('estado') == 'completada') selected @endif>
                                        Completada
                                    </option>
                                    <option value="revertida" @if(request('estado') == 'revertida') selected @endif>
                                        Revertida
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="user_id" class="form-label">
                                    <i class="ri-user-line me-1"></i> Usuario
                                </label>
                                <select name="user_id" id="user_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todos</option>
                                    @foreach($usuarios as $usuario)
                                        <option value="{{ $usuario->id }}" @if(request('user_id') == $usuario->id) selected @endif>
                                            {{ $usuario->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="fecha_inicio" class="form-label">
                                    <i class="ri-calendar-line me-1"></i> Desde
                                </label>
                                <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio"
                                       value="{{ request('fecha_inicio') }}" onchange="this.form.submit()">
                            </div>

                            <div class="col-md-2">
                                <label for="fecha_fin" class="form-label">
                                    <i class="ri-calendar-line me-1"></i> Hasta
                                </label>
                                <input type="date" class="form-control" name="fecha_fin" id="fecha_fin"
                                       value="{{ request('fecha_fin') }}" onchange="this.form.submit()">
                            </div>

                            <div class="col-md-12">
                                <label for="search" class="form-label">
                                    <i class="ri-search-line"></i> Buscar
                                </label>
                                <div class="input-group">
                                    <input type="text" id="search" class="form-control" name="search"
                                           placeholder="Buscar por producto, código..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                        <a href="{{ route('conversiones.historial') }}"
                                           class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Resumen estadístico --}}
        <div class="col-lg-12 mb-3">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="ri-refresh-line" style="font-size: 2rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $historial->total() }}</h5>
                                    <small>Total Conversiones</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="ri-checkbox-circle-line" style="font-size: 2rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $historial->where('estado', 'completada')->count() }}</h5>
                                    <small>Completadas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="ri-close-circle-line" style="font-size: 2rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $historial->where('estado', 'revertida')->count() }}</h5>
                                    <small>Revertidas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="ri-calendar-line" style="font-size: 2rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $historial->where('created_at', '>=', today())->count() }}</h5>
                                    <small>Hoy</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ri-list-check-2 me-1"></i>
                        Registro de Conversiones
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center">N°</th>
                                    <th class="text-center">Fecha</th>
                                    <th>Producto Origen</th>
                                    <th>Producto Destino</th>
                                    <th class="text-center">Conversión</th>
                                    <th class="text-center">Usuario</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($historial as $conversion)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($historial->currentPage() - 1) * $historial->perPage() + $loop->iteration }}
                                        </td>
                                        <td class="text-center">
                                        <small>
                                            {{ \Carbon\Carbon::parse($conversion->created_at)->timezone('America/Mexico_City')->format('d/m/Y') }}<br>
                                            <span class="text-muted">
                                                {{ \Carbon\Carbon::parse($conversion->created_at)->timezone('America/Mexico_City')->format('h:i A') }}
                                            </span>
                                        </small>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong>{{ $conversion->producto_origen_nombre }}</strong>
                                                <small class="text-muted">
                                                    <i class="ri-qr-code-line me-1"></i>{{ $conversion->producto_origen_codigo }}
                                                </small>
                                                <span class="badge bg-light text-dark">
                                                    -{{ $conversion->cantidad_origen }} {{ $conversion->producto_origen_unidad }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong>{{ $conversion->producto_destino_nombre }}</strong>
                                                <small class="text-muted">
                                                    <i class="ri-qr-code-line me-1"></i>{{ $conversion->producto_destino_codigo }}
                                                </small>
                                                <span class="badge bg-success text-white">
                                                    +{{ $conversion->total_unidades_generadas }} {{ $conversion->producto_destino_unidad }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="conversion-formula">
                                                <span class="badge bg-primary">
                                                    {{ $conversion->cantidad_origen }} × {{ $conversion->factor_conversion }}
                                                </span>
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        = {{ $conversion->total_unidades_generadas }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-column align-items-center">
                                                <strong>{{ $conversion->user->name ?? 'N/A' }}</strong>
                                                <small class="text-muted">
                                                    {{ $conversion->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($conversion->estado == 'completada')
                                                <span class="badge bg-success">
                                                    <i class="ri-checkbox-circle-line me-1"></i>
                                                    Completada
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="ri-close-circle-line me-1"></i>
                                                    Revertida
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <a href="{{ route('conversiones.historial.detalle', $conversion->id) }}"
                                                   class="btn btn-sm btn-outline-primary me-1"
                                                   data-toggle="tooltip" title="Ver Detalles">
                                                    <i class="ri-eye-line mr-0"></i>
                                                </a>
                                                {{--
                                                @if($conversion->estado == 'completada')
                                                    <button class="btn btn-sm btn-outline-warning"
                                                            onclick="revertirConversion({{ $conversion->id }})"
                                                            data-toggle="tooltip" title="Revertir Conversión">
                                                        <i class="ri-arrow-go-back-line"></i>
                                                    </button>
                                                @endif
                                                --}}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="ri-history-line" style="font-size: 3rem; color: #ccc;"></i>
                                                <h6 class="mt-2 text-muted">No se encontraron conversiones</h6>
                                                <p class="text-muted mb-0">No hay registros que coincidan con los filtros aplicados</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($historial->hasPages())
                    <div class="card-footer">
                        {{ $historial->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Estilos adicionales --}}
<style>
.conversion-formula {
    background: rgba(13, 110, 253, 0.1);
    padding: 8px;
    border-radius: 6px;
    border: 1px solid rgba(13, 110, 253, 0.2);
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background: rgba(13, 110, 253, 0.05);
    border-bottom: 1px solid rgba(13, 110, 253, 0.1);
}
</style>

@endsection
