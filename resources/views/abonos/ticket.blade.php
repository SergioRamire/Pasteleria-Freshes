<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $configNegocio?->nombre_negocio ?? 'AcuarioA.' }} </title>
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
        .tabla-producto th:nth-child(2),
        .tabla-producto td:nth-child(2) { width: 18%; text-align: center; }
        .tabla-producto th:nth-child(3),
        .tabla-producto td:nth-child(3) { width: 60%; text-align: left; }
        .tabla-producto th:nth-child(4),
        .tabla-producto td:nth-child(4) { width: 30%; text-align: right; }

        .tabla-producto tbody tr:not(:last-child) {
            border-bottom: 1px dashed #ccc;
        }

        .totales { display: flex; justify-content: space-between; margin-top: 4px; font-size: 10px; }
        .info { margin-top: 5px; font-size: 10px; text-align: left; }
        .aviso { font-size: 10px; margin-top: 6px; line-height: 1.4; text-align: justify; }
        .aviso strong { display: block; margin-bottom: 4px; text-align: center; }
        .separador { text-align: center; margin: 10px 0; border-bottom: 2px double #000; padding-bottom: 5px; font-size: 10px; }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>

<body onload="window.print(); setTimeout(() => window.location.href='{{ route('abonos.index') }}', 1000)">
    <div id="ticket" class="ticket">
         <div class="center">
            @if(!empty($configNegocio?->logo))
                    <img src="{{ asset('storage/' . $configNegocio->logo) }}"
                        alt="Logo principal"
                        class="img-fluid"
                        style="max-width: 120px; height: auto; margin-bottom: 6px;">
            @else
                    <img src="{{ asset('assets/images/logo/logo.png') }}"
                        alt="Logo principal"
                        class="img-fluid"
                        style="max-width: 120px; height: auto; margin-bottom: 6px;">
            @endif
       
            <!-- <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo" style="max-width: 120px; height: auto; margin-bottom: 6px;"> -->
        </div>

        <div class="center">
            <strong> {{ $configNegocio?->nombre_negocio ?? 'AcuarioA.' }}</strong><br>
            <!-- R.F.C.: {{ $configNegocio?->rfc }}<br> -->
            Sucursal: {{ $sucursal->nombre }}<br>
            {{ $sucursal->direccion }}<br>
            Tel:  {{ $configNegocio?->telefono ?? '951-562-0424.' }} 
        </div>

        <div class="line"></div>

     
        
        <div class="center">
                 <strong >COMPROBANTE DE ABONO</strong><br>
        </div>
        
        <div class="line"></div>

        <table class="info">

            <tr>
                <td class="label">Folio:</td>
                <td>{{ $abono->codigo }}</td>
            </tr>

            <tr>
                <td class="label">Venta:</td>
                <td>{{ $order->invoice_no }}</td>
            </tr>

            <tr>
                <td class="label">Fecha:</td>
                <td>{{ now()->format('d/m/Y') }}</td>
            </tr>

            <tr>
                <td class="label">Hora:</td>
                <td>{{ now()->format('h:i A') }}</td>
            </tr>

            <tr>
                <td class="label">Cajero:</td>
                <td>{{ $cajero->name }}</td>
            </tr>

            <tr>
                <td class="label">Cliente:</td>
                <td>{{ $order->customer_name }}</td>
            </tr>

            <tr>
                <td class="label">Método:</td>
                <td>{{ ucfirst($abono->metodo_pago) }}</td>
            </tr>

            @if($abono->metodo_pago=='tarjeta')
                <tr>
                    <td class="label">Ticket:</td>
                    <td>{{ $abono->num_ticket }}</td>
                </tr>
                <tr>
                    <td class="label">Tarjeta:</td>
                    <td>**** {{ $abono->num_tarjeta }}</td>
                </tr>
            @endif

            <tr>
                <td class="label">Observaciones:</td>
                <td>{{ $abono->observacion ?? 'Ninguna' }}</td>
            </tr>

        </table>

        <div class="line"></div>


        <div class="resumen">
            <table>

                <tr>
                    <td>Total Venta</td>
                    <td align="right">
                        ${{ number_format($order->total,2) }}
                    </td>
                </tr>

                <tr>

                    <td>Total Pagado</td>

                    <td align="right">
                        ${{ number_format($order->pay,2) }}
                    </td>
                </tr>
                
                <tr>
                    <td class="label"> Abono Registrado </td>
                    <td align="right" class="total">${{ number_format($abono->monto,2) }}</td>
                </tr>

                <tr>
                    <td> Saldo Pendiente </td>
                    <td align="right"> ${{ number_format($order->due,2) }} </td>
                </tr>

            </table>

        </div>
         <div class="aviso">
            <strong>⚠️ Aviso Importante</strong>
            - Gracias por su confianza. Este comprobante confirma el registro de su abono en nuestro sistema. Le recomendamos conservar este documento hasta la liquidación total de su compra. Para cualquier aclaración, presente este comprobante junto con el número de venta correspondiente.
        </div>

        <div class="line"></div>

        <div class="center">
            ¡Gracias por su preferencia!<br>
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
        
        // Imprime la primera copia
        window.print();

        // Espera 400 ms y manda la segunda copia
        setTimeout(() => {
            window.print();
        }, 1000);

        // Después de 1.2 seg redirige a ventas pendientes de pago
        setTimeout(() => {
            window.location.href = "{{ route('order.pendingDue') }}";
        }, 1200);
        
    });
    </script>
</body>
</html>
