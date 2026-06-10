@php
    setlocale(LC_TIME, 'es_ES');
    use Carbon\Carbon;

    $fecha_actual = Carbon::now()->timezone('America/Mexico_City')->formatLocalized('%A, %d de %B de %Y');
    $total = Cart::subtotal();
    $subtotal = $total / (1 + 0.16);
    $iva = $subtotal * 0.16;
    $fechaVencimiento = Carbon::now()->addDays(3)->locale('es')->isoFormat('dddd DD [de] MMMM [de] YYYY');
    $numeroNotaCompleto = sprintf('%06d', rand(100000, 999999));

    if (!function_exists('numeroALetras')) {
        function numeroALetras($numero) {
            $entero = floor($numero);
            $decimales = round(($numero - $entero) * 100);
            $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
            $decenas = ['', '', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
            $especiales = ['DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
            $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];
            if ($entero == 0 && $decimales == 0) return 'CERO PESOS 00/100 M.N.';
            $resultado = '';
            if ($entero >= 1000000) {
                $millones = floor($entero / 1000000);
                $resultado .= $millones == 1 ? 'UN MILLÓN ' : convertirGrupo($millones, $unidades, $decenas, $especiales, $centenas) . ' MILLONES ';
                $entero %= 1000000;
            }
            if ($entero >= 1000) {
                $miles = floor($entero / 1000);
                $resultado .= $miles == 1 ? 'MIL ' : convertirGrupo($miles, $unidades, $decenas, $especiales, $centenas) . ' MIL ';
                $entero %= 1000;
            }
            if ($entero > 0) $resultado .= convertirGrupo($entero, $unidades, $decenas, $especiales, $centenas) . ' ';
            $resultado = trim($resultado) . (floor($numero) == 1 ? ' PESO' : ' PESOS');
            $resultado .= ' ' . sprintf('%02d', $decimales) . '/100 M.N.';
            return $resultado;
        }
    }

    if (!function_exists('convertirGrupo')) {
        function convertirGrupo($numero, $unidades, $decenas, $especiales, $centenas) {
            $resultado = '';
            if ($numero >= 100) {
                $cent = floor($numero / 100);
                $resultado .= $numero == 100 ? 'CIEN' : $centenas[$cent] . ' ';
                $numero %= 100;
            }
            if ($numero >= 20) {
                $dec = floor($numero / 10);
                $uni = $numero % 10;
                $resultado .= $decenas[$dec];
                if ($uni > 0) $resultado .= ' Y ' . $unidades[$uni];
            } elseif ($numero >= 10) {
                $resultado .= $especiales[$numero - 10];
            } elseif ($numero > 0) {
                $resultado .= $unidades[$numero];
            }
            return trim($resultado);
        }
    }
@endphp

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Cotización — {{ $configNegocio?->nombre_negocio ?? 'Mi Negocio' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="{{ $configNegocio?->favicon
            ? asset('storage/' . $configNegocio->favicon)
            : asset('assets/images/logo/logo-min.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            background: #f0f4f8;
            color: #2d3748;
            font-size: 12px;
            padding: 20px;
        }

        .container {
            position: relative;
            width: 210mm;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            border: 1px solid #dbeafe;
            box-shadow: 0 0 0 4px #eff6ff, 0 4px 20px rgba(59,130,246,0.08);
            overflow: hidden;
        }

        /* ── Marca de agua ── */
        .watermark {
            position: absolute;
            font-size: 18px;
            color: rgba(180,200,230,0.13);
            white-space: nowrap;
            z-index: 0;
            pointer-events: none;
            user-select: none;
            transform: rotate(-30deg);
            font-weight: 700;
            letter-spacing: 2px;
        }
        .wm1  { top:8%;  left:5%;  }
        .wm2  { top:18%; left:45%; }
        .wm3  { top:28%; left:15%; }
        .wm4  { top:38%; left:55%; }
        .wm5  { top:48%; left:5%;  }
        .wm6  { top:58%; left:40%; }
        .wm7  { top:68%; left:15%; }
        .wm8  { top:78%; left:55%; }
        .wm9  { top:88%; left:10%; }
        .wm10 { top:95%; left:45%; }

        /* ── Header ── */
        .doc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 30px;
            border-bottom: 2px solid #dbeafe;
            position: relative;
            z-index: 1;
        }

        .doc-header .logo-side img {
            max-height: 60px;
            max-width: 150px;
            width: auto;
            object-fit: contain;
        }

        .doc-header .title-side {
            text-align: right;
        }

        .doc-header .title-side .doc-type {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #93c5fd;
            margin-bottom: 4px;
        }

        .doc-header .title-side h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1e40af;
            line-height: 1;
            margin-bottom: 4px;
        }

        .doc-header .title-side .folio {
            font-size: 0.78rem;
            color: #64748b;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 20px;
            padding: 3px 12px;
            display: inline-block;
        }

        /* ── Banda de estado ── */
        .status-bar {
            background: #eff6ff;
            border-bottom: 1px solid #dbeafe;
            padding: 10px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .status-bar .status-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            color: #475569;
        }

        .status-bar .status-item i {
            color: #3b82f6;
            font-size: 0.85rem;
        }

        .status-bar .badge-pendiente {
            background: #fef9c3;
            color: #92400e;
            border: 1px solid #fde68a;
            border-radius: 20px;
            padding: 3px 12px;
            font-size: 0.72rem;
            font-weight: 600;
        }

        /* ── Cuerpo ── */
        .doc-body {
            padding: 24px 30px;
            position: relative;
            z-index: 1;
        }

        /* ── Info cliente ── */
        .client-box {
            border: 1px solid #dbeafe;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 20px;
            background: #fafcff;
        }

        .client-box .client-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #93c5fd;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .client-box .client-name {
            font-size: 1rem;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 4px;
        }

        .client-box .client-detail {
            font-size: 0.78rem;
            color: #64748b;
            line-height: 1.6;
        }

        /* ── Sección títulos ── */
        .section-title {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1e40af;
            margin: 20px 0 10px;
            padding-bottom: 6px;
            border-bottom: 1.5px solid #dbeafe;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── Tablas ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            z-index: 1;
            page-break-inside: avoid;
        }

        thead tr { background: #eff6ff; }

        thead th {
            padding: 9px 12px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e40af;
            border-bottom: 1.5px solid #dbeafe;
            text-align: left;
        }

        tbody tr:nth-child(even) { background: #f8faff; }

        tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #f0f4ff;
            color: #475569;
            font-size: 0.82rem;
        }

        /* ── Totales ── */
        .totals-wrap {
            display: flex;
            justify-content: flex-end;
            margin-top: 16px;
        }

        .totals-box {
            width: 260px;
            border: 1px solid #dbeafe;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .totals-box .totals-header {
            background: #eff6ff;
            border-bottom: 1px solid #dbeafe;
            padding: 8px 14px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e40af;
        }

        .totals-box .totals-body {
            padding: 10px 14px;
            background: #fff;
        }

        .totals-box .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 0.8rem;
            color: #475569;
            border-bottom: 1px solid #f0f4ff;
        }

        .totals-box .totals-row:last-child { border: none; }

        .totals-box .totals-footer {
            background: #eff6ff;
            border-top: 1.5px solid #dbeafe;
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .totals-box .totals-footer .total-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1e40af;
        }

        .totals-box .totals-footer .total-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e40af;
        }

        .totals-box .en-letras {
            padding: 6px 14px;
            font-size: 0.68rem;
            color: #94a3b8;
            font-style: italic;
            background: #fff;
            border-top: 1px solid #f0f4ff;
            text-align: right;
        }

        /* ── Nota legal ── */
        .note {
            margin-top: 20px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.72rem;
            color: #92400e;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .note strong {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
            font-size: 0.78rem;
        }

        /* ── Botones ── */
        .invoice-btn-section {
            padding: 16px 30px;
            border-top: 1px solid #dbeafe;
            text-align: center;
            background: #fafcff;
            position: relative;
            z-index: 1;
        }

        .btn {
            display: inline-block;
            padding: 9px 20px;
            margin: 4px;
            font-size: 0.82rem;
            font-weight: 600;
            border-radius: 8px;
            border: 1.5px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary {
            background: #1e40af;
            color: #fff;
            border-color: #1e40af;
        }

        .btn-primary:hover {
            background: #1e3a8a;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(30,64,175,0.25);
        }

        .btn-outline {
            background: transparent;
            color: #1e40af;
            border-color: #93c5fd;
        }

        .btn-outline:hover {
            background: #eff6ff;
            transform: translateY(-1px);
        }

        /* ── Print ── */
        @media print {
            body { background: none; padding: 0; }
            .container { box-shadow: none; border-radius: 0; border: none; }
            .d-print-none { display: none !important; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>

<body onload="window.print()">
<div class="container">

    {{-- Marcas de agua --}}
    @for ($i = 1; $i <= 10; $i++)
        <div class="watermark wm{{ $i }}">COTIZACIÓN — NO ES COMPROBANTE FISCAL</div>
    @endfor

    {{-- Header --}}
    <div class="doc-header">
        <div class="logo-side">
            @if(!empty($configNegocio?->logo))
                <img src="{{ asset('storage/' . $configNegocio->logo) }}" alt="Logo">
            @else
                <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo">
            @endif
        </div>
        <div class="title-side">
            <div class="doc-type">Documento de Cotización</div>
            <h1>Cotización</h1>
            <span class="folio">N° {{ $numeroNotaCompleto }}</span>
        </div>
    </div>

    {{-- Banda de estado --}}
    <div class="status-bar">
        <div class="status-item">
            <i class="fas fa-calendar-alt"></i>
            {{ ucfirst($fecha_actual) }}
        </div>
        <div class="status-item">
            <i class="fas fa-clock"></i>
            Válida hasta: {{ ucfirst($fechaVencimiento) }}
        </div>
        <div>
            <span class="badge-pendiente">⚠ Pendiente de Pago</span>
        </div>
    </div>

    {{-- Cuerpo --}}
    <div class="doc-body">

        {{-- Cliente --}}
        <div class="client-box">
            <div class="client-title"><i class="fas fa-user"></i> Datos del Cliente</div>
            <div class="client-name">{{ $customer->name }}</div>
            <div class="client-detail">
                @if($customer->address) <i class="fas fa-map-marker-alt"></i> {{ $customer->address }}<br> @endif
                <i class="fas fa-phone"></i> {{ $customer->phone ?? '—' }} &nbsp;&nbsp;
                <i class="fas fa-envelope"></i> {{ $customer->email ?? '—' }}
            </div>
        </div>

        {{-- Productos --}}
        <div class="section-title">
            <i class="fas fa-boxes"></i> Resumen del Pedido
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Artículo</th>
                    <th style="text-align:center;">Cantidad</th>
                    <th style="text-align:center;">Precio Unit.</th>
                    <th style="text-align:center;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($content as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->options->product_code ?? 'N/A' }}</td>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td style="text-align:center;">{{ $item->qty }}</td>
                         <td style="text-align:center;">${{ number_format($item->price, 2) }}</td>
                        <td style="text-align:center;">${{ number_format($item->qty * $item->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totales --}}
        <div class="totals-wrap">
            <div class="totals-box">
                <div class="totals-header"><i class="fas fa-receipt"></i> Detalles del Pedido</div>
                <div class="totals-body">
                    <div class="totals-row">
                        <span>Subtotal</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="totals-row">
                        <span>IVA (16%)</span>
                        <span>${{ number_format($iva, 2) }}</span>
                    </div>
                    <div class="totals-row">
                        <span>Total de productos</span>
                        <span>{{ $content->sum('qty') }} pzas</span>
                    </div>
                </div>
                <div class="en-letras">{{ numeroALetras($total) }}</div>
                <div class="totals-footer">
                    <span class="total-label">TOTAL</span>
                    <span class="total-value">${{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Nota legal --}}
        <div class="note">
            <strong><i class="fas fa-exclamation-triangle"></i> Aviso Importante</strong>
            Este documento es únicamente una cotización informativa y <strong>no constituye un comprobante fiscal (CFDI)</strong>.
            Los precios y condiciones están sujetos a cambios sin previo aviso y deberán confirmarse al momento de la compra.
        </div>

    </div>

    {{-- Botones --}}
    <div class="invoice-btn-section d-print-none">
        <a href="javascript:window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimir Cotización
        </a>
        <a id="invoice_download_btn" class="btn btn-outline">
            <i class="fas fa-file-pdf"></i> Descargar PDF
        </a>
    </div>

</div>

{{-- Scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById("invoice_download_btn").addEventListener("click", function () {
        const invoice = document.querySelector(".container");
        const btnSection = document.querySelector(".invoice-btn-section");
        if (btnSection) btnSection.style.display = "none";

        const loadingMsg = document.createElement('div');
        loadingMsg.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF...';
        loadingMsg.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:20px 30px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.2);z-index:10000;font-family:sans-serif;font-size:14px;';
        document.body.appendChild(loadingMsg);

        html2canvas(invoice, {
            scale: 3,
            useCORS: true,
            backgroundColor: '#ffffff',
            logging: false,
            scrollX: 0,
            scrollY: 0,
        }).then((canvas) => {
            const imgData = canvas.toDataURL("image/png", 1.0);
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4', compress: false });

            const pdfWidth = 210, pdfHeight = 297, margin = 5;
            const usableWidth = pdfWidth - margin * 2;
            const usableHeight = pdfHeight - margin * 2;
            const ratio = canvas.width / canvas.height;
            let imgW = usableWidth;
            let imgH = usableWidth / ratio;

            if (imgH > usableHeight) {
                const pages = Math.ceil(imgH / usableHeight);
                const secH = canvas.height / pages;
                for (let p = 0; p < pages; p++) {
                    if (p > 0) pdf.addPage();
                    const sec = document.createElement('canvas');
                    sec.width = canvas.width;
                    sec.height = secH;
                    const ctx = sec.getContext('2d');
                    ctx.drawImage(canvas, 0, p * secH, canvas.width, secH, 0, 0, canvas.width, secH);
                    pdf.addImage(sec.toDataURL("image/png", 1.0), 'PNG', margin, margin, usableWidth, usableHeight, '', 'NONE');
                }
            } else {
                pdf.addImage(imgData, 'PNG', margin, margin + (usableHeight - imgH) / 2, imgW, imgH, '', 'NONE');
            }

            pdf.save(`COTIZACION_{{ $numeroNotaCompleto }}.pdf`);
            document.body.removeChild(loadingMsg);
            if (btnSection) btnSection.style.display = "";
        }).catch(() => {
            alert('Error al generar el PDF.');
            document.body.removeChild(loadingMsg);
            if (btnSection) btnSection.style.display = "";
        });
    });
});
</script>
</body>
</html>