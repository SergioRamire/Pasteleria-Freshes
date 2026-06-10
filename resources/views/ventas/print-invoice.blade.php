@php
    setlocale(LC_TIME, 'es_ES');
    use Carbon\Carbon;
    //  $hoy = ->toDateString();

    $fecha_actual = Carbon::now()->timezone('America/Mexico_City')->formatLocalized('%A, %d de %B de %Y');
    $total = Cart::subtotal();

    $subtotal = $total / (1 + 0.16);
    $iva = $subtotal * 0.16;
    $fechaVencimiento = Carbon::now()->addDays(3)->locale('es')->isoFormat('dddd DD [de] MMMM [de] YYYY');
@endphp

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>COTIZACIÓN ACUARIO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Estilos -->
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/logo-min.png') }}">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            font-size: 12px;
        }

        .container {
            position: relative;
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            background: white;
            padding: 20px 30px;
            box-sizing: border-box;
            overflow: hidden;
        }

        .watermark {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 36px;
            color: rgba(200, 200, 200, 0.18);
            white-space: nowrap;
            z-index: 0;
            pointer-events: none;
            user-select: none;
        }

        .header {
            background-color: #0077b6;
            color: white;
            padding: 10px 15px;
            border-radius: 6px 6px 0 0;
            font-size: 14px;
            font-weight: bold;
            z-index: 1;
            position: relative;
        }

        .logo {
            max-height: 45px;
            margin: 15px 0 10px;
        }

        h5 {
            margin: 0 0 10px;
            font-size: 13px;
            position: relative;
            z-index: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
            z-index: 1;
            position: relative;
        }

        th, td {
            padding: 6px 6px;
            text-align: left;
            border-bottom: 1px solid #e3e3e3;
        }

        thead th {
            background-color: #f1f1f1;
            font-weight: 600;
            color: #333;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
            border-radius: 15px;
            padding: 2px 8px;
            font-size: 10px;
        }

        .totals-box {
            float: right;
            width: 260px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 12px;
            margin-top: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            background: #fafafa;
            z-index: 1;
            position: relative;
        }

        .totals-box h5 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .totals-box td {
            padding: 4px 0;
        }

        .total {
            font-size: 14px;
            color: #007bff;
            font-weight: bold;
            text-align: right;
        }

        .note {
            font-size: 10px;
            margin-top: 25px;
            background: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 10px;
            border-radius: 6px;
            color: #856404;
            z-index: 1;
            position: relative;
        }

        @media print {
            body {
                background: white;
            }

            .container {
                width: auto;
                padding: 15px;
                box-shadow: none;
            }

            .watermark {
                font-size: 48px;
            }
        }
    </style>
</head>

<body onload="window.print()">
<div class="container">
    <div class="watermark">Cotización No Comprobante De Venta</div>

    <div class="header">N° 250028</div>
    <img src="{{ asset('assets/images/logo/logo.png') }}" class="logo" alt="Logo">
    <h5>Hola, {{ $customer->name }}</h5>

    <table>
        <thead>
            <tr>
                <th>Fecha de Orden</th>
                <th>Estado del Pedido</th>
                <th>Nota N°</th>
                <th>Datos del Cliente</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ ucfirst(Carbon::now()->timezone('America/Mexico_City')->locale('es')->isoFormat('ddd DD [de] MMMM [de] YYYY')) }}</td>

                <td><span class="badge badge-danger">No pagado</span></td>
                <td>250028</td>
                <td>
                    <p class="mb-0">{{ $customer->address }}<br>
                    Nombre: {{ $customer->name ?? '-' }}<br>
                    Teléfono: {{ $customer->phone }}<br>
                    Email: {{ $customer->email }}</p>
                </td>
            </tr>
        </tbody>
    </table>

    <h5>Resumen del pedido</h5>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Artículo</th>
                <th class="text-center">Cantidad</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($content as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->options->product_code ?? 'N/A' }}</td>
                    <td>{{ $item->name }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-center">${{ number_format($item->price, 2) }}</td>
                    <td class="text-center">${{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="note">
        <strong>Nota:</strong><br>
        La presente cotización se emite únicamente con fines informativos y no constituye un documento fiscal.<br>
        Los precios, productos y condiciones aquí descritos están sujetos a cambios sin previo aviso y deberán confirmarse al momento de realizar la compra.<br>
        Este documento no tiene validez oficial ni valor fiscal y no sustituye una factura o comprobante fiscal digital (CFDI).
    </div>

    <div class="totals-box">
        <h5>Detalles del pedido</h5>
        <table>
            <tr>
                <td>Sub Total:</td>
                <td style="text-align: right;">${{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>IVA (16%):</td>
                <td style="text-align: right;">${{ number_format($iva, 2) }}</td>
            </tr>
            <tr>
                <td>Fecha de vencimiento:</td>
                <td style="text-align: right;">{{ ucfirst($fechaVencimiento) }}</td>
            </tr>
        </table>
        <hr>
        <div class="total">Total: ${{ number_format($total, 2) }}</div>
    </div>

    <div style="clear: both;"></div>
</div>
</body>
</html>
