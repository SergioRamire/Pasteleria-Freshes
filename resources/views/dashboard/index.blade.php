@extends('dashboard.body.main')

@section('container')
<div class="container-fluid py-4">

    {{-- ALERTAS --}}
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
                <div id="alert-success" class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <strong><i class="ri-check-line"></i> Éxito:</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div id="alert-error" class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <strong><i class="ri-close-line"></i> Error:</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    {{-- BIENVENIDA --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded bg-white p-4">
                <h3 class="mb-2">👋 Hola, <strong>{{ auth()->user()->name }}</strong></h3>
                <p class="text-muted">Este panel te muestra un resumen actualizado del desempeño de tu ferretera.</p>
            </div>
        </div>
    </div>

    {{-- TARJETAS RESUMEN --}}
    <div class="row">
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm border-0 text-white bg-primary">
                <div class="card-body">
                    <h6>Total en Caja</h6>
                    <h4 class="fw-bold">${{ number_format($total_paid, 2) }}</h4>
                    <i class="ri-wallet-3-line ri-xl float-end"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm border-0 text-white bg-danger">
                <div class="card-body">
                    <h6>Total Retiros</h6>
                    <h4 class="fw-bold">${{ number_format($total_due, 2) }}</h4>
                    <i class="ri-money-dollar-circle-line ri-xl float-end"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm border-0 text-white bg-success">
                <div class="card-body">
                    <h6>Pedidos del Día</h6>
                    <h4 class="fw-bold">{{ count($complete_orders) }}</h4>
                    <i class="ri-truck-line ri-xl float-end"></i>
                </div>
            </div>
        </div>

    {{--
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card shadow-sm border-0 text-white bg-warning">
                <div class="card-body">
                    <h6>Productos Bajo Stock</h6>
                    <h4 class="fw-bold">{{ count($low_stock_products) }}</h4>
                    <i class="ri-alert-line ri-xl float-end"></i>
                </div>
            </div>
        </div>
    --}}

    </div>

    {{-- GRÁFICAS --}}
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📈 Descripción General</h5>
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">Este mes</button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Año</a>
                        <a class="dropdown-item" href="#">Mes</a>
                        <a class="dropdown-item" href="#">Semana</a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="layout1-chart1" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">💰 Ingresos vs Costos</h5>
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">Este mes</button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Año</a>
                        <a class="dropdown-item" href="#">Mes</a>
                        <a class="dropdown-item" href="#">Semana</a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="layout1-chart-2" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- PRODUCTOS PRINCIPALES --}}
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow border-0 rounded-lg">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">⭐ Productos Principales</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($products as $product)
                            <div class="col-md-4 mb-4">
                                <div class="card border-0 shadow-sm h-100 text-center">
                                    <img src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/images/product/default.webp') }}"
                                         class="card-img-top p-3" style="height: 140px; object-fit: contain;">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1">{{ $product->product_name }}</h6>
                                        <p class="text-muted mb-0">{{ $product->product_store }} Artículos</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- NUEVOS PRODUCTOS --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0 rounded-lg">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🆕 Nuevos Productos</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">Ver todo</a>
                </div>
                <div class="card-body">
                    @foreach ($new_products as $product)
                            <div class="media mb-3">
                                <img src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/images/product/default.webp') }}"
                                 class="mr-3 rounded" width="64" height="64" style="object-fit: contain;">
                            <div class="media-body">
                                <h6 class="mt-0">{{ $product->product_name }}</h6>
                                <p class="text-muted mb-0">Stock: {{ $product->product_store }}</p>
                                <small class="text-muted">Precio: ${{ $product->selling_price }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('specificpagescripts')
<script src="{{ asset('assets/js/chart-custom.js') }}"></script>
<script src="{{ asset('assets/js/customizer.js') }}"></script>
@endsection
