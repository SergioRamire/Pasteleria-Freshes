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
            @if (session('imprimir_url'))
                <script>
                    window.onload = function () {
                        window.open("{{ session('imprimir_url') }}", "_blank");
                    };
                </script>
            @endif

            @php
                use Carbon\Carbon;
                $hoy = Carbon::now()->timezone('America/Mexico_City')->format('Y-m-d');
            @endphp

            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="mb-3">Adquisiciones de inventario a proveedores
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Registra y consulta las compras realizadas a proveedores para mantener actualizado el inventario y controlar el abastecimiento de productos.">
                        </i>
                    </h3>
                </div>
                <div class="d-flex gap-2">
                    @can('compras.realizar_compra')
                        <a href="{{ route('nuevascompras.index') }}" class="btn btn-primary add-list" title="Registrar Compra de Proveedores">
                            <b>+</b> Registrar Compra
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-3">
            <form action="{{ route('compras.index') }}" method="get">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-3">
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

                    {{-- <div class="form-group col-md-3">
                        <label for="row">
                            <i class="ri-archive-line mr-1"></i> Estado del traspaso:
                        </label>
                        <select class="form-control" id="estado" name="estado" onchange="this.form.submit()">
                           <option value="" disabled {{ request('estado') ? '' : 'selected' }}>--Selecciona una opción--</option>
                            <option value="despachado" {{ request('estado') == 'despachado' ? 'selected' : '' }}>Despachado</option>
                            <option value="recibido" {{ request('estado') == 'recibido' ? 'selected' : '' }}>Recibido</option>
                            <option value="solicitado" {{ request('estado') == 'solicitado' ? 'selected' : '' }}>Solicitado</option>
                        </select>
                    </div> --}}

                    <div class="form-group col-md-3">
                        <label for="order_date"><i class="ri-calendar-line"></i> Fecha de la compra</label>
                        <input type="date" name="order_date" max="{{$hoy}}" id="order_date" class="form-control" value="{{ request('order_date') }}" onchange="this.form.submit()">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="search"><i class="ri-search-line"></i> Buscar</label>
                        <div class="input-group">
                            <input type="text" id="search" class="form-control" name="search" placeholder="Buscar compra" value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                <a href="{{ route('compras.index') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
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
                            <th>Código</th>
                            <th>@sortablelink('fecha', 'Fecha')</th>
                            <th>@sortablelink('hora', 'Hora')</th>
                            {{-- <th>Estado</th> --}}
                            <th>Sucursal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body text-center">
                        @forelse ($compras as $compra)
                        <tr>
                            <td>{{ ($compras->currentPage() - 1) * $compras->perPage() + $loop->iteration }}</td>
                            <td>{{ $compra->codigo }}</td>
                            <td>{{ \Carbon\Carbon::parse($compra->fecha)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($compra->hora)->format('h:i A') }}</td>
                            {{-- <td>
                                @if($traspaso->estado == 'solicitado')
                                    <span class="badge badge-danger">{{ ucfirst($traspaso->estado) }}</span>
                                @elseif($traspaso->estado == 'despachado')
                                   <span class="badge badge-primary "> {{ ucfirst($traspaso->estado) }}</span>
                                @else
                                    <span class="badge badge-success">{{ ucfirst($traspaso->estado) }}</span>
                                @endif
                            </td> --}}

                            <td>{{ ucfirst($compra->sucursal_origen_nombre) }}</td>
                            <td>
                                <form action="{{ route('compras.destroy', $compra->id) }}" method="POST" style="margin-bottom: 5px">
                                    @method('delete')
                                    @csrf
                                    <div class="d-flex align-items-center list-action">
                                         <a class="btn btn-info mr-2" data-toggle="tooltip" data-placement="top" title="Inspeccionar " data-original-title="View"
                                            href="{{ route('compras.show', $compra->id) }}"><i class="ri-eye-line mr-0"></i>
                                        </a>
                                        {{-- <a class="btn btn-success mr-2" data-toggle="tooltip" data-placement="top" title="Editar " data-original-title="Edit"
                                            href="{{ route('compras.edit', $compra->id) }}"><i class="ri-pencil-line mr-0"></i>
                                        </a> --}}
                                        {{-- <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"
                                        href="{{ route('compras.edit', $compra->id) }}"><i class="ri-pencil-line mr-0"></i>
                                        </a> --}}
                                        {{-- @if($traspaso->estado == 'solicitado') --}}
                                                {{-- <button type="submit" class="btn btn-warning mr-2 border-none" onclick="return confirm('Are you sure you want to delete this record?')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cancelar Traspaso"><i class="ri-delete-bin-line mr-0"></i></button> --}}
                                        {{-- @endif --}}
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No se encontraron compras a proveedores.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $compras->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
