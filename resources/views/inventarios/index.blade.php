@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
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

            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="mb-3">Inventarios por Sucursales
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Consulta y compara las existencias de productos en todas las sucursales disponibles para una mejor gestión del stock.">
                        </i>
                    </h3>
                </div>
                <div>
                    @can('inventarios.crear')
                        <a href="{{ route('inventarios.create') }}" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Asignar Producto A Sucursal</a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Selector de filas --}}
        <div class="col-lg-12 mb-3">
            <form action="{{ route('inventarios.index') }}" method="get">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-2">
                            <label for="row" class="form-label">
                                <i class="ri-align-justify"></i> Filas por página
                            </label>
                            <select class="form-control" name="row" id="row" onchange="this.form.submit()">
                            <option value="30" @if(request('row') == '30') selected @endif>30</option>
                            <option value="35" @if(request('row') == '35') selected @endif>35</option>
                            <option value="50" @if(request('row') == '50') selected @endif>50</option>
                            <option value="100" @if(request('row') == '100') selected @endif>100</option>
                        </select>
                    </div>

                    {{-- Filtros y búsqueda --}}
                    <div class="form-group col-md-5">
                        <label for="sucursal"><i class="ri-building-line me-1"></i> Sucursales</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-store"></i>
                                </span>
                            </div>
                            <select class="form-control" name="sucursal" id="sucursal" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}" @if(request('sucursal') == $sucursal->id) selected @endif>
                                        {{ $sucursal->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-md-5">
                        <label for="search"><i class="ri-search-line"></i> Buscar:</label>
                        <div class="input-group">
                            <input type="text" id="search" class="form-control" name="search" placeholder="Buscar producto" value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                <a href="{{ route('inventarios.index') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
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
            <div class="table-responsive rounded mb-3">
                <table class="table mb-0">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th>N°</th>
                            <th>Código</th>
                            <th>@sortablelink('producto', 'Producto')</th>
                            <th>Código Barras</th>
                            <th>Stock</th>
                            <th>Precio publico</th>
                            <th>Sucursal</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @forelse ($inventarios as $inventario)
                            <tr>
                                <td>{{ ($inventarios->currentPage() - 1) * $inventarios->perPage() + $loop->iteration }}</td>
                                <td>{{ $inventario->product_code }}</td>
                                <td>{{ $inventario->producto }}</td>
                                <td>{{ $inventario->codigo_barras }}</td>
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
                                <td class="text-center">${{ number_format($inventario->precio_publico, 2) }}</td>
                                <td>{{ $inventario->sucursal }}</td>
                                <td>
                                    @if($inventario->estado == 1)
                                        <span class="badge badge-success">Activo</span>
                                    @elseif($inventario->disponibilidad == 0)
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                    {{-- $inventario->sucursal }} --}}
                                </td>
                                <td>
                                    <form action="{{ route('inventarios.destroy', $inventario->id) }}" method="POST" id="delete-form-{{ $inventario->id }}">
                                        @method('delete')
                                        @csrf
                                        <div class="d-flex align-items-center list-action">
                                            @can('inventarios.editar')
                                                <a class="btn btn-success mr-2" title="Editar" href="{{ route('inventarios.edit', $inventario->id) }}">
                                                    <i class="ri-pencil-line mr-0"></i>
                                                </a>
                                            @endcan

                                            @can('inventarios.eliminar')
                                                @php
                                                    $count = App\Models\OrderDetails::where('inventario_id', $inventario->id)->count();
                                                @endphp

                                                @if ($count == 0)
                                                    <button type="submit" class="btn btn-warning mr-2 border-none" onclick="return confirm('¿Estás seguro de que quieres eliminar este registro? Esta acción no se puede deshacer.')" data-toggle="tooltip" data-placement="top" title="Eliminar" data-original-title="Delete"><i class="ri-delete-bin-line mr-0"></i></button>
                                                @endif
                                            @endcan
                                        </div>
                                    </form>
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
            {{ $inventarios->links() }}
        </div>
    </div>
</div>
@endsection

{{-- Confirmación para eliminar --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete').forEach(function(button) {
        button.addEventListener('click', function () {
            const itemId = this.getAttribute('data-id');
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Esta acción no se puede deshacer!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${itemId}`).submit();
                }
            });
        });
    });
});
</script>
@endpush
