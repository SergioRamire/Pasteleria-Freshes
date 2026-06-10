@php
    use Carbon\Carbon;
    setlocale(LC_TIME, 'es_ES.UTF-8');
    $fecha = Carbon::now()->timezone('America/Mexico_City')->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Pedido</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/logo-min.png') }}">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #333;
        }

        .documento {
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            padding: 25mm 20mm;
        }

        .encabezado {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #004080;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .encabezado img {
            height: 55px;
        }

        .datos-documento {
            text-align: right;
        }

        .datos-documento h1 {
            margin: 0;
            font-size: 18px;
            color: #004080;
        }

        .datos-documento p {
            margin: 2px 0;
            font-size: 11px;
        }

        .cliente {
            margin-bottom: 15px;
        }

        .cliente strong {
            display: inline-block;
            width: 80px;
        }

        h3 {
            margin-top: 25px;
            font-size: 14px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .nota {
            font-size: 10px;
            margin-top: 20px;
            background: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 10px;
            color: #856404;
        }

        .firmas {
            margin-top: 40px;
            font-size: 11px;
            display: flex;
            justify-content: space-between;
        }

        .firmas div {
            width: 45%;
            border-top: 1px solid #000;
            padding-top: 5px;
            text-align: center;
        }

        @media print {
            .documento {
                padding: 15mm;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="documento">
        <div class="encabezado">
            <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo">
            <div class="datos-documento">
                <h1>Solicitud de Pedido</h1>
                <p>Fecha: {{ ucfirst($fecha) }}</p>
                <p>Folio: #{{ $folio ?? 'N/A' }}</p>
            </div>
        </div>

        @if(isset($customer))
            <div class="cliente">
                <p><strong>Cliente:</strong> {{ $customer->name ?? '-' }}</p>
                <p><strong>Dirección:</strong> {{ $customer->address ?? '-' }}</p>
                <p><strong>Teléfono:</strong> {{ $customer->phone ?? '-' }}</p>
                <p><strong>Email:</strong> {{ $customer->email ?? '-' }}</p>
            </div>
        @endif

        <h3>Productos Solicitados a {{ optional($content->first())->proveedor ?? 'Proveedor desconocido' }}</h3>
        <table>
            <thead>
                <tr class="text-center">
                    <th>#</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Producto</th>
                    <th>Marca</th>
                    <th>Código</th>
                    <th>Cód. Barras</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($content as $item)
                    <tr class="text-center">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->cantidad }}</td>
                        <td>{{ $item->unidad ?? 'Sin unidad' }}</td>
                        <td>{{ $item->producto }}</td>
                        <td>{{ $item->marca }}</td>
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->codigo_barras }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="nota">
            <strong>Nota:</strong> Esta solicitud es informativa y no equivale a una orden de compra definitiva. La disponibilidad de productos está sujeta a confirmación. Contacte a su proveedor para validar condiciones de entrega y precios.
        </div>

        <div class="firmas">
            <div>Solicitado por {{ $sucursal->nombre ?? 'Sucursal desconocida' }}</div>
            <div>Vo. Bo. Proveedor</div>
        </div>
    </div>
</body>
</html>
