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
                    <h4 class="card-title">Ver producto de {{ strtolower($invent->nombre_sucursal) }}</h4>
                </div>

                <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <img class="rounded img-thumbnail" id="image-preview"
                                     src="{{ $invent->product_image ? asset(path: 'storage/products/'.$invent->product_image) : asset('assets/images/product/default.webp') }}"
                                     style="max-width: 200px;" alt="Imagen del producto">
                            </div>

                            <!-- Nombre del producto -->
                            <div class="form-group col-md-6">
                                <label><i class="ri-price-tag-3-line me-1"></i> Nombre del producto</label>
                                <input type="text" class="form-control" value="{{ $invent->nombre_producto }}" disabled>
                            </div>

                            <!-- codigo de producto -->
                            <div class="form-group col-md-4">
                                <label><i class="ri-qr-code-line me-1"></i> Código del producto</label>
                                <input type="text" class="form-control" value="{{ $invent->product_code }}" disabled>
                            </div>

                            <!-- codigo de barras -->
                            <div class="form-group col-md-4">
                                <label><i class="ri-barcode-line me-1"></i> Código de barras</label>
                                <input type="text" class="form-control" value="{{ $invent->codigo_barras }}" disabled>
                            </div>

                            <!-- Categoría -->
                            <div class="form-group col-md-4">
                                <label><i class="ri-building-line me-1"></i> Categoría</label>
                                <input type="text" class="form-control" value="{{ $invent->category_name }}" disabled>
                            </div>

                            <!-- Proveedor -->
                            <div class="form-group col-md-6">
                                <label><i class="ri-building-line me-1"></i> Proveedor</label>
                                <input type="text" class="form-control" value="{{ $invent->proveedor }}" disabled>
                            </div>

                            <!-- Sucursal -->
                            <div class="form-group col-md-6">
                                <label><i class="ri-building-line me-1"></i> Sucursal</label>
                                <input type="text" class="form-control" value="{{ $invent->nombre_sucursal }}" disabled>
                            </div>
                             <div class="form-group col-md-4">
                                <label><i class="ri-money-dollar-circle-line me-1"></i> Precio de compra</label>
                                <input type="text" class="form-control" value=" {{"$". $invent->buying_price }}" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label><i class="ri-price-tag-3-line me-1"></i> Precio de venta </label>
                                <input type="text" class="form-control" value="{{"$".  $invent->selling_price }}" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label><i class="ri-store-2-line me-1"></i> Precio Mayorista</label>
                                <input type="text" class="form-control" value="{{"$".  $invent->dealer_price }}" readonly>
                            </div>
                            <div class="form-group col-md-3">
                                <label><i class="ri-calculator-line"></i> Unidad</label>
                                <input type="text" class="form-control" value="{{ $invent->unidad ? $invent->unidad : 'Sin unidad' }}" readonly>
                            </div>
                            <div class="form-group col-md-3">
                                <label><i class="fas fa-receipt"></i> Código SAT</label>
                                <input type="text" class="form-control" value="{{ $invent->codigo_sat ? $invent->codigo_sat : 'Sin código' }}" readonly>
                            </div>

                            <!-- Stock mínimo -->
                            <div class="form-group col-md-3">
                                <label for="stock_minimo"><i class="ri-arrow-down-line me-1"></i> Stock Mínimo</label>
                                <input type="number" min="0" class="form-control @error('stock_minimo') is-invalid @enderror"
                                       name="stock_minimo" id="stock_minimo"
                                       value="{{ old('stock_minimo', $invent->stock_minimo) }}" disabled>
                                @error('stock_minimo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Existencias -->
                            <div class="form-group col-md-3">
                                <label for="stock"><i class="ri-stack-line me-1"></i> Existencias Actuales</label>
                                <input type="number" min="0"
                                    class="form-control {{ old('stock', $invent->stock) <= $invent->stock_minimo ? 'text-danger' : 'text-success' }} @error('stock') is-invalid @enderror"
                                    name="stock" id="stock"
                                    value="{{ old('stock', $invent->stock) }}"
                                    style="font-weight: bold;" disabled>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Disponibilidad -->
                            <div class="form-group col-md-6">
                                <label for="disponibilidad"><i class="ri-eye-line me-1"></i> Disponibilidad</label>
                                <select name="disponibilidad" id="disponibilidad" class="form-control @error('disponibilidad') is-invalid @enderror" disabled>
                                    <option value="1" {{ old('disponibilidad', $invent->disponibilidad) == 1 ? 'selected' : '' }}>Disponible</option>
                                    <option value="0" {{ old('disponibilidad', $invent->disponibilidad) == 0 ? 'selected' : '' }}>No disponible</option>
                                </select>
                                @error('disponibilidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div class="form-group col-md-6">
                                <label for="estado"><i class="ri-repeat-line me-1"></i> Estado</label>
                                <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" disabled>
                                    <option value="1" {{ old('estado', $invent->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('estado', $invent->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('myinventarios.index') }}" class="btn btn-danger"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
                        </div>
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
