<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TICKET DE VENTA</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/logo-min.png') }}">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            font-weight: bold;
        }
        .ticket {
            width: 55mm;
            padding: 0 0.5mm;
            box-sizing: border-box;
        }
        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 6px 0; }
        .tabla-producto { width: 100%; font-size: 9px; table-layout: fixed; }
        .tabla-producto th,
        .tabla-producto td { padding: 1px 0; vertical-align: top; word-break: break-word; }
        .tabla-producto th:nth-child(1),
        .tabla-producto td:nth-child(1) { width: 18%; text-align: center; }
        /* .tabla-producto th:nth-child(2),
        .tabla-producto td:nth-child(2) { width: 18%; text-align: center; } */
        .tabla-producto th:nth-child(2),
        .tabla-producto td:nth-child(2) { width: 78%; text-align: left; }
        .tabla-producto th:nth-child(3),
        .tabla-producto td:nth-child(3) { width: 30%; text-align: right; }

        .tabla-producto tbody tr:not(:last-child) {
            border-bottom: 1px dashed #ccc;
        }

        .totales { display: flex; justify-content: space-between; margin-top: 4px; font-size: 10px; }
        .info { margin-top: 5px; font-size: 10px; text-align: left; }
        .aviso { font-size: 10px; margin-top: 6px; line-height: 1.4; text-align: justify; }
        .aviso strong { display: block; margin-bottom: 4px; text-align: center; }
        .separador { text-align: center; margin: 1px 0; border-bottom: 2px double #000; padding-bottom: 5px; font-size: 10px; }
        @media print {
            body { margin: 0; }
        }
        .logo-container{
            text-align: center;
            margin-bottom: 8px; /* Espacio pequeño */
            line-height: 0;
        }

        .ticket-logo{
            max-height: 70px;      /* Altura máxima */
            max-width: 100%;       /* Nunca se sale del ticket */
            width: auto;
            height: auto;
            object-fit: contain;
        }
    </style>
</head>

<body onload="window.print(); setTimeout(() => window.location.href='{{ route('ventas.index') }}', 1000)">
    <div id="ticket" class="ticket">
        <div class="logo-container">
            @if(!empty($configNegocio?->logo))
                <img src="{{ asset('storage/' . $configNegocio->logo) }}"
                    class="ticket-logo"
                    alt="Logo">
            @else
                <img src="{{ asset('assets/images/logo/logo.png') }}"
                    class="ticket-logo"
                    alt="Logo">
            @endif
        </div>

        <div class="center">
            <strong> {{ $configNegocio?->nombre_negocio ?? 'AcuarioA.' }}</strong><br>
            R.F.C.: GORA410218TNA<br>
            Sucursal: {{ $sucursal->nombre }}<br>
            {{ $sucursal->direccion }}<br>
            Tel:  {{ $configNegocio?->telefono ?? '951-562-0424.' }} 
        </div>

        <div class="line"></div>

        @php
            $totalProductos = collect($productos)->sum('cantidad');
        @endphp

        <div class="info">
            <strong>Ticket N°:</strong> {{ $venta->invoice_no }}<br>
            <strong>Fecha:</strong> {{ \Carbon\Carbon::now()->timezone('America/Mexico_City')->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}<br>
            <strong>Hora:</strong> {{ \Carbon\Carbon::now()->timezone('America/Mexico_City')->format('h:i A') }}<br>
            <strong>Vendedor:</strong> {{ $cajero->name }}<br>
            <strong>Cliente:</strong> {{ $venta->customer->name ?? $customer->shopname ?? 'Público General' }}<br>
            <strong>Método de Pago:</strong> {{ $venta->metodo_pago }}<br>
            <strong>Total de Productos:</strong> {{ $totalProductos }}
        </div>

        <div class="line"></div>

        <table class="tabla-producto">
            <thead>
                <tr>
                    <th>CANT</th>
                    <!-- <th>UD</th> -->
                    <th>DESCRIPCIÓN</th>
                    <th>IMPORTE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $item)
                <tr>
                    <td>{{ $item->cantidad }}</td>
                    <!-- <td>{{ $item->unidad }}</td> -->
                    <td>{{ $item->producto }}</td>
                    <td>${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $total = $venta->sub_total;
            $subtotal = $total / 1.16;
            $iva = $total - $subtotal;
        @endphp

        <div class="line"></div>
        <div class="totales"><span>Subtotal:</span><span>${{ number_format($subtotal, 2) }}</span></div>
        <div class="totales"><span>IVA (16%):</span><span>${{ number_format($iva, 2) }}</span></div>
        <div class="totales"><span>Total:</span><span>${{ number_format($total, 2) }}</span></div>
        <div class="totales"><span>Pagado:</span><span>${{ number_format($venta->pay, 2) }}</span></div>
        <div class="totales"><span>Cambio:</span><span>${{ number_format(abs($venta->due), 2) }}</span></div>

        <div class="line"></div>
        <div class="aviso"><span>{{ numeroALetras($total) }}</span></div>

        <div class="line"></div>

        <div class="center" style="height: 170px; border: 1px dashed #000; margin: 10px 0; display: flex; align-items: center; justify-content: center;">
            <em></em>
        </div>

        <div class="line"></div>

        <div class="aviso">
            <strong>⚠️ Avisos Importantes</strong>
            - No se aceptan cambios ni devoluciones una vez que el producto ha sido revisado por el cliente y retirado del establecimiento.<br>
            - La factura debe solicitarse al momento de la compra o, a más tardar, el mismo día, comunicándose al <strong>WhatsApp +52 951 142 2928</strong>
            - Después de ese plazo, la operación se considerará como venta al público en general y no podrá ser facturada.
        </div>

        <div class="line"></div>

        <div class="center">
            ¡Gracias por su compra!<br>
            Horario:<br>
            L a V: 8:00 am - 6:00 pm<br>
            S y D: 8:00 am - 5:00 pm<br>
            <!-- * Días festivos no se labora * -->
            <br><br><br><br>.
        </div>

        <div style="height: 60px;"></div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        /*
        // Imprime la primera copia
        window.print();

        // Espera 400 ms y manda la segunda copia
        setTimeout(() => {
            window.print();
        }, 1000);

        // Después de 1.2 seg redirige a ventas
        setTimeout(() => {
            window.location.href = "{{ route('ventas.index') }}";
        }, 1200);
        */
    });
    </script>
