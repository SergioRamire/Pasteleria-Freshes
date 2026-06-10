@extends('dashboard.body.main')

@section('specificpagestyles')
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Editar producto de {{ strtolower($invent->nombre_sucursal) }}</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('myinventarios.update', $invent->id_inventario) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 text-center">
                                <img class="rounded img-thumbnail" id="image-preview"
                                     src="{{ $invent->product_image ? asset('storage/products/'.$invent->product_image) : asset('assets/images/product/default.webp') }}"
                                     style="max-width: 200px;" alt="Imagen del producto">
                            </div>

                            <!-- Nombre del producto -->
                            <div class="form-group col-md-6">
                                <label><i class="ri-price-tag-3-line me-1"></i> Nombre del Producto</label>
                                <input type="text" class="form-control @error('product_id') is-invalid @enderror"
                                       id="product_id" name="nombre_producto"
                                       value="{{ old('nombre_producto', $invent->product_name) }}" disabled>
                            </div>

                            <div class="form-group col-md-3">
                                <label><i class="ri-qr-code-line me-1"></i> Código del producto</label>
                                <input type="text" class="form-control @error('product_code') is-invalid @enderror"
                                       id="product_code" name="nombre_producto"
                                       value="{{ old('product_code', $invent->product_code) }}" disabled>
                            </div>

                            <div class="form-group col-md-3">
                                <label><i class="ri-barcode-line me-1"></i> Código de barras</label>
                                <input type="text" class="form-control @error('codigo_barras') is-invalid @enderror"
                                       id="codigo_barras" name="nombre_producto"
                                       value="{{ old('codigo_barras', $invent->codigo_barras) }}" disabled>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="unidad"><i class="ri-calculator-line"></i> Unidad</label>
                                <input type="text" class="form-control @error('unidad') is-invalid @enderror"
                                       name="unidad" value="{{ $invent->unidad ? $invent->unidad : 'Sin unidad' }}" disabled>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="codigo_sat"><i class="fas fa-receipt"></i> Código SAT</label>
                                <input type="text" class="form-control @error('codigo_sat') is-invalid @enderror"
                                       name="codigo_sat" value="{{ $invent->codigo_sat ? $invent->codigo_sat : 'Sin código SAT' }}" disabled>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="marca_nombre"><i class="ri-shopping-bag-line me-1"></i> Marca</span></label>
                                <input type="text" class="form-control @error('marca_nombre') is-invalid @enderror"
                                       id="marca_nombre" name="marca_nombre"
                                       value="{{ old('marca_nombre', $invent->marca_nombre) }}" disabled>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="categoria"><i class="ri-archive-line me-1"></i> Categoría</label>
                                <input type="categoria" class="form-control @error('categoria') is-invalid @enderror"
                                       id="categoria" name="categoria"
                                       value="{{ old('categoria', $invent->categoria) }}" disabled>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="proveedor"><i class="ri-store-2-line me-1"></i> Proveedor</label>
                                <input type="proveedor" class="form-control @error('proveedor') is-invalid @enderror"
                                       id="proveedor" name="proveedor"
                                       value="{{ old('proveedor', $invent->proveedor) }}" disabled>
                            </div>

                            <!-- Almacén -->
                            <div class="form-group col-md-3">
                                <label for="branche_id"><i class="ri-building-line me-1"></i> Sucursal</label>
                                <input type="text" class="form-control @error('branche_id') is-invalid @enderror"
                                       name="nombre_sucursal" value="{{ old('nombre_sucursal', $invent->nombre_sucursal) }}" disabled>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="buying_price"><i class="ri-money-dollar-circle-line me-1"></i> Precio compra</label>
                                <input type="number" class="form-control @error('buying_price') is-invalid @enderror"
                                       id="buying_price" name="buying_price"
                                       value="{{ old('buying_price', $invent->buying_price) }}" disabled>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="selling_price"><i class="ri-price-tag-3-line me-1"></i> Precio Público</label>
                                <input type="number" class="form-control @error('selling_price') is-invalid @enderror"
                                       id="selling_price" name="selling_price"
                                       value="{{ old('selling_price', $invent->selling_price) }}" disabled>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="dealer_price"><i class="ri-shopping-bag-3-line me-1"></i> Precio Mayoreo</label>
                                <input type="number" class="form-control @error('dealer_price') is-invalid @enderror"
                                       id="dealer_price" name="selling_price"
                                       value="{{ old('dealer_price', $invent->dealer_price) }}" disabled>
                            </div>

                            <!-- Stock mínimo -->
                            <div class="form-group col-md-3">
                                <label for="stock_minimo"><i class="ri-arrow-down-line me-1"></i> Stock Mínimo <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control @error('stock_minimo') is-invalid @enderror" required
                                       name="stock_minimo" id="stock_minimo"
                                       value="{{ old('stock_minimo', $invent->stock_minimo) }}">
                                @error('stock_minimo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Existencias -->
                            <div class="form-group col-md-3">
                                <label for="stock"><i class="ri-stack-line me-1"></i> Existencias <span class="text-danger">*</span></label>
                                <input type="number" min="0"
                                    class="form-control {{ old('stock', $invent->stock) <= old('stock_minimo', $invent->stock_minimo) ? 'text-danger' : 'text-success' }} @error('stock') is-invalid @enderror"
                                    name="stock" id="stock"
                                    value="{{ old('stock', $invent->stock) }}" required
                                    style="font-weight: bold;">
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Disponibilidad -->
                            <div class="form-group col-md-3">
                                <label for="disponibilidad"><i class="ri-eye-line me-1"></i> Disponibilidad <span class="text-danger">*</span></label>
                                <select name="disponibilidad" id="disponibilidad" class="form-control @error('disponibilidad') is-invalid @enderror" required>
                                    <option value="1" {{ old('disponibilidad', $invent->disponibilidad) == 1 ? 'selected' : '' }}>Disponible</option>
                                    <option value="0" {{ old('disponibilidad', $invent->disponibilidad) == 0 ? 'selected' : '' }}>No disponible</option>
                                </select>
                                @error('disponibilidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div class="form-group col-md-3">
                                <label for="estado"><i class="ri-repeat-line me-1"></i> Estado <span class="text-danger">*</span></label>
                                <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" required>
                                    <option value="1" {{ old('estado', $invent->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('estado', $invent->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <!-- Botón Cancelar a la derecha -->
                            <div>
                                <a href="{{ route('myinventarios.index') }}" class="btn btn-danger text-white">
                                    <i class="ri-close-line me-1"></i> Cancelar
                                </a>
                            </div>

                            <!-- Botón Guardar a la izquierda -->
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-refresh-line me-1"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stockInput = document.getElementById('stock');
        const disponibilidadSelect = document.getElementById('disponibilidad');

        stockInput.addEventListener('input', function () {
            const stockValue = parseInt(this.value);
            disponibilidadSelect.value = (!isNaN(stockValue) && stockValue > 0) ? "1" : "0";
        });
    });

    $('#buying_date').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd'
    });
    $('#expire_date').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd'
    });
</script>

@include('components.preview-img-form')
@endsection
