<!doctype html>
<html lang="es">
@php
setlocale(LC_TIME, 'es_ES.UTF-8');
\Carbon\Carbon::setLocale('es');
$fecha_actual = \Carbon\Carbon::now()->timezone('America/Mexico_City')->translatedFormat('l, d \d\e F \d\e Y');
$ventas_totales = $tota_ventas->sum('total');
$retiros_totales = $retiros->sum('total');
$iva = $ventas_totales * 0.16;
$total = $ventas_totales;
$subtotal = $total - $iva;
@endphp
<head>
    <meta charset="utf-8">
    <title>Reporte Corte de Caja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ $configNegocio?->favicon ? asset('storage/' . $configNegocio->favicon) : asset('assets/images/logo/logo-min.png') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/backend-plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/backend.css?v=1.0.0') }}">
    <link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/remixicon/fonts/remixicon.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #fff;
            font-family: 'Poppins', sans-serif;
            color: #2d3748;
            padding: 30px 20px;
            font-size: 13px;
        }

        .wrapper {
            max-width: 960px;
            margin: auto;
            background: #fff;
            border-radius: 14px;
            border: 1px solid #dbeafe;
            box-shadow: 0 0 0 4px #eff6ff, 0 4px 24px rgba(59,130,246,0.08);
            overflow: hidden;
        }

        /* ── Header ── */
        .report-header {
            border-bottom: 2px solid #dbeafe;
            padding: 28px 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .report-header .logo-wrap img {
            max-height: 65px;
            max-width: 150px;
            object-fit: contain;
        }

        .report-header .header-info {
            text-align: right;
        }

        .report-header .header-info h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 4px;
        }

        .report-header .header-info p {
            font-size: 0.8rem;
            color: #64748b;
            margin: 2px 0;
        }

        /* ── Body ── */
        .report-body {
            padding: 28px 36px;
        }

        /* ── Info de caja ── */
        .caja-info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 28px;
        }

        .caja-badge {
            border: 1px solid #dbeafe;
            border-radius: 10px;
            padding: 12px 14px;
            background: #fff;
        }

        .caja-badge .label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .caja-badge .value {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e40af;
        }

        /* ── Títulos de sección ── */
        .section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e40af;
            margin: 24px 0 10px;
            padding-bottom: 6px;
            border-bottom: 1.5px solid #dbeafe;
        }

        /* ── Tablas ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            overflow: hidden;
        }

        thead tr {
            background: #eff6ff;
        }

        thead th {
            padding: 10px 14px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #1e40af;
            border-bottom: 1.5px solid #dbeafe;
        }

        tbody tr:nth-child(even) { background: #f8faff; }

        tbody td {
            padding: 9px 14px;
            border-bottom: 1px solid #f0f4ff;
            color: #475569;
        }

        tfoot tr { background: #eff6ff; }

        tfoot th {
            padding: 10px 14px;
            font-weight: 700;
            color: #1e40af;
            font-size: 0.85rem;
            border-top: 1.5px solid #dbeafe;
        }

        /* ── Resumen ── */
        .resumen-box {
            border: 1px solid #dbeafe;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 28px;
        }

        .resumen-title {
            background: #eff6ff;
            border-bottom: 1.5px solid #dbeafe;
            text-align: center;
            padding: 12px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #1e40af;
            letter-spacing: 0.3px;
        }

        .resumen-body {
            padding: 16px 24px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            background: #fff;
        }

        .resumen-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #f0f4ff;
            background: #fafcff;
        }

        .resumen-row .r-label {
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
        }

        .resumen-row .r-value {
            font-weight: 700;
            font-size: 0.95rem;
        }

        .color-danger  { color: #dc2626; }
        .color-info    { color: #0891b2; }
        .color-success { color: #16a34a; }

        .resumen-total {
            background: #eff6ff;
            border-top: 1.5px solid #dbeafe;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 24px;
            color: #1e40af;
        }

        .resumen-total .total-label {
            font-size: 0.95rem;
            font-weight: 600;
        }

        .resumen-total .total-value {
            font-size: 1.4rem;
            font-weight: 700;
        }

        /* ── Firma ── */
        .firma-section {
            text-align: center;
            margin: 36px 0 10px;
        }

        .firma-line {
            border-top: 1.5px solid #94a3b8;
            width: 200px;
            margin: 10px auto 8px;
        }

        .firma-section p {
            color: #64748b;
            font-size: 0.82rem;
            margin: 2px 0;
        }

        /* ── Print ── */
        @media print {
            body { background: none; padding: 0; }
            .wrapper {
                box-shadow: none;
                border-radius: 0;
                border: none;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- HEADER --}}
    <div class="report-header">
        <div class="logo-wrap">
            @if(!empty($configNegocio?->logo))
                <img src="{{ asset('storage/' . $configNegocio->logo) }}" alt="Logo">
            @else
                <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo">
            @endif
        </div>
        <div class="header-info">
            <h2>Corte de Caja</h2>
            <p><i class="ri-calendar-line"></i> {{ $fecha_actual }}</p>
            <p><i class="ri-store-2-line"></i> {{ $caja->nombre_sucursal }}</p>
            <p><i class="ri-user-line"></i> {{ $caja->nombre_usuario }}</p>
        </div>
    </div>

    {{-- BODY --}}
    <div class="report-body">

        {{-- Info de caja --}}
        <div class="caja-info-grid">
            <div class="caja-badge">
                <div class="label">Estado</div>
                <div class="value">{{ ucfirst($caja->estado) }}</div>
            </div>
            <div class="caja-badge">
                <div class="label">Monto Inicial</div>
                <div class="value">${{ number_format($caja->monto_inicial, 2) }}</div>
            </div>
            <div class="caja-badge">
                <div class="label">Monto Final</div>
                <div class="value">${{ number_format($caja->monto_final, 2) }}</div>
            </div>
            <div class="caja-badge">
                <div class="label">Hora Apertura</div>
                <div class="value">{{ \Carbon\Carbon::parse($caja->hora_apertura)->format('h:i A') }}</div>
            </div>
            <div class="caja-badge">
                <div class="label">Hora Cierre</div>
                <div class="value">{{ $caja->hora_cierre ? \Carbon\Carbon::parse($caja->hora_cierre)->format('h:i A') : 'Sin cierre' }}</div>
            </div>
            <div class="caja-badge">
                <div class="label">Fecha</div>
                <div class="value">{{ $caja->fecha }}</div>
            </div>
            <div class="caja-badge">
                <div class="label">Sucursal</div>
                <div class="value">{{ $caja->nombre_sucursal }}</div>
            </div>
            <div class="caja-badge">
                <div class="label">Empleado</div>
                <div class="value">{{ $caja->nombre_usuario }}</div>
            </div>
        </div>

        {{-- Secciones --}}
        @php
        $secciones = [
            ['titulo' => 'Ventas',                 'tipo' => 'venta',                   'icono' => 'ri-shopping-cart-line'],
            ['titulo' => 'Ventas Canceladas',       'tipo' => 'Venta cancelada',         'icono' => 'ri-close-circle-line'],
            ['titulo' => 'Devoluciones / Retiros',  'tipo' => ['retiro', 'devolucion'],  'icono' => 'ri-arrow-go-back-line'],
            ['titulo' => 'Ingresos',                'tipo' => 'ingreso',                 'icono' => 'ri-money-dollar-circle-line'],
        ];
        @endphp

        @foreach ($secciones as $seccion)
            @php
                $filtradas = is_array($seccion['tipo'])
                    ? $transacciones->whereIn('tipo_transaccion', $seccion['tipo'])
                    : $transacciones->where('tipo_transaccion', $seccion['tipo']);
            @endphp

            <div class="section-title">
                <i class="{{ $seccion['icono'] }}"></i>
                {{ $seccion['titulo'] }}
            </div>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Hora</th>
                        <th>Total</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($filtradas as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->hora)->format('h:i A') }}</td>
                            <td><strong>${{ number_format($item->total, 2) }}</strong></td>
                            <td>{{ $item->descripcion }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; color:#aaa; font-style:italic;">
                                Sin registros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Total {{ $seccion['titulo'] }}:</th>
                        <th colspan="2">${{ number_format($filtradas->sum('total'), 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        @endforeach

        {{-- Resumen --}}
        <div class="resumen-box">
            <div class="resumen-title">
                <i class="ri-pie-chart-line"></i> Resumen del Corte de Caja
            </div>
            <div class="resumen-body">
                <div class="resumen-row">
                    <span class="r-label"><i class="ri-arrow-down-circle-line color-danger"></i> Total Retiros</span>
                    <span class="r-value color-danger">${{ number_format($retiros_totales, 2) }}</span>
                </div>
                <div class="resumen-row">
                    <span class="r-label"><i class="ri-bank-card-line color-info"></i> Cobrado con Tarjeta</span>
                    <span class="r-value color-info">${{ number_format($tarjetas, 2) }}</span>
                </div>
                <div class="resumen-row">
                    <span class="r-label"><i class="ri-money-dollar-box-line color-success"></i> Cobrado en Efectivo</span>
                    <span class="r-value color-success">${{ number_format($efectivo + $cambios, 2) }}</span>
                </div>
            </div>
            <div class="resumen-total">
                <span class="total-label"><i class="ri-wallet-3-line"></i> Total Ventas</span>
                <span class="total-value">${{ number_format($ventas_totales, 2) }}</span>
            </div>
        </div>

        {{-- Firma --}}
        <div class="firma-section">
            <div class="firma-line"></div>
            <p><strong>{{ $caja->nombre_usuario }}</strong></p>
            <p>Firma del Responsable</p>
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