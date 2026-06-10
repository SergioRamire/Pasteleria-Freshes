{{-- Agregar SweetAlert2 en el head o antes del cierre del body --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@extends('dashboard.body.main')

@php use Illuminate\Support\Str; @endphp
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
                    <h3 class="mb-3">Inventario de {{ $sucursal->nombre }}
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Revisa y administra los productos disponibles en la sucursal actual, verificando existencias y actualizaciones en tiempo real.">
                        </i>
                    </h3>
                </div>
                @php
                    $count = App\Models\Inventario::where('branche_id', $sucursal->id)
                                    ->whereColumn('stock', '<=', 'stock_minimo')
                                    ->count();
                @endphp
                @if($count > 0)
                    <div>
                        <a href="{{ route('myinventarios.imprimir_stock', $sucursal->id) }}" class="btn btn-danger" title="Descargar productos con stock mínimo" target="_blank">
                            <i class="fa-solid fa-download me-1"></i>Descargar Stock Mínimo
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Filtros y búsqueda --}}
        <div class="col-lg-12 mb-3">
            <form action="{{ route('myinventarios.index') }}" method="get">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-2">
                        <label for="row" class="form-label">
                            <i class="ri-align-justify"></i> Fila por página
                        </label>
                        <select class="form-control" name="row" id="row" onchange="this.form.submit()">
                            <option value="30" @if(request('row') == '30') selected @endif>30</option>
                            <option value="35" @if(request('row') == '35') selected @endif>35</option>
                            <option value="50" @if(request('row') == '50') selected @endif>50</option>
                            <option value="100" @if(request('row') == '100') selected @endif>100</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="category_id">
                            <i class="ri-archive-line me-1"></i> Categoría
                        </label>
                        <select name="category_id" id="category_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @if(request('category_id') == $category->id) selected @endif>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="marca_id">
                            <i class="ri-calendar-line me-1"></i> Marca
                        </label>
                        <select name="marca_id" id="marca_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            @foreach($marcas as $marca)
                                <option value="{{ $marca->id }}" @if(request('marca_id') == $marca->id) selected @endif>
                                    {{ $marca->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="search"><i class="ri-search-line"></i> Buscar producto</label>
                        <div class="input-group">
                            <input type="text" id="search" class="form-control" name="search" placeholder="Buscar producto" value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                <a href="{{ route('myinventarios.index') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabla --}}
        <div class="col-lg-12">
            <div class="table-responsive rounded shadow-sm border mb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th>N°</th>
                            <th>Código</th>
                            <th>Foto</th>
                            <th>@sortablelink('producto', 'Producto')</th>
                            <th>@sortablelink('codigo_barras', 'Código barras')</th>
                            <th>Precio venta</th>
                            <th>Categoría</th>
                            <th>Marca</th>
                            <th>Stock</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @forelse ($inventarios as $inventario)
                            <tr>
                                <td>{{ ($inventarios->currentPage() - 1) * $inventarios->perPage() + $loop->iteration }}</td>
                                <td>{{ $inventario->product_code }}</td>
                                <td>
                                    <img class="avatar-60 rounded" src="{{ $inventario->product_image ? asset('storage/products/'.$inventario->product_image) : asset('assets/images/product/default.webp') }}">
                                </td>
                                <td>{{ $inventario->producto }}</td>
                                <td>{{ $inventario->codigo_barras }}</td>
                                <td class="text-center">${{ number_format($inventario->precio_venta, 2) }}</td>
                                <td>{{ $inventario->category_name }}</td>
                                <td>{{ $inventario->marca_nombre }}</td>
                                <td class="text-center">
                                    @if($inventario->stock <= $inventario->stock_minimo)
                                        <span class="badge text-white" style="background-color: #dc3545;" title="Stock Bajo">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                            {{ $inventario->stock }}
                                        </span>
                                    @else
                                        {{ $inventario->stock }}
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex align-items-center list-action">
                                        {{-- Ver inventario --}}
                                        <a class="badge badge-info mr-2"
                                           style="font-size: 0.95rem; padding: 0.5em 0.75em;"
                                           data-toggle="tooltip"
                                           title="Ver Inventario"
                                           href="{{ route('myinventarios.show', $inventario->id) }}">
                                            <i class="ri-eye-line mr-0"></i>
                                        </a>

                                        {{-- Editar inventario --}}
                                        @can('myinventarios.editar')
                                            <a class="badge bg-success mr-2"
                                               style="font-size: 0.95rem; padding: 0.5em 0.75em;"
                                               data-toggle="tooltip"
                                               title="Editar Inventario"
                                               href="{{ route('myinventarios.edit', $inventario->id) }}">
                                                <i class="ri-pencil-line mr-0"></i>
                                            </a>
                                        @endcan

                                        {{-- Eliminar inventario con SweetAlert2 --}}
                                        @can('myinventarios.eliminar')
                                            @php
                                                $count = App\Models\OrderDetails::where('inventario_id', $inventario->id)->count();
                                            @endphp

                                            @if ($count == 0)
                                                <button type="button"
                                                        class="badge bg-danger border-0 mr-2"
                                                        style="font-size: 0.95rem; padding: 0.5em 0.75em; cursor: pointer;"
                                                        onclick="confirmDelete({{ $inventario->id }}, '{{ addslashes($inventario->producto) }}', '{{ $inventario->product_code }}', {{ $inventario->stock }})"
                                                        data-toggle="tooltip"
                                                        title="Eliminar Inventario">
                                                    <i class="ri-delete-bin-line mr-0"></i>
                                                </button>

                                                {{-- Formulario oculto para eliminar --}}
                                                <form action="{{ route('myinventarios.destroy', $inventario->id) }}"
                                                      method="POST"
                                                      id="delete-form-{{ $inventario->id }}"
                                                      style="display: none;">
                                                    @csrf
                                                    @method('delete')
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">No se encontraron resultados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Paginación --}}
            {{ $inventarios->links() }}
        </div>
    </div>
</div>

{{-- Script para SweetAlert2 --}}
<script>
function confirmDelete(inventarioId, producto, productCode, stock) {
    Swal.fire({
        title: '¿Eliminar inventario?',
        html: `
            <div class="text-start">
                <p class="mb-2"><strong>Producto:</strong> ${producto}</p>
                <p class="mb-2"><strong>Código:</strong> ${productCode}</p>
                <p class="mb-2"><strong>Stock:</strong> ${stock}</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ri-delete-bin-line me-1"></i>Sí, eliminar',
        cancelButtonText: '<i class="ri-close-line me-1"></i>Cancelar',
        customClass: {
            popup: 'swal2-popup-custom',
            title: 'swal2-title-custom',
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false,
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                html: 'Por favor espera un momento',
                allowEscapeKey: false,
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            // Enviar formulario
            document.getElementById('delete-form-' + inventarioId).submit();
        }
    });
}

// Auto-ocultar alertas después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const alertSuccess = document.getElementById('alert-success');
    const alertError = document.getElementById('alert-error');

    if (alertSuccess) {
        setTimeout(() => {
            alertSuccess.style.transition = 'opacity 0.5s ease';
            alertSuccess.style.opacity = '0';
            setTimeout(() => alertSuccess.remove(), 500);
        }, 5000);
    }

    if (alertError) {
        setTimeout(() => {
            alertError.style.transition = 'opacity 0.5s ease';
            alertError.style.opacity = '0';
            setTimeout(() => alertError.remove(), 500);
        }, 5000);
    }
});
</script>

{{-- Estilos adicionales para SweetAlert2 --}}
<style>
.swal2-popup-custom {
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.swal2-title-custom {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
}

.swal2-html-container {
    font-size: 1rem;
}

.swal2-confirm.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    font-weight: 500;
}

.swal2-confirm.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.swal2-cancel.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    font-weight: 500;
}

.swal2-cancel.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}
</style>

@endsection
