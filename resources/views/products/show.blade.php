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
                    <div class="header-title">
                        <h4 class="card-title">Código de barras</h4>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row align-items-center">
                        <!-- Código de barras (texto) -->
                        <div class="form-group col-md-6">
                            <label for="codigo_barras"><i class="ri-barcode-line me-1"></i> Código de barras</label>
                            <input type="text" id="codigo_barras" class="form-control" value="{{ $product->codigo_barras }}" readonly>
                        </div>

                        <!-- Código de barras (imagen/render) -->
                        <div class="form-group col-md-6">
                            <label><i class="ri-barcode-box-line me-1"></i> Código de barras generado</label>
                                {!! $barcode !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Producto de información</h4>
                    </div>
                </div>

                <div class="card-body">
                    <!-- begin: Show Data -->
                    <div class="form-group row align-items-center">
                        <div class="col-md-12">
                            <div class="profile-img-edit">
                                <div class="crm-profile-img-edit">
                                    <img class="crm-profile-pic img-thumbnail avatar-130" id="image-preview" src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/images/product/default.webp') }}" alt="profile-pic">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=" row align-items-center">
                        <div class="form-group col-md-12">
                            <label><i class="ri-price-tag-3-line me-1"></i> Nombre del Producto</label>
                            <input type="text" class="form-control" value="{{  $product->product_name }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="product_code"><i class="ri-qr-code-line me-1"></i> Código de producto</label>
                            <input type="text" class="form-control" value="{{  $product->product_code }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label><i class="ri-archive-line me-1"></i> Categoría</label>
                            <input type="text" class="form-control" value="{{  $product->category->name }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label><i class="ri-shopping-bag-line me-1"></i> Marca</label>
                            <input type="text" class="form-control" value="{{  $product->marca->nombre }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label><i class="fa-solid fa-balance-scale me-1"></i> Unidad de Medida</label>
                            <input type="text" class="form-control" value="{{  $product->equivalencia->nombre ?? 'Sin Unidad' }}" readonly>
                        </div>

                        <div class="form-group col-md-4">
                            <label><i class="ri-numbers-line me-1"></i> Clave SAT</label>
                            <input type="text" class="form-control" value="{{ $product->satclave->c_ClaveProdServ ?? 'Sin código SAT' }}"readonly>
                        </div>

                        <div class="form-group col-md-6">
                            <label><i class="ri-calendar-check-line me-1"></i> Fecha de compra</label>
                            <input class="form-control" value="{{ $product->buying_date }}" readonly/>
                        </div>
                        <div class="form-group col-md-6">
                            <label><i class="ri-timer-2-line me-1"></i> Fecha de caducidad</label>
                            <input class="form-control" value="{{ $product->expire_date }}" readonly />
                        </div>
                        <div class="form-group col-md-4">
                            <label><i class="ri-money-dollar-circle-line me-1"></i> Precio de compra</label>
                            <input type="text" class="form-control" value=" {{"$". $product->buying_price }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label><i class="ri-price-tag-3-line me-1"></i> Precio de venta </label>
                            <input type="text" class="form-control" value="{{"$".  $product->selling_price }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label><i class="ri-store-2-line me-1"></i> Precio Mayorista</label>
                            <input type="text" class="form-control" value="{{"$".  $product->dealer_price }}" readonly>
                        </div>
                    </div>
                        <div class="mt-3">
                            <a href="{{ route('products.index') }}" class="btn btn-danger"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
                        </div>
                    <!-- end: Show Data -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

<script>
    $('#buying_date').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd'
        // https://gijgo.com/datetimepicker/configuration/format
    });
    $('#expire_date').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd'
        // https://gijgo.com/datetimepicker/configuration/format
    });
</script>

@include('components.preview-img-form')
@endsection
