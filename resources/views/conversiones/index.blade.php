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
                    <h3 class="mb-3">Conversiones
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Esta función permite registrar y vender productos que no se manejan en piezas exactas, sino en cantidades fraccionadas o medidas personalizadas (por ejemplo, kilos, metros o litros).">
                        </i>
                    </h3>
                </div>

            </div>
        </div>

        {{-- Filtros y búsqueda --}}
        <div class="col-lg-12 mb-3">
            <form action="{{ route('conversiones.index') }}" method="get">
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

                    <div class="form-group col-md-4">
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
                    <div class="form-group col-md-3">
                        <label for="unidad_id">
                            <i class="fa-solid fa-balance-scale me-1"></i> Unidad de Medida
                        </label>
                        <select name="unidad_id" id="unidad_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            @foreach($unidades as $unidad)
                                <option value="{{ $unidad->id }}" @if(request('unidad_id') == $unidad->id) selected @endif>
                                    {{ $unidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="search"><i class="ri-search-line"></i> Buscar</label>
                        <div class="input-group">
                            <input type="text" id="search" class="form-control" name="search" placeholder="Buscar por Producto, Código o Código de barras" value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                <a href="{{ route('conversiones.index') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
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
                            <th class="text-center">Código</th>
                            <th class="text-center">Foto</th>
                            <th>@sortablelink('producto', 'Producto')</th>
                            {{--<th>@sortablelink('codigo_barras', 'Código barras')</th>--}}
                            <th class="text-center">Unidad</th>
                            <th>Categoría</th>
                            <th class="text-center">Marca</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @forelse ($inventarios as $inventario)
                            <tr>
                                <td class="text-center">{{ ($inventarios->currentPage() - 1) * $inventarios->perPage() + $loop->iteration }}</td>
                                <td class="text-center">{{ $inventario->product_code }}</td>
                                <td class="text-center">
                                    <img class="avatar-60 rounded" src="{{ $inventario->product_image ? asset('storage/products/'.$inventario->product_image) : asset('assets/images/product/default.webp') }}">
                                </td>
                                <td>{{ $inventario->producto }}</td>
                                {{--<td>{{ $inventario->codigo_barras }}</td>--}}
                                <td class="text-center">{{ $inventario->unidad ? $inventario->unidad : 'Sin unidad'}}</td>
                                <td>{{ $inventario->category_name }}</td>
                                <td class="text-center">{{ $inventario->marca_nombre }}</td>
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
                                <td class="text-center">
                                    <div class="d-flex align-items-center list-action">
                                        {{-- Botón Convertir Producto --}}
                                        @can('conversiones.crear')
                                            <a class="badge bg-success mr-2"
                                            style="font-size: 0.95rem; padding: 0.5em 0.75em;"
                                            data-toggle="tooltip"
                                            title="Convertir Producto"
                                            href="{{ route('conversiones.crear', $inventario->id) }}">
                                                <i class="ri-shuffle-line"></i>
                                            </a>
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

@endsection
