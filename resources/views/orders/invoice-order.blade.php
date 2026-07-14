<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>REPORTE DE VENTA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Estilos -->
    <link rel="shortcut icon" href="{{ $configNegocio?->favicon
        ? asset('storage/' . $configNegocio->favicon)
        : asset('assets/images/logo/logo-min.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/invoice/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f1f3f5;
            margin: 0;
            padding: 20px;
        }

        .invoice-content {
            background-color: #fff;
            padding: 40px;
            margin: auto;
            max-width: 900px;
            border-radius: 10px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
        }

        h1, h5, h6 {
            font-weight: 600;
        }

        .logo img {
            max-height: 80px;
        }

        .table th {
            background-color: #007bff;
            color: #fff;
        }

        .firma-cliente {
            margin-top: 50px;
            border-top: 2px solid #000;
            width: 60%;
            padding-top: 10px;
        }

        .firma-cliente p {
            margin-bottom: 0;
        }

        .ratio-80x36 {
            position: relative;
            width: 100%;
            padding-bottom: 45%;
        }

        .ratio-80x36 iframe {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        @media print {
            body {
                background-color: white !important;
            }
            .invoice-content {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
            .invoice-btn-section {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="invoice-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="logo">
                @if(!empty($configNegocio?->logo))
                    <img src="{{ asset('storage/' . $configNegocio->logo) }}"
                        alt="Logo"
                        style="max-height: 80px; width: auto; object-fit: contain;">
                @else
                    <img src="{{ asset('assets/images/logo/logo.png') }}"
                        alt="Logo"
                        style="max-height: 80px; width: auto; object-fit: contain;">
                @endif
            </div>
            <div>
                <h1>Venta #{{ $order->invoice_no }}</h1>
            </div>
        </div>

        <!-- Info General -->
        <div class="row mb-4">
            <div class="col-sm-6">
            <h6>Fecha de la venta:</h6>
            <p>{{ \Carbon\Carbon::parse($order->order_date)->locale('es')->translatedFormat('D d \d\e F Y') }}</p>
            </div>
            <div class="col-sm-6 text-sm-end">
            <h6>Sucursal:</h6>
            <p>{{ $sucursal->nombre }}<br>{{ $sucursal->direccion }}</p>
            </div>
        </div>

        <!-- Cliente y Pago -->
        <div class="row mb-4">
            <div class="col-sm-6">
                <h6><strong>Cliente:</strong></h6>
                <p>
                    {{ ucwords(strtolower($order->customer->name)) }}<br>
                    Correo: {{ $order->customer->email }}<br>
                    Telefono 1: {{ $order->customer->phone }}<br>
                    Telefono 2: {{ $order->customer->phone2 }}
                </p>
                @if($order->enviar == 1)
                    <p><strong>Pedido enviado a:</strong><br>
                    {{ $order->customer->calle }} {{ $order->customer->num_exterior }}
                    {{ $order->customer->num_interior ? 'Int. ' . $order->customer->num_interior : '' }},
                    Col. {{ $order->customer->colonia }}, {{ $order->customer->municipio }}, {{ $order->customer->estado }}.
                    <p><strong>Referencias:</strong><br>
                    {{ $order->customer->referencia }}
                    </p>
                @else
                    <p><strong>Entrega en sucursal.</strong></p>
                @endif
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6><strong>Detalles de pago:</strong></h6>
                <p>
                    <i class="fas fa-check-circle text-success me-1"></i> Estado:
                    <span class="{{ strtolower($order->payment_status) == 'pagado' ? 'text-success' : 'text-danger' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span><br>
                    @if($order->payment_status != 'pendiente')
                        <i class="fas fa-wallet text-primary me-1"></i> Método: {{ ucfirst($order->metodo_pago) }}<br>
                        <i class="fas fa-hand-holding-usd text-info me-1"></i> Total: ${{ number_format($order->total, 2) }}<br>
                        <i class="fas fa-coins text-warning me-1"></i> Cambio: ${{ number_format(abs($order->due), 2) }}                   
                    @endif
                </p>
            </div>
        </div>

        <!-- Resumen Productos -->
        <h5 class="mb-3">Resumen de Productos</h5>
        <div class="table-responsive mb-5">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Cantidad</th>
                        <!-- <th>Unidad</th> -->
                        <th>Código</th>
                        <th>Producto</th>
                        {{-- <th>Precio Unitario</th> --}}
                        {{--<th>Total (+IVA)</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orderDetails as $item)
                    <tr>
                        <td>{{ $item->quantity }}</td>
                        <!-- <td>{{ $item->product->equivalencia->nombre ?? 'Sin unidad' }}</td> -->
                        <td>{{ $item->product->product_code }}</td>
                        <td>{{ $item->product->product_name }}</td>
                        {{--
                        <td>${{ $item->unitcost }}</td>
                        <td>${{ $item->total }}</td>
                        --}}
                    </tr>
                    @endforeach
                    {{--
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                        <td><strong class="text-danger">${{ $order->total }}</strong></td>
                    </tr>
                    --}}
                </tbody>
            </table>
        </div>

<!-- Mapa de Google -->
        @if($order->enviar == 1 && !empty($order->customer->rul_maps))
            <h5 class="mb-3">Ubicación en Google Maps</h5>
            <div class="ratio ratio-16x9 mb-5">
                <iframe
                    src="{{$order->customer->rul_maps}}&z=16&output=embed"
                    style="border:0;"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        @endif

        <!-- Secciones de entrega - Solo para entregas a domicilio -->
        @if($order->enviar == 1)
            <!-- Firma Cliente -->
            <h5 class="mb-3">Confirmación de Entrega</h5>
            <p>Yo, <strong>{{ ucwords(strtolower($order->customer->name)) }}</strong>, confirmo que he recibido en buen estado y a conformidad los materiales indicados en la presente nota de entrega.</p>
            <div class="firma-cliente">
                <p><strong>Firma del Cliente</strong></p>
                <small class="text-muted">Nombre y firma legible</small>
            </div>

            <!-- Firma Empleado -->
            <div class="firma-cliente mt-5">
                <p><strong>Firma del encargado de la entrega</strong></p>
                <small class="text-muted">Nombre y firma legible</small>
            </div>

            <!-- Fecha de Entrega -->
            <div class="mt-3">
                <p><strong>Fecha de Entrega:</strong> {{ \Carbon\Carbon::now()->timezone('America/Mexico_City')->locale('es')->translatedFormat('D d \d\e F Y') }}</p>
            </div>
        @endif

        <!-- Botones -->
        <div class="invoice-btn-section mt-4 d-print-none">
            <a href="javascript:window.print()" class="btn btn-primary me-2">
                <i class="fas fa-print me-1"></i> Imprimir Venta
            </a>
            <a id="invoice_download_btn" class="btn btn-outline-primary">
                <i class="fas fa-download me-1"></i> Descargar Venta
            </a>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('assets/invoice/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/invoice/js/jspdf.min.js') }}"></script>
<script src="{{ asset('assets/invoice/js/html2canvas.js') }}"></script>
<script src="{{ asset('assets/invoice/js/app.js') }}"></script>

<script>
document.getElementById("invoice_download_btn").addEventListener("click", function () {
    const invoice = document.querySelector(".invoice-content");
    const btnSection = document.querySelector(".invoice-btn-section");
    const mapsFrame = document.querySelector(".ratio iframe");

    // Ocultar botones y mapa
    if (btnSection) btnSection.style.display = "none";
    if (mapsFrame) mapsFrame.style.display = "none";

    html2canvas(invoice, { scale: 2 }).then((canvas) => {
        const imgData = canvas.toDataURL("image/png");
        const pdf = new jsPDF('p', 'mm', 'a4');

        const imgWidth = 210;
        const pageHeight = 297;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        let heightLeft = imgHeight;
        let position = 0;

        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        pdf.save(`NOTA_VENTA_{{ $order->invoice_no }}.pdf`);

        // Restaurar elementos
        if (btnSection) btnSection.style.display = "";
        if (mapsFrame) mapsFrame.style.display = "";
    });
});
</script>
</body>
</html>
