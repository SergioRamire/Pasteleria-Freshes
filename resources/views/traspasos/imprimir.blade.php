<!doctype html>
<html lang="es">
@php
    setlocale(LC_TIME, 'es_ES.UTF-8');
    \Carbon\Carbon::setLocale('es');
    $fecha_actual = \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y');
@endphp
<head>
    <meta charset="utf-8">
    <title>Reporte de Traspaso de Material</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="{{ asset('assets/images/logo/logo-min.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/remixicon/fonts/remixicon.css') }}">

    <style>
    body {
        font-family: 'Arial', sans-serif;
        background: #fff;
        padding: 30px;
        font-size: 13px;
        line-height: 1.4;
        color: #000;
    }
    .wrapper {
        max-width: 900px;
        margin: auto;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .card-header {
        background: #0056b3;
        color: #fff;
        padding: 15px;
        border-radius: 6px 6px 0 0;
        text-align: center;
    }
    .card-header h4 {
        margin: 0;
        font-weight: bold;
    }
    .logo-invoice {
        height: 60px;
        margin-bottom: 10px;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .table th,
    .table td {
        border: 1px solid #ccc;
        padding: 6px;
        text-align: center;
        font-size: 12px;
        vertical-align: middle;
    }
    .table th {
        background: #f2f2f2;
        text-transform: uppercase;
    }
    .signature {
        margin-top: 60px;
        text-align: center;
    }
    .signature .line {
        width: 200px;
        border-top: 1px solid #000;
        margin: 0 auto 8px;
    }
    .avatar-60 {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }

        body {
            margin: 0;
            padding: 0;
            font-size: 11px;
        }

        .wrapper {
            width: 100%;
            margin: 0;
            padding: 0;
            box-shadow: none;
            border: none;
        }

        .table th,
        .table td {
            padding: 4px;
            font-size: 10px;
        }

        .card-header {
            font-size: 14px;
        }
    }
    </style>

</head>
<body>
<div class="wrapper">
    <div class="card-header text-center">
        <h4>Reporte de Traspaso de Material</h4>
    </div>
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <img src="{{ asset('assets/images/logo/logo.png') }}" class="logo-invoice mb-2">
            <p class="text-muted">Generado el {{ $fecha_actual }}</p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $traspaso->codigo }}</td>
                    <td>{{ $traspaso->fecha }}</td>
                    <td>{{ $traspaso->hora }}</td>
                    <td>{{ ucfirst($traspaso->estado) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th>Sucursal Origen</th>
                    <th>Sucursal Destino</th>
                    <th>Responsable</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $traspaso->sucursal_origen_nombre }}</td>
                    <td>{{ $traspaso->sucursal_destino_nombre }}</td>
                    <td>{{ $traspaso->responsable }}</td>
                </tr>
            </tbody>
        </table>

        <h5 class="text-center mb-3">Productos Solicitados</h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    {{--
                    <th class="text-center">Imagen</th>
                    --}}
                    <th>Producto</th>
                    <th class="text-center">Código</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">Unidad</th>
                    <th class="text-center">Precio U.</th>
                    <th class="text-center">Imp. Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos_traspasos as $productos_traspaso)
                    <tr>
                        <td class="text-center">
                            {{ $loop->iteration }}
                        </td>
                        {{-- <td class="text-center">
                            <img class="avatar-60 rounded" src="{{ $productos_traspaso->product_image ? asset('storage/products/'.$productos_traspaso->product_image) : asset('assets/images/noimage.png') }}" alt="imagen">
                        </td> --}}
                        <td>{{ ucfirst($productos_traspaso->producto) }}</td>
                        <td class="text-center">{{ $productos_traspaso->product_code }}</td>
                        <td class="text-center">{{ $productos_traspaso->cantidad }}</td>
                        <td class="text-center">
                            {{ $productos_traspaso->unidad ? ucfirst(strtolower($productos_traspaso->unidad)) : 'Sin unidad' }}
                        </td>
                        <td class="text-center">${{ number_format($productos_traspaso->selling_price, 2) }}</td>
                        <td class="text-center" style="font-weight: bold;">
                            ${{ number_format($productos_traspaso->cantidad * $productos_traspaso->selling_price, 2, '.', ',') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No se encontraron productos en este traspaso.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @php
            $total = $productos_traspasos->sum(function($item) {
                return round($item->cantidad * $item->selling_price, 2);
            });
        @endphp

        <div style="text-align: right; margin-top: 20px; padding-right: 20px;">
            <h4><strong>Total General: ${{ number_format($total, 2, '.', ',') }}</strong></h4>
        </div>

        <div class="signature">
            <div class="line"></div>
            <p><strong>{{ $traspaso->responsable }}</strong></p>
            <p>Firma del Responsable</p>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', () => window.print());
</script>
</body>
</html>
