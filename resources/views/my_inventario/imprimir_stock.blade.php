<!doctype html>
<html lang="es">
@php
    setlocale(LC_TIME, 'es_ES.UTF-8');
    \Carbon\Carbon::setLocale('es');
    $fecha_actual = \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y');
@endphp
<head>
    <meta charset="utf-8">
    <title>Reporte de inventario en la sucursal {{ $sucursal->nombre }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Estilos originales del sistema -->
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/logo-min.png') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/backend-plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/backend.css?v=1.0.0') }}">
    <link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/remixicon/fonts/remixicon.css') }}">

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            padding: 30px;
        }
        h5 {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #007bff;
        }
        table th {
            background: #f7f7f7;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #dee2e6;
        }
        .provider-section {
            margin-bottom: 3rem;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container-fluid">
        <div class="card card-block">
            <div class="card-header d-flex justify-content-between">
                <div class="iq-header-title">
                    <h4 class="card-title mb-0">Reporte de productos con stock mínimo en la sucursal {{ $sucursal->nombre }}</h4>
                    <p>Se muestran los productos cuyo stock es menor o igual al stock mínimo.</p>
                </div>
            </div>
            <div class="card-body">
                <!-- Logo y saludo -->
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <img src="{{ asset('assets/images/logo/logo.png') }}" class="logo-invoice img-fluid mb-3" style="max-height: 80px;">
                        <h5>Reporte generado el {{ $fecha_actual }}</h5>
                    </div>
                </div>

                <!-- Productos agrupados por proveedor -->
                @php
                    $agrupado = $query->groupBy('proveedor_id');
                @endphp

                @forelse ($agrupado as $proveedor_id => $productos)
                    @php
                        $proveedor = \App\Models\Supplier::find($proveedor_id);
                    @endphp

                    <div class="provider-section">
                        <h5 class="mt-4">Proveedor: {{ $proveedor->name ?? 'Proveedor no especificado' }}</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Producto</th>
                                        <th>Código de Producto</th>
                                        <th>Código de Barras</th>
                                        <th>Stock Minimo</th>
                                        <th>Existencias</th>
                                        <th>Marca</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($productos as $producto)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $producto->producto }}</td>
                                            <td>{{ $producto->product_code }}</td>
                                            <td>{{ $producto->codigo_barras }}</td>
                                            <td>{{ $producto->stock_minimo }}</td>
                                            <td>{{ $producto->stock }}</td>
                                            <td>{{ $producto->marca_nombre ?? 'Sin marca' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-right font-weight-bold">Total productos con stock mínimo:</td>
                                        <td colspan="2">{{ $productos->count() }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @empty
                    <p class="text-center mt-4">No hay productos con stock mínimo registrados.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener("load", function () {
        window.print();
    });
</script>
</body>
</html>
