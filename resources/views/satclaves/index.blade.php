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
                    <h3 class="mb-3">Claves SAT
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Consulta y asigna las claves del SAT correspondientes a cada producto para asegurar el cumplimiento fiscal en la facturación.">
                        </i>
                    </h3>
                </div>
                <div>
                    @can('claves.crear')
                        <a href="{{ route('satclaves.create') }}" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Agregar Clave</a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="col-lg-12 mb-3">
            <form action="{{ route('satclaves.index') }}" method="GET">
                <div class="row align-items-end">
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

                    <div class="form-group col-md-10">
                        <label for="search"><i class="ri-search-line"></i> Buscar</label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control"
                                value="{{ request('search') }}" placeholder="Clave SAT">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('satclaves.index') }}" class="input-group-text bg-danger" title="Limpiar búsqueda">
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
                            <th>@sortablelink('c_ClaveProdServ','Clave')</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body text-center">
                        @forelse ($claves as $clave)
                        <tr>
                            <td>{{ (($claves->currentPage() * 10) - 10) + $loop->iteration  }}</td>
                            <td>{{ $clave->c_ClaveProdServ }}</td>
                            <td>
                                    @if($clave->activo == 1)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                            <td>
                                <div class="d-flex align-items-center list-action">
                                    <a class="badge badge-info mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Inspeccionar"
                                        href="{{ route('satclaves.show', $clave->id) }}"><i class="ri-eye-line mr-0"></i>
                                    </a>
                                    @can('claves.editar')
                                        <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar" href="{{ route('satclaves.edit', $clave->id) }}">
                                            <i class="ri-pencil-line mr-0"></i>
                                        </a>
                                    @endcan

                                    @can('claves.agregar_producto')
                                        @if($clave->activo == 1)
                                            <a class="badge bg-info text-white mr-2" data-toggle="tooltip" title="Ver Productos Asociados" href="{{ route('satclaves.verproductos', $clave->id) }}">
                                                <i class="ri-file-list-3-line"></i>
                                            </a>
                                        @endif
                                    @endcan

                                    <form action="{{ route('satclaves.destroy', $clave->id) }}" method="POST" style="margin-bottom: 5px">
                                        @method('delete')
                                        @csrf
                                        @can('claves.eliminar')
                                            @php
                                                $count = App\Models\Product::where('satclave_id', $clave->id)->count();
                                            @endphp

                                            @if ($count == 0)
                                                <button type="submit" class="badge bg-warning mr-2 border-none" onclick="return confirm('Advertencia: el código SAT {{$clave->c_ClaveProdServ}} será eliminado permanentemente. ¿Está seguro?')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Eliminar"><i class="ri-delete-bin-line mr-0"></i></button>
                                            @endif
                                        @endcan
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tipo de dato clave SAT no encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $claves->links() }}
        </div>
    </div>
    <!-- Page end  -->
</div>

@endsection
