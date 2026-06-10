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
                    <h3 class="mb-3">Equivalencias
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Define y administra las relaciones de equivalencia entre productos para facilitar su manejo en distintas presentaciones o unidades de medida.">
                        </i>
                    </h3>
                </div>
                <div>
                    @can('equivalencias.crear')
                        <a href="{{ route('equivalencias.create') }}" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Agregar Equivalencia</a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="col-lg-12 mb-3">
            <form action="{{ route('equivalencias.index') }}" method="GET">
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
                        <label for="search"><i class="ri-search-line"></i> Buscar Equivalencia</label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control"
                                value="{{ request('search') }}" placeholder="Nombre Equivalencia">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('equivalencias.index') }}" class="input-group-text bg-danger" title="Limpiar búsqueda">
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
                            <th class="text-center">N°</th>
                            <th>@sortablelink('nombre','Nombre')</th>
                            <th>Abreviatura</th>
                            <th class="text-center">@sortablelink('clave_sat','Clave SAT')</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @forelse ($equivalencias as $equivalencia)
                        <tr>
                            <td class="text-center">{{ (($equivalencias->currentPage() * 10) - 10) + $loop->iteration  }}</td>
                            <td>{{ $equivalencia->nombre }}</td>
                            <td>{{ $equivalencia->abreviatura }}</td>
                            <td class="text-center"><span class="badge badge-primary">{{ $equivalencia->clave_sat ?? 'Sin código SAT' }}</span></td>
                            <td class="text-center">
                                    @if($equivalencia->activo == 1)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center list-action">
                                    <a class="badge badge-info mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Inspeccionar"
                                        href="{{ route('equivalencias.show', $equivalencia->id) }}"><i class="ri-eye-line mr-0"></i>
                                    </a>
                                    @can('equivalencias.editar')
                                        <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar" href="{{ route('equivalencias.edit', $equivalencia->id) }}">
                                            <i class="ri-pencil-line mr-0"></i>
                                        </a>
                                    @endcan
                                    <form action="{{ route('equivalencias.destroy', $equivalencia->id) }}" method="POST" style="margin-bottom: 5px">
                                        @method('delete')
                                        @csrf
                                        @can('equivalencias.eliminar')
                                            @php
                                                $count = App\Models\Product::where('equivalencia_id', $equivalencia->id)->count();
                                            @endphp

                                            @if ($count == 0)
                                                <button type="submit" class="badge bg-warning mr-2 border-none" onclick="return confirm('¿Está seguro que desea eliminar esta equivalencia? Esta acción no se puede deshacer')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Eliminar"><i class="ri-delete-bin-line mr-0"></i></button>
                                            @endif
                                        @endcan
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tipo de dato equivalencia no encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $equivalencias->links() }}
        </div>
    </div>
    <!-- Page end  -->
</div>

@endsection
