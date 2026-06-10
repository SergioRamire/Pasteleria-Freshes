@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
                <div  id="alert-success" class="alert text-white bg-success" role="alert">
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
                    <h3 class="mb-3">Relación de productos solicitados
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Visualiza y gestiona los productos solicitados en cada pedido para asegurar un control preciso y eficiente de las órdenes realizadas.">
                        </i>
                    </h3>
                </div>
                <div>
                    {{-- @can('marcas.crear') --}}
                        <a href="{{ route('nuevascomprasproveedor.index') }}" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Crear Lista</a>
                    {{-- @endcan --}}
                </div>
            </div>
        </div>

        {{-- Selector de filas --}}
        <div class="col-lg-12 mb-3">
            <form action="{{ route('listasproductosproveedor.index') }}" method="get">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-2">
                            <label for="row" class="form-label">
                                <i class="ri-align-justify"></i> Filas por página
                            </label>
                        <select class="form-control" name="row" id="row" onchange="this.form.submit()">
                            <option value="10" @if(request('row') == '10') selected @endif>10</option>
                            <option value="25" @if(request('row') == '25') selected @endif>25</option>
                            <option value="50" @if(request('row') == '50') selected @endif>50</option>
                            <option value="100" @if(request('row') == '100') selected @endif>100</option>
                        </select>
                    </div>

                    {{-- Filtros y búsqueda --}}
                    <div class="form-group col-md-5">
                        <label for="sucursal"><i class="ri-truck-line me-1"></i> Sucursales</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-truck"></i>
                                </span>
                            </div>
                            <select class="form-control" name="sucursal" onchange="this.form.submit()">
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
                        <label for="search"><i class="ri-search-line"></i> Buscar</label>
                        <div class="input-group">
                            <input type="text" id="search" class="form-control" name="search" placeholder="Buscar Marca" value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                <a href="{{ route('listasproductosproveedor.index') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
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
                        <tr class="ligth ligth-data text-center">
                            <th>N°</th>
                            <th>@sortablelink('codigo', 'codigo')</th>
                            <th>@sortablelink('fecha', 'fecha')</th>
                            <th>@sortablelink('hora', 'Hora')</th>
                            <th>Sucursal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body text-center">
                        @forelse ($listas as $lista)
                            <tr>
                                <td>{{ (($listas->currentPage() - 1) * $listas->perPage()) + $loop->iteration }}</td>
                                <td>{{ $lista->codigo }}</td>
                                <td>{{ \Carbon\Carbon::parse($lista->fecha)->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($lista->hora)->format('h:i A') }}</td>
                                <td>{{ $lista->sucursal }}</td>
                                <td>
                                    <form action="{{ route('listasproductosproveedor.destroy', $lista->id) }}" method="POST" id="delete-form-{{ $lista->id }}">
                                        @csrf
                                        @method('delete')
                                        <div class="d-flex align-items-center list-action">
                                            {{-- @can('marcas.editar')
                                                <a class="btn btn-success mr-2" title="Editar" href="{{ route('listasproductosproveedor.edit', $lista->id) }}">
                                                    <i class="ri-pencil-line"></i>
                                                </a>
                                            @endcan --}}
                                            <a class="btn btn-success" data-toggle="tooltip" title="Imprimir" href="{{ route('listasproductosproveedor.show', $lista->id) }}" target="_blank">
                                                <i class="ri-printer-line mr-0"></i>
                                            </a>
                                            {{-- @can('marcas.eliminar')
                                                @php
                                                    $count = App\Models\Product::where('marca_id', $marca->id)->count();
                                                @endphp

                                                @if ($count == 0)
                                                    <button type="submit" class="btn btn-warning" title="Eliminar" onclick="return confirm('¿Seguro que quieres eliminar esta marca?')">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                @endif
                                            @endcan --}}
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No se encontraron datos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $listas->links() }}
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
