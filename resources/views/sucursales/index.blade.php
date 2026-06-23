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
                    <h3 class="mb-3">Sucursales
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Gestiona la información de las distintas sucursales para mantener un control centralizado y actualizado de cada ubicación.">
                        </i>
                    </h3>
                </div>
                <div>
                    @can('sucursales.crear')
                        <a href="{{ route('sucursales.create') }}" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Agregar Sucursal</a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="col-lg-12 mb-3">
            <form action="{{ route('sucursales.index') }}" method="GET">
                <div class="row align-items-end">
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

                    <div class="form-group col-md-10">
                        <label for="search"><i class="ri-search-line"></i> Buscar Sucursal</label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control"
                                value="{{ request('search') }}" placeholder="Nombre De Sucursal">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('sucursales.index') }}" class="input-group-text bg-danger" title="Limpiar búsqueda">
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
                            <th>@sortablelink('name', 'Nombre de sucursal')</th>
                            <th>Dirección</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @forelse ($sucursales as $sucursale)
                        <tr>
                            <td>{{ (($sucursales->currentPage() * 10) - 10) + $loop->iteration  }}</td>
                            <td>{{ $sucursale->nombre }}</td>
                            <td>{{ $sucursale->direccion }}</td>
                            <td>
                                {{-- Formulario oculto para eliminación --}}
                                <form id="delete-form-{{ $sucursale->id }}" action="{{ route('sucursales.destroy', $sucursale->id) }}" method="POST" style="display: none;">
                                    @method('delete')
                                    @csrf
                                </form>

                                <div class="d-flex align-items-center list-action">
                                    <a class="badge badge-info mr-2" data-toggle="tooltip" data-placement="top" title="Inspeccionar Sucursal" data-original-title="View"
                                        href="{{ route('sucursales.show', $sucursale->id) }}"><i class="ri-eye-line mr-0"></i>
                                    </a>
                                    @can('sucursales.editar')
                                        <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="Editar Sucursal" data-original-title="Edit" href="{{ route('sucursales.edit', $sucursale->id) }}">
                                            <i class="ri-pencil-line mr-0"></i>
                                        </a>
                                    @endcan

                                    @can('sucursales.eliminar')
                                        @php
                                            $count = App\Models\User::where('branche_id', $sucursale->id)->count();
                                        @endphp

                                        @if ($count == 0)
                                            <button type="button" class="badge bg-warning mr-2 border-none btn-delete"
                                                    data-id="{{ $sucursale->id }}"
                                                    data-nombre="{{ $sucursale->nombre }}"
                                                    data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="Eliminar Sucursal"
                                                    data-original-title="Delete">
                                                <i class="ri-delete-bin-line mr-0"></i>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tipo de dato sucursal no encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $sucursales->links() }}
        </div>
    </div>
    <!-- Page end  -->
</div>

{{-- Script para confirmación de eliminación --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete').forEach(function(button) {
        button.addEventListener('click', function () {
            const itemId = this.getAttribute('data-id');
            const itemNombre = this.getAttribute('data-nombre');

            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Deseas eliminar la sucursal "${itemNombre}"? Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar mensaje de carga
                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Enviar el formulario
                    document.getElementById(`delete-form-${itemId}`).submit();
                }
            });
        });
    });
});
</script>

@endsection
