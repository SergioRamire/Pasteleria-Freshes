@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <!-- ALERTAS -->
        <div class="col-lg-12">
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

            <!-- ENCABEZADO -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="mb-3">Historial de Actualizaciones de Productos
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Consulta los cambios realizados en los productos para llevar un control del historial de actualizaciones y garantizar la trazabilidad de la información.">
                        </i>
                    </h3>
                </div>
                <div>
                    <a href="{{ route('historiales.analisis') }}" class="btn btn-success">
                        <i class="fas fa-chart-line me-1"></i> Ver gráfico
                    </a>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE FILTROS -->
        <div class="col-lg-12 mb-3">
            <form action="{{ route('historiales.index') }}" method="get" id="filterForm">
                <div class="form-row align-items-end">

                    <!-- Filas por página -->
                    <div class="form-group col-md-2">
                        <label for="row" class="form-label fw-semibold">
                            <i class="ri-align-justify"></i> Filas por página
                        </label>
                        <select class="form-control" name="row" id="row" onchange="this.form.submit()">
                            <option value="10" @selected(request('row') == '10')>10</option>
                            <option value="25" @selected(request('row') == '25')>25</option>
                            <option value="50" @selected(request('row') == '50')>50</option>
                            <option value="100" @selected(request('row') == '100')>100</option>
                        </select>
                    </div>

                    <!-- Fecha del pedido -->
                    @php
                        $hoy = now()->timezone('America/Mexico_City')->toDateString();
                    @endphp
                    <div class="form-group col-md-4">
                        <label for="order_date" class="form-label fw-semibold">
                            <i class="ri-calendar-line"></i> Fecha del pedido
                        </label>
                        <input type="date" name="order_date" id="order_date" max="{{ $hoy }}" class="form-control"
                            value="{{ request('order_date') }}">
                    </div>

                    <!-- Búsqueda -->
                    <div class="form-group col-md-6">
                        <label for="search" class="form-label fw-semibold">
                            <i class="ri-search-line"></i> Buscar
                        </label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Buscar historial..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('historiales.index') }}" class="input-group-text bg-danger" title="Limpiar búsqueda">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- TABLA DE PEDIDOS -->
        <div class="col-lg-12">
            <div class="table-responsive rounded shadow-sm border mb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>N°</th>
                            <th>Producto</th>
                            <th>Código</th>
                            <th>@sortablelink('fecha', 'Fecha')</th>
                            <th>Accion</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historiales as $historial)
                            <tr>
                                <td class="text-center">{{ (($historiales->currentPage() - 1) * $historiales->perPage()) + $loop->iteration }}</td>
                                <td>{{ $historial->producto }}</td>
                                <td class="text-center">{{ $historial->codigo }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($historial->fecha)->format('d-m-Y') }}</td>
                                <td class="text-center">{{ $historial->accion }}</td>
                                <td class="text-center align-middle">
                                    <form action="{{ route('historiales.destroy', $historial->id) }}" method="POST" class="mb-0 d-flex justify-content-center">
                                        @method('delete')
                                        @csrf
                                        <div class="list-action">
                                            <a class="btn btn-info me-2" data-toggle="tooltip" data-placement="top" title="Ver"
                                                href="{{ route('historiales.show', $historial->id) }}">
                                                <i class="ri-eye-line mr-0"></i>
                                            </a>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No se encontraron datos relacionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- PAGINACIÓN -->

            </div>
             <div class="d-flex justify-content-end">
                    {{ $historiales->appends(request()->query())->links() }}
                </div>

        </div>
    </div>
</div>

<!-- AUTO-SUBMIT PARA SELECT Y DATE -->
<script>
    document.getElementById('row').addEventListener('change', function () {
        document.getElementById('filterForm').submit();
    });
    document.getElementById('order_date').addEventListener('change', function () {
        document.getElementById('filterForm').submit();
    });
</script>
@endsection
