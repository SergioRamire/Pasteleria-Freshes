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
                    <h3 class="mb-3">Productos
                        <i class="fas fa-info-circle text-primary"
                        data-toggle="tooltip"
                        data-placement="right"
                        title="Consulta, registra y administra los productos disponibles para la venta, incluyendo sus características, precios y unidades de medida.">
                        </i>
                    </h3>
                </div>
                <div>
                <a href="{{ route('products.create') }}" class="btn btn-primary add-list"><b>+ </b>Crear Producto</a>
                </div>
            </div>
        </div>

        {{-- Filtros y busqueda --}}
        <div class="col-lg-12 mb-3">
            <form action="{{ route('products.index') }}" method="get">
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

                    <div class="form-group col-md-3">
                        <label for="category_id">
                            <i class="ri-equalizer-line me-1"></i> Categoría
                        </label>
                        <select name="category_id" id="category_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @if(request('category_id') == $category->id) selected @endif>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="marca_id">
                            <i class="ri-home-smile-2-line me-1"></i> Marca
                        </label>
                        <select name="marca_id" id="marca_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            @foreach($marcas as $marca)
                                <option value="{{ $marca->id }}" @if(request('marca_id') == $marca->id) selected @endif>{{ $marca->nombre }}</option>
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
                                <a href="{{ route('products.index') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
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
                            <th>N°.</th>
                            <th >Código</th>
                            <th>Foto</th>
                            <th>@sortablelink('codigo_barras', 'Código Barras')</th>
                            <th >@sortablelink('product_name', 'Nombre')</th>
                            <th>@sortablelink('category.name', 'Categoría')</th>
                            <th>@sortablelink('marca.nombre', 'Marca')</th>
                            {{-- <th>@sortablelink('supplier_name', 'Proveedor')</th> --}}
                            <th>@sortablelink('selling_price', 'Precio público')</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @forelse ($products as $product)
                        <tr>
                            <td>{{ (($products->currentPage() * 10) - 10) + $loop->iteration  }}</td>
                            <td>
                               {{ $product->product_code }}
                            </td>
                            <td>
                                <img class="avatar-60 rounded" src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/images/product/default.webp') }}">
                            </td>
                            <td>{{ $product->codigo_barras }}</td>
                            <td>{{ $product->product_name }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>{{ $product->marca->nombre }}</td>
                            {{-- <td>{{ $product->supplier_name }}</td> --}}
                            <td class="text-center">${{ number_format($product->selling_price, 2) }}</td>
                            <td>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="margin-bottom: 5px">
                                    @method('delete')
                                    @csrf
                                    <div class="d-flex align-items-center list-action">
                                        <a class="btn btn-info mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver"
                                            href="{{ route('products.show', $product->id) }}"><i class="ri-eye-line mr-0"></i>
                                        </a>
                                        <a class="btn btn-success mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar"
                                            href="{{ route('products.edit', $product->id) }}""><i class="ri-pencil-line mr-0"></i>
                                        </a>

                                        @php
                                            $count = App\Models\Inventario::where('product_id', $product->id)->count();
                                        @endphp

                                        @if ($count == 0)
                                            <button type="submit" class="btn btn-warning mr-2 border-none" onclick="return confirm('¿Estás seguro de que quieres eliminar este registro? Esta acción no se puede deshacer.')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Eliminar"><i class="ri-delete-bin-line mr-0"></i></button>
                                        @endif

                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">Tipo de dato producto no encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $products->links() }}
        </div>
    </div>
    <!-- Page end  -->
</div>

@endsection
