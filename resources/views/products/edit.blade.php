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

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Editar Producto</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <!-- Imagen actual -->
                            <div class="col-md-12 text-center">
                                <img class="rounded img-thumbnail" id="image-preview"
                                     src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/images/product/default.webp') }}"
                                     style="max-width: 200px;" alt="Imagen del producto">
                            </div>

                            <!-- Subir nueva imagen -->
                            <div class="col-md-12 center">
                                <label for="product_image" class="form-label"><i class="ri-image-line me-1"></i> Imagen del producto</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('product_image') is-invalid @enderror"
                                           id="image" name="product_image" accept="image/*" onchange="previewImage();">
                                    <label class="custom-file-label" for="product_image">Elegir archivo...</label>
                                </div>
                                @error('product_image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Código de Barras -->
                            {{-- <div class="form-group col-md-6">
                                <label for="product_code">Código del producto</label>
                                <input type="text" class="form-control @error('product_code') is-invalid @enderror"
                                       id="product_code" name="codigo_barras"
                                       value="{{ old('product_code', $product->product_code ?? '') }}"
                                       >
                                @error('product_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}
                             <div class="form-group col-md-6">
                                <label for="product_code"><i class="ri-qr-code-line me-1"></i> Código de producto</label>
                                <input type="text"
                                    class="form-control @error('product_code') is-invalid @enderror"
                                    id="product_code"
                                    name="product_code"
                                    pattern="^[A-Za-z0-9\-]{4,7}$"
                                    maxlength="7"
                                    minlength="4"
                                    title="El código debe tener entre 4 y 7 caracteres: letras, números o guiones"
                                    value="{{ old('product_code', $product->product_code ?? '') }}"
                                    required
                                    oninput="this.value = this.value.replace(/[^A-Za-z0-9\-]/g, '').slice(0, 7)">
                                @error('product_code')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- <div class="form-group col-md-6">
                                <label for="codigo_barras">Código de Barras</label>
                                <input type="text" class="form-control @error('codigo_barras') is-invalid @enderror"
                                       id="codigo_barras" name="codigo_barras"
                                       value="{{ old('codigo_barras', $product->codigo_barras ?? '') }}"
                                       placeholder="Ej. 7501031311309">
                                @error('codigo_barras')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}
                            <div class="form-group col-md-6">
                                    <label for="codigo_barras"><i class="ri-barcode-line me-1"></i> Código de barras</label>
                                    <input type="text"
                                        class="form-control @error('codigo_barras') is-invalid @enderror"
                                        id="codigo_barras"
                                        name="codigo_barras"
                                        pattern="\d{13}"
                                        maxlength="13"
                                        title="El código de barras debe tener exactamente 13 dígitos"
                                        value="{{ old('codigo_barras', $product->codigo_barras ?? '') }}"
                                        required
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)">
                                    @error('codigo_barras')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                            </div>

                            <!-- Nombre del producto -->
                            <div class="form-group col-md-12">
                                <label for="product_name"><i class="ri-price-tag-3-line me-1"></i> Nombre del Producto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('product_name') is-invalid @enderror"
                                       id="product_name" name="product_name"
                                       value="{{ old('product_name', $product->product_name) }}" required>
                                @error('product_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Categoría -->
                            <div class="form-group col-md-4">
                                <label for="category_id"><i class="ri-archive-line me-1"></i> Categoría <span class="text-danger">*</span></label>
                                <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" required>
                                    <option value="" disabled selected hidden>Seleccione una categoria...</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Marca -->
                            <div class="form-group col-md-4">
                                <label for="marca_id"><i class="ri-shopping-bag-line me-1"></i> Marca <span class="text-danger">*</span></label>
                                <select class="form-control @error('marca_id') is-invalid @enderror" name="marca_id" required>
                                    <option value="" disabled selected hidden>Seleccione una marca...</option>
                                    @foreach ($marcas as $marca)
                                        <option value="{{ $marca->id }}" {{ old('marca_id', $product->marca_id) == $marca->id ? 'selected' : '' }}>
                                            {{ $marca->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('marca_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="equivalencia_id"><i class="fa-solid fa-balance-scale me-1"></i> Unidad de Medida <span class="text-danger">*</span></label>
                                <select class="form-control @error('equivalencia_id') is-invalid @enderror" name="equivalencia_id" required>
                                    <option value="" disabled selected hidden>Seleccione una marca...</option>
                                    @foreach ($equivanecias as $equivanecia)
                                        <option value="{{ $equivanecia->id }}" {{ old('equivalencia_id', $product->equivalencia_id) == $equivanecia->id ? 'selected' : '' }}>
                                            {{ $equivanecia->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('equivalencia_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div class="form-group col-md-4">
                                <label for="status_product"><i class="ri-repeat-line me-1"></i> Estado</label>
                                <select name="status_product" id="status_product" class="form-control @error('status_product') is-invalid @enderror" required>
                                    <option value="1" {{ old('status_product', $product->status_product) == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('status_product', $product->status_product) == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('status_product')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @php
                                use Carbon\Carbon;
                                 $today = now()->timezone('America/Mexico_City')->toDateString();
                                // $minBuyingDate =  Carbon::wwoday()->format('Y-m-d'); // Opcional
                            @endphp

                            <!-- Fechas -->
                            <div class="form-group col-md-4">
                                <label for="buying_date"><i class="ri-calendar-check-line me-1"></i> Fecha de compra</label>
                                <input type="date" id="buying_date" name="buying_date" min="{{ $product->buying_date}}" max="{{$today}}" class="form-control @error('buying_date') is-invalid @enderror"
                                       value="{{ old('buying_date', $product->buying_date) }}" />
                                @error('buying_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="expire_date"><i class="ri-timer-2-line me-1"></i> Fecha de caducidad</label>
                                <input type="date" id="expire_date" name="expire_date" class="form-control @error('expire_date') is-invalid @enderror"
                                       value="{{ old('expire_date', $product->expire_date) }}" />
                                @error('expire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                        <hr>
                        <h5 class="mb-3">Datos del precio</h5>

                        <div class="row">
                             <!-- Precios -->
                           <div class="form-group col-md-3">
                                <label for="buying_price"><i class="ri-money-dollar-circle-line me-1"></i> Precio de compra <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('buying_price') is-invalid @enderror"
                                    id="buying_price" name="buying_price" min="0" value="{{ old('buying_price', $product->buying_price) }}" required>
                                @error('buying_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="iva_percent"><i class="ri-bar-chart-line me-1"></i> IVA (%)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="iva_percent" value="16">
                            </div>

                            <div class="form-group col-md-3">
                                <label for="ganancia_percent"><i class="ri-line-chart-line me-1"></i> Ganancia (%)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="ganancia_percent" value="19">
                            </div>

                            <div class="form-group col-md-3">
                                <label for="selling_price"><i class="ri-price-tag-3-line me-1"></i> Precio de venta <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror"
                                    id="selling_price" name="selling_price" min="0" value="{{ old('selling_price', $product->selling_price) }}" required>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="desc_distribuidor"><i class="ri-arrow-down-line me-1"></i> Descuento Mayoreo (%)</label>
                                <input type="number" step="0.01" class="form-control" min="0" id="desc_distribuidor" value="10">

                            </div>

                            <div class="form-group col-md-6">
                                <label for="dealer_price"><i class="ri-store-2-line me-1"></i> Precio Mayorista</label>
                                <input type="number" step="0.01" class="form-control @error('dealer_price') is-invalid @enderror"
                                    id="dealer_price" name="dealer_price" value="{{ old('dealer_price', $product->dealer_price) }}" readonly required>
                                @error('dealer_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <a href="{{ route('products.index') }}" class="btn btn-danger text-white">
                                    <i class="ri-close-line me-1"></i> Cancelar
                                </a>
                            </div>

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

<script>
    // $('#buying_date').datepicker({
    //     uiLibrary: 'bootstrap4',
    //     format: 'yyyy-mm-dd'
    // });
    // $('#expire_date').datepicker({
    //     uiLibrary: 'bootstrap4',
    //     format: 'yyyy-mm-dd'
    // });

    document.addEventListener('DOMContentLoaded', function () {
    const buyingInput = document.getElementById('buying_price');
    const ivaInput = document.getElementById('iva_percent');
    const gananciaInput = document.getElementById('ganancia_percent');
    const distInput = document.getElementById('desc_distribuidor');
    const sellingInput = document.getElementById('selling_price');
    const dealerInput = document.getElementById('dealer_price');

    function calcularPrecios() {
        const compra = parseFloat(buyingInput.value) || 0;
        const iva = parseFloat(ivaInput.value) || 0;
        const ganancia = parseFloat(gananciaInput.value) || 0;
        const descuentoDistribuidor = parseFloat(distInput.value) || 0;

        const precioConIVA = compra * (1 + iva / 100);
        const precioVenta = precioConIVA * (1 + ganancia / 100);
        const precioDistribuidor = precioVenta * (1 - descuentoDistribuidor / 100);

        sellingInput.value = precioVenta.toFixed(2);
        dealerInput.value = precioDistribuidor.toFixed(2);
    }

    // Recalcular al cambiar cualquier valor
    [buyingInput, ivaInput, gananciaInput, distInput].forEach(input => {
        input.addEventListener('input', calcularPrecios);
    });

    calcularPrecios(); // Cálculo inicial
});
</script>

@include('components.preview-img-form')
@endsection
