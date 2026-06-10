@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Agregar Producto a Sucursal</h4>
                    </div>
                </div>
                
                <div class="card-body">
                    {{-- FORMULARIO FILTROS --}}
                    <form action="{{ route('inventarios.create') }}" method="GET">
                        <div class="row d-flex align-items-end justify-content-between mb-3">

                            <!-- Fila -->
                            <div class="form-group col-md-3 mb-2">
                                <label for="row" class="form-label">
                                    <i class="ri-align-justify"></i> Filas por página
                                </label>
                                <select class="form-control" name="row" id="row">
                                    <option value="10" @if(request('row') == '10') selected @endif>10</option>
                                    <option value="25" @if(request('row') == '25') selected @endif>25</option>
                                    <option value="50" @if(request('row') == '50') selected @endif>50</option>
                                    <option value="100" @if(request('row') == '100') selected @endif>100</option>
                                </select>
                            </div>

                            <!-- Categoría -->
                            <div class="form-group col-md-3 mb-2">
                                <label for="category_id" class="form-label">
                                    <i class="ri-archive-line"></i> Categoría
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

                            <!-- Marcas -->
                            <div class="form-group col-md-3 mb-2">
                                <label for="marca_id" class="form-label">
                                    <i class="ri-price-tag-3-line"></i> Marcas
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

                            <!-- Proveedor -->
                            <div class="form-group col-md-3 mb-2">
                                <label for="proveedor_id" class="form-label">
                                    <i class="ri-truck-line"></i> Proveedor
                                </label>
                                <select name="proveedor_id" id="proveedor_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todos</option>
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}" @if(request('proveedor_id') == $proveedor->id) selected @endif>
                                            {{ $proveedor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- BUSCAR --}}
                            <div class="form-group col-md-12 mb-2">
                                <label for="search"><i class="ri-search-line"></i> Buscar Producto</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-barcode"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="search" class="form-control" name="search" placeholder="Buscar producto" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                        <a href="{{ route('inventarios.create') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form> {{-- ← cierre correcto del primer formulario --}}

                    {{-- FORMULARIO POST PARA GUARDAR PRODUCTO + SUCURSAL --}}
                    <form action="{{ route('inventarios.store') }}" method="POST">
                        @csrf

                        <div class="table-responsive rounded mb-3">
                            <table class="table mb-0">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th>N°.</th>
                                        <th>Foto</th>
                                        <th>Codigo Barras</th>
                                        <th>@sortablelink('product_name', 'Nombre')</th>
                                        <th>@sortablelink('category_name', 'Categoría')</th>
                                        <th>@sortablelink('marca_nombre', 'Marca')</th>
                                        <th>@sortablelink('selling_price', 'Precio')</th>
                                        <th>Estado</th>
                                        <th>Seleccionar</th>
                                    </tr>
                                </thead>
                                <tbody class="ligth-body">
                                    @forelse ($products as $product)
                                    <tr>
                                        <td>{{ (($products->currentPage() - 1) * $products->perPage()) + $loop->iteration }}</td>
                                        <td>
                                            <img class="avatar-60 rounded" src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/images/product/default.webp') }}">
                                        </td>
                                        <td>{{ $product->codigo_barras }}</td>
                                        <td>{{ $product->product_name }}</td>
                                        <td>{{ $product->category_name }}</td>
                                        <td>{{ $product->marca_nombre }}</td>
                                        <td>{{ $product->selling_price }}</td>
                                        <td>
                                            @if ($product->expire_date > now()->format('Y-m-d'))
                                                <span class="badge rounded-pill bg-success">No caducado</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger">Caducado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="producto_id" value="{{ $product->id }}" id="producto_{{ $product->id }}" required>
                                                <label class="form-check-label" for="producto_{{ $product->id }}">Seleccionar</label>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9">
                                            <div class="alert text-white bg-danger" role="alert">
                                                <div class="iq-alert-text">Datos no encontrados</div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $products->links() }}

                        <div class="form-group mt-3">
                            <label for="sucursal_id">
                                <i class="ri-store-2-line me-1"></i> Selecciona la Sucursal:
                            </label>
                            <select name="sucursal_id" id="sucursal_id" class="form-control" required>
                                <option value="" disabled selected hidden>Seleccione una sucursal</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stock_minimo">
                                        <i class="ri-arrow-down-line me-1"></i> Stock mínimo <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('stock_minimo') is-invalid @enderror"
                                        id="stock_minimo" min="0" name="stock_minimo"
                                        value="3" required>
                                    @error('stock_minimo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stock">
                                        <i class="ri-archive-line me-1"></i> Stock <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('stock') is-invalid @enderror"
                                        id="stock" name="stock" min="0"
                                        value="{{ old('stock') }}" required>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <!-- Botón Cancelar -->
                            <div>
                                <a class="btn btn-danger text-white" href="{{ route('inventarios.index') }}">
                                    <i class="ri-close-line me-1"></i> Cancelar
                                </a>
                            </div>
                            <!-- Botón Guardar -->
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.preview-img-form')
@endsection
