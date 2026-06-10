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
                    <div class="header-title">
                        <h4 class="card-title">Agregar producto</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <!-- begin: Input Image -->
                        <div class="form-group row align-items-center">
                            <div class="col-md-12">
                                <div class="profile-img-edit">
                                    <div class="crm-profile-img-edit">
                                        <img class="crm-profile-pic rounded-circle avatar-100" id="image-preview" src="{{ asset('assets/images/product/default.webp') }}" alt="profile-pic">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group mb-4 col-lg-6">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('product_image') is-invalid @enderror" id="image" name="product_image" accept="image/*" onchange="previewImage();">
                                    <label class="custom-file-label" for="product_image">Eliga una imagen</label>
                                </div>
                                @error('product_image')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">

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
                                    value="{{ old('product_code') }}"
                                    oninput="this.value = this.value.replace(/[^A-Za-z0-9\-]/g, '').slice(0, 7)">
                                @error('product_code')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                    <label for="codigo_barras"><i class="ri-barcode-line me-1"></i> Código de barras</label>
                                    <input type="text"
                                        class="form-control @error('codigo_barras') is-invalid @enderror"
                                        id="codigo_barras"
                                        name="codigo_barras"
                                        pattern="\d{13}"
                                        maxlength="13"
                                        title="El código de barras debe tener exactamente 13 dígitos"
                                        value="{{ old('codigo_barras') }}"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)">
                                    @error('codigo_barras')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                            </div>
                        </div>
                        <div class=" row align-items-center">
                            <div class="form-group col-md-12">
                                <label for="product_name"><i class="ri-price-tag-3-line me-1"></i> Nombre del Producto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('product_name') is-invalid @enderror" id="product_name" name="product_name" value="{{ old('product_name') }}" required>
                                @error('product_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="category_id"><i class="ri-archive-line me-1"></i> Categoría <span class="text-danger">*</span></label>
                                <select class="form-control" name="category_id" required>
                                    <option value="" disabled selected hidden>Seleccione una categoria...</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="marca_id"><i class="ri-shopping-bag-line me-1"></i> Marca <span class="text-danger">*</span></label>
                                <select class="form-control" name="marca_id" required>
                                    <option value="" disabled selected hidden>Seleccione una marca...</option>
                                    @foreach ($marcas as $marca)
                                        <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }} }}>{{ $marca->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('marca_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="equivalencia_id"><i class="fa-solid fa-balance-scale me-1"></i> Unidad de Medida <span class="text-danger">*</span></label>
                                <select class="form-control @error('equivalencia_id') is-invalid @enderror" name="equivalencia_id" required>
                                    <option value="" disabled selected hidden>Seleccione una unidad...</option>
                                    @foreach ($equivanecias as $equivanecia)
                                        <option value="{{ $equivanecia->id }}" {{ old('equivalencia_id', $product->equivalencia_id ?? '') == $equivanecia->id ? 'selected' : '' }}>
                                            {{ $equivanecia->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('equivalencia_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @php
                                use Carbon\Carbon;
                                 $today = now()->timezone('America/Mexico_City')->toDateString();
                                $minBuyingDate =  Carbon::today()->subDays(7)->format('Y-m-d');
                                $maxBuyingDate = Carbon::today()->format('Y-m-d'); // Opcional
                            @endphp

                            <div class="form-group col-md-6">
                                <label for="buying_date"><i class="ri-calendar-check-line me-1"></i> Fecha de compra</label>
                                <input type="date"
                                    id="buying_date"
                                    name="buying_date"
                                    class="form-control @error('buying_date') is-invalid @enderror"
                                    value="{{ old('buying_date') }}"
                                    min="{{ $minBuyingDate }}"
                                    max="{{ $today }}"> {{-- puedes quitar max si no lo necesitas --}}
                                @error('buying_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="expire_date"><i class="ri-timer-2-line me-1"></i> Fecha de caducidad</label>
                                <input type="date"
                                    id="expire_date"
                                    name="expire_date"
                                    class="form-control @error('expire_date') is-invalid @enderror"
                                    value="{{ old('expire_date') }}"
                                    min="{{ $today }}"> {{-- puedes quitar max si no lo necesitas --}}
                                @error('expire_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>


                        </div>
                        <hr>
                        <h5 class="mb-3">Datos del precio</h5>
                        <div class="row">

                            <div class="form-group col-md-3">
                                <label for="buying_price"><i class="ri-money-dollar-circle-line me-1"></i> Precio de compra <span class="text-danger">*</span></label>
                                <input type="number"
                                        class="form-control @error('buying_price') is-invalid @enderror"
                                        id="buying_price"
                                        name="buying_price"
                                        value="{{ old('buying_price') }}"
                                        step="1"
                                        min="0"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        required>
                                @error('buying_price')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
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
                                <input type="number" step="0.01" min="0" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price') }}" required>
                                @error('selling_price')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="desc_distribuidor"><i class="ri-arrow-down-line me-1"></i> Descuento Mayoreo (%)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="desc_distribuidor" value="10">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="dealer_price"><i class="ri-store-2-line me-1"></i> Precio Mayorista</label>
                                <input type="number" class="form-control" min="0" id="dealer_price" name="dealer_price" readonly>
                            </div>
                        </div>
                        <!-- end: Input Data -->
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <a class="btn bg-danger text-white" href="{{ route('products.index') }}">
                                <i class="ri-close-line me-1"></i> Cancelar
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

<script>

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

        [buyingInput, ivaInput, gananciaInput, distInput].forEach(input => {
            input.addEventListener('input', calcularPrecios);
        });

        calcularPrecios();
    });

</script>

@include('components.preview-img-form')
@endsection

<!-- jQuery (obligatorio para Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS y CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('#equivalencia_id').select2({
            placeholder: "Selecciona una unidad...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