</body>
</html>

@php
function numeroALetras($numero) {
    $entero = floor($numero);
    $decimales = round(($numero - $entero) * 100);

    $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
    $decenas = ['', '', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
    $especiales = ['DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
    $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

    if ($entero == 0 && $decimales == 0) return 'CERO PESOS 00/100 M.N.';

    $resultado = '';

    // Manejo de números grandes (hasta 100,000)
    if ($entero >= 1000000) {
        // Esta función maneja hasta millones, pero para tu caso específico de 100,000 no será necesario
        $millones = floor($entero / 1000000);
        if ($millones == 1) {
            $resultado .= 'UN MILLÓN ';
        } else {
            $resultado .= convertirGrupo($millones, $unidades, $decenas, $especiales, $centenas) . ' MILLONES ';
        }
        $entero %= 1000000;
    }

    // Miles
    if ($entero >= 1000) {
        $miles = floor($entero / 1000);
        if ($miles == 1) {
            $resultado .= 'MIL ';
        } else {
            $resultado .= convertirGrupo($miles, $unidades, $decenas, $especiales, $centenas) . ' MIL ';
        }
        $entero %= 1000;
    }

    // Unidades
    if ($entero > 0) {
        $resultado .= convertirGrupo($entero, $unidades, $decenas, $especiales, $centenas) . ' ';
    }

    // Determinar si es PESO o PESOS
    if (floor($numero) == 1) {
        $resultado = trim($resultado) . ' PESO';
    } else {
        $resultado = trim($resultado) . ' PESOS';
    }

    // Agregar centavos en formato 00/100 M.N.
    $resultado .= ' ' . sprintf('%02d', $decimales) . '/100 M.N.';

    return $resultado;
}

function convertirGrupo($numero, $unidades, $decenas, $especiales, $centenas) {
    $resultado = '';

    // Centenas
    if ($numero >= 100) {
        $cent = floor($numero / 100);
        if ($numero == 100) {
            $resultado .= 'CIEN';
        } else {
            $resultado .= $centenas[$cent] . ' ';
        }
        $numero %= 100;
    }

    // Decenas y unidades
    if ($numero >= 20) {
        $dec = floor($numero / 10);
        $uni = $numero % 10;
        $resultado .= $decenas[$dec];
        if ($uni > 0) {
            $resultado .= ' Y ' . $unidades[$uni];
        }
    } elseif ($numero >= 10) {
        $resultado .= $especiales[$numero - 10];
    } elseif ($numero > 0) {
        $resultado .= $unidades[$numero];
    }

    return trim($resultado);
}
@endphp
