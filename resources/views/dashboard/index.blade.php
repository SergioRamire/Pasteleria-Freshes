@extends('dashboard.body.main')

@section('container')
@php $rol = auth()->user()->getRoleNames()->first(); @endphp

<div class="container-fluid py-4">

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="ri-check-line"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="ri-error-warning-line"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- BIENVENIDA --}}
    <div class="card border-0 shadow-sm mb-4 p-4 d-flex flex-row align-items-center justify-content-between">
        <div>
            <h4 class="mb-1">👋 Hola, <strong>{{ auth()->user()->name }}</strong></h4>
            <span class="badge bg-primary fs-6">{{ $rol }}</span>
            <p class="text-muted mt-1 mb-0">{{ ucfirst(\Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}</p>
        </div>
        <i class="ri-dashboard-line" style="font-size: 3rem; color: #cbd5e1;"></i>
    </div>


    {{-- ══════════════════════════════════════════════════════
         SUPERADMIN + GERENTE
    ══════════════════════════════════════════════════════ --}}
    @hasanyrole('SuperAdmin|Gerente')

        {{-- Tarjetas resumen --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm text-white h-10" style="background: linear-gradient(135deg,#6366f1,#818cf8);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Ventas Hoy</p>
                        <h4 class="fw-bold">${{ number_format($ventasHoy, 2) }}</h4>
                        <i class="ri-line-chart-line ri-xl float-end mt-n4"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm text-white h-10" style="background: linear-gradient(135deg,#10b981,#34d399);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Ventas del Mes</p>
                        <h4 class="fw-bold">${{ number_format($ventasMes, 2) }}</h4>
                        <i class="ri-bar-chart-2-line ri-xl float-end mt-n4"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm text-white h-10" style="background: linear-gradient(135deg,#f59e0b,#fbbf24);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Ventas Pendientes de Entrega</p>
                        <h4 class="fw-bold">{{ $ordenesPendientes }}</h4>
                        <i class="ri-time-line ri-xl float-end mt-n4"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm text-white h-10" style="background: linear-gradient(135deg,#3b82f6,#60a5fa);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Ventas Entregadas</p>
                        <h4 class="fw-bold">{{ $ordenesCompletas }}</h4>
                        <i class="ri-checkbox-circle-line ri-xl float-end mt-n4"></i>
                    </div>
                </div>
            </div>
            @role('SuperAdmin')
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm text-white h-10" style="background: linear-gradient(135deg,#8b5cf6,#a78bfa);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Usuarios</p>
                        <h4 class="fw-bold">{{ $totalUsuarios }}</h4>
                        <i class="ri-user-line ri-xl float-end mt-n4"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm text-white h-10" style="background: linear-gradient(135deg,#ec4899,#f472b6);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Sucursales</p>
                        <h4 class="fw-bold">{{ $totalSucursales }}</h4>
                        <i class="ri-store-2-line ri-xl float-end mt-n4"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm text-white h-10" style="background: linear-gradient(015deg,#7b45ec99,#f232b6);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Cajas Abiertas</p>
                        <h4 class="fw-bold">{{ $cjasAbiertas }}</h4>
                        <i class="fas fa-cash-register me-1"></i>
                    </div>
                </div>
            </div>
            @endrole
        </div>

        {{-- Gráficas principales --}}
        <div class="row g-3 mb-4">
            {{-- Ventas diarias últimos 30 días --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-semibold mb-0">📈 Ventas últimos 30 días</h6>
                    </div>
                    <div class="card-body">
                        <div id="chartVentasDiarias" style="min-height:300px;"></div>
                    </div>
                </div>
            </div>

            {{-- Métodos de pago --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-semibold mb-0">💳 Método de Pago</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div id="chartMetodoPago" style="min-height:280px; width:100%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            {{-- Ingresos vs Egresos --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-semibold mb-0">💰 Ingresos vs Egresos (últimos 6 meses)</h6>
                    </div>
                    <div class="card-body">
                        <div id="chartIngresosEgresos" style="min-height:280px;"></div>
                    </div>
                </div>
            </div>

            {{-- Top productos --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-semibold mb-0">🏆 Top 5 Productos más vendidos</h6>
                    </div>
                    <div class="card-body">
                        <div id="chartTopProductos" style="min-height:280px;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bajo stock --}}
        @if($bajoStock->count())
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-3">
                        <h6 class="fw-semibold mb-0">⚠️ Productos con Stock Bajo</h6>
                        <span class="badge bg-danger">{{ $bajoStock->count() }} productos</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Stock Actual</th>
                                        <th class="text-center">Stock Mínimo</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bajoStock as $inv)
                                    <tr>
                                        <td>{{ $inv->producto->product_name ?? 'N/A' }}</td>
                                        <td class="text-center fw-bold text-danger">{{ $inv->stock }}</td>
                                        <td class="text-center">{{ $inv->stock_minimo }}</td>
                                        <td class="text-center">
                                            @if($inv->stock == 0)
                                                <span class="badge bg-danger">Sin stock</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Bajo</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    @endhasanyrole


    {{-- ══════════════════════════════════════════════════════
         CAJERO
    ══════════════════════════════════════════════════════ --}}
    @role('Cajero')

        {{-- Estado de caja --}}
        <div class="row g-3 mb-4">
            <div class="col-12">
                @if($cajaHoy)
                    <div class="alert border-0 shadow-sm mb-0
                        {{ $cajaHoy->estado === 'abierta' ? 'alert-success' : 'alert-secondary' }}">
                        <strong>
                            <i class="ri-store-line me-1"></i>
                            Caja #{{ $cajaHoy->numero_caja }} —
                            {{ $cajaHoy->estado === 'abierta' ? 'ABIERTA' : 'CERRADA' }}
                        </strong>
                        &nbsp;|&nbsp; Apertura: {{ $cajaHoy->hora_apertura }}
                        @if($cajaHoy->estado === 'cerrada')
                            &nbsp;|&nbsp; Cierre: {{ $cajaHoy->hora_cierre }}
                        @endif
                        &nbsp;|&nbsp; Monto inicial: ${{ number_format($cajaHoy->monto_inicial, 2) }}
                    </div>
                @else
                    <div class="alert alert-warning border-0 shadow-sm mb-0">
                        <i class="ri-error-warning-line me-1"></i>
                        No hay caja abierta para hoy.
                    </div>
                @endif
            </div>
        </div>

        {{-- Tarjetas cajero --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-4">
                <div class="card border-0 shadow-sm text-white h-100" style="background:linear-gradient(135deg,#10b981,#34d399);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Total Cobrado Hoy</p>
                        <h4 class="fw-bold">${{ number_format($totalCobradoHoy, 2) }}</h4>
                        <i class="ri-money-cny-circle-line ri-xl float-end mt-n4"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card border-0 shadow-sm text-white h-100" style="background:linear-gradient(135deg,#3b82f6,#60a5fa);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Ventas del Día</p>
                        <h4 class="fw-bold">{{ $ventasHoy->count() }}</h4>
                        <i class="ri-shopping-cart-line ri-xl float-end mt-n4"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card border-0 shadow-sm text-white h-100" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Pendientes de Pago</p>
                        <h4 class="fw-bold">{{ $ordenesPendientes }}</h4>
                        <i class="ri-time-line ri-xl float-end mt-n4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráfica ventas por hora + tabla de ventas del día --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-semibold mb-0">🕐 Ventas por hora (hoy)</h6>
                    </div>
                    <div class="card-body">
                        <div id="chartVentasHora" style="min-height:280px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-semibold mb-0">🧾 Mis ventas de hoy</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Folio</th>
                                        <th>Cliente</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Pago</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ventasHoy as $orden)
                                    <tr>
                                        <td><small class="text-muted">{{ $orden->invoice_no ?? '#'.$orden->id }}</small></td>
                                        <td>{{ $orden->customer->name ?? 'N/A' }}</td>
                                        <td class="text-center">${{ number_format($orden->total, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $orden->metodo_pago === 'cash' ? 'bg-success' : ($orden->metodo_pago === 'card' ? 'bg-primary' : 'bg-secondary') }}">
                                                {{ ucfirst($orden->metodo_pago ?? 'N/A') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $orden->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ $orden->payment_status === 'paid' ? 'Pagado' : 'Pendiente' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">Sin ventas registradas hoy.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endrole


    {{-- ══════════════════════════════════════════════════════
         ALMACÉN
    ══════════════════════════════════════════════════════ --}}
    @role('Almacen')

        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm text-white h-100" style="background:linear-gradient(135deg,#3b82f6,#60a5fa);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Total Productos</p>
                        <h4 class="fw-bold">{{ $totalProductos }}</h4>
                        <i class="ri-archive-line ri-xl float-end mt-n4"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm text-white h-100" style="background:linear-gradient(135deg,#10b981,#34d399);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Unidades en Inventario</p>
                        <h4 class="fw-bold">{{ number_format($totalInventario) }}</h4>
                        <i class="ri-stack-line ri-xl float-end mt-n4"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm text-white h-100" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Bajo Stock</p>
                        <h4 class="fw-bold">{{ $bajoStock->count() }}</h4>
                        <i class="ri-alert-line ri-xl float-end mt-n4"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm text-white h-100" style="background:linear-gradient(135deg,#ef4444,#f87171);">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Sin Stock</p>
                        <h4 class="fw-bold">{{ $sinStock->count() }}</h4>
                        <i class="ri-close-circle-line ri-xl float-end mt-n4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            {{-- Gráfica stock por categoría --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-semibold mb-0">📦 Stock por Categoría</h6>
                    </div>
                    <div class="card-body">
                        <div id="chartStockCategoria" style="min-height:300px;"></div>
                    </div>
                </div>
            </div>

            {{-- Productos sin stock --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-3">
                        <h6 class="fw-semibold mb-0">🚨 Productos Sin Stock</h6>
                        <span class="badge bg-danger">{{ $sinStock->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height:300px; overflow-y:auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Stock Mínimo</th>
                                        <th class="text-center">Disponible</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sinStock as $inv)
                                    <tr>
                                        <td>{{ $inv->producto->product_name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $inv->stock_minimo }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">0</span>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-4">✅ Todo con stock disponible</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bajo stock detallado --}}
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-3">
                        <h6 class="fw-semibold mb-0">⚠️ Productos con Stock Bajo</h6>
                        <span class="badge bg-warning text-dark">{{ $bajoStock->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-center">Mínimo</th>
                                        <th class="text-center">Déficit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bajoStock as $inv)
                                    <tr>
                                        <td>{{ $inv->producto->product_name ?? 'N/A' }}</td>
                                        <td class="text-center fw-bold {{ $inv->stock == 0 ? 'text-danger' : 'text-warning' }}">
                                            {{ $inv->stock }}
                                        </td>
                                        <td class="text-center">{{ $inv->stock_minimo }}</td>
                                        <td class="text-center text-danger">
                                            -{{ max(0, $inv->stock_minimo - $inv->stock) }}
                                        </td>
                                    </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-4">✅ Sin alertas de stock.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endrole

</div>
@endsection

@section('specificpagescripts')
 <script src="{{ asset('assets/js/chart-custom.js') }}"></script>
<script src="{{ asset('assets/js/customizer.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ... tus gráficas
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    @hasanyrole('SuperAdmin|Gerente')

    // ── Ventas diarias 30 días ─────────────────────────────────────────────
    const ventasDiariasData = @json($ventasDiarias);
    new ApexCharts(document.getElementById('chartVentasDiarias'), {
        chart: { type: 'area', height: 300, toolbar: { show: false }, sparkline: { enabled: false } },
        series: [{ name: 'Ventas ($)', data: ventasDiariasData.map(d => d.total) }],
        xaxis: { categories: ventasDiariasData.map(d => d.fecha), labels: { rotate: -45, style: { fontSize: '11px' } } },
        yaxis: { labels: { formatter: v => '$' + v.toLocaleString('es-MX') } },
        colors: ['#6366f1'],
        fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
        stroke: { curve: 'smooth', width: 2 },
        tooltip: { y: { formatter: v => '$' + v.toLocaleString('es-MX', {minimumFractionDigits:2}) } },
        grid: { borderColor: '#f1f5f9' },
    }).render();

    // ── Métodos de pago (donut) ────────────────────────────────────────────
    const metodosData = @json($ventasPorMetodo);
    new ApexCharts(document.getElementById('chartMetodoPago'), {
        chart: { type: 'donut', height: 280 },
        series: metodosData.map(d => d.total),
        labels: metodosData.map(d => d.metodo),
        colors: ['#10b981','#3b82f6','#f59e0b','#ef4444','#8b5cf6'],
        legend: { position: 'bottom' },
        plotOptions: { pie: { donut: { size: '60%' } } },
        tooltip: { y: { formatter: v => v + ' órdenes' } },
    }).render();

    // ── Ingresos vs Egresos ────────────────────────────────────────────────
    const ingresosRaw = @json($ingresosMensuales);
    const meses = [...new Set(ingresosRaw.map(d => d.mes))];
    const ingresos = meses.map(m => {
        const r = ingresosRaw.find(d => d.mes === m && d.tipo_transaccion === 'ingreso');
        return r ? parseFloat(r.total) : 0;
    });
    const egresos = meses.map(m => {
        const r = ingresosRaw.find(d => d.mes === m && d.tipo_transaccion === 'egreso');
        return r ? parseFloat(r.total) : 0;
    });
    new ApexCharts(document.getElementById('chartIngresosEgresos'), {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        series: [
            { name: 'Ingresos', data: ingresos },
            { name: 'Egresos',  data: egresos },
        ],
        xaxis: { categories: meses },
        colors: ['#10b981', '#ef4444'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
        yaxis: { labels: { formatter: v => '$' + v.toLocaleString('es-MX') } },
        tooltip: { y: { formatter: v => '$' + v.toLocaleString('es-MX', {minimumFractionDigits:2}) } },
        legend: { position: 'top' },
        grid: { borderColor: '#f1f5f9' },
    }).render();

    // ── Top 5 productos ────────────────────────────────────────────────────
    const topData = @json($topProductos);
    new ApexCharts(document.getElementById('chartTopProductos'), {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        series: [{ name: 'Unidades vendidas', data: topData.map(d => d.vendidos) }],
        xaxis: { categories: topData.map(d => d.product_name) },
        colors: ['#3b82f6'],
        plotOptions: { bar: { borderRadius: 4, horizontal: true } },
        tooltip: { y: { formatter: v => v + ' uds.' } },
        grid: { borderColor: '#f1f5f9' },
    }).render();

    @endhasanyrole


    @role('Cajero')

    // ── Ventas por hora ────────────────────────────────────────────────────
    const horasData = @json($ventasPorHora);
    new ApexCharts(document.getElementById('chartVentasHora'), {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        series: [{ name: 'Monto ($)', data: horasData.map(d => parseFloat(d.monto)) }],
        xaxis: { categories: horasData.map(d => d.hora + ':00'), title: { text: 'Hora del día' } },
        colors: ['#10b981'],
        plotOptions: { bar: { borderRadius: 4 } },
        yaxis: { labels: { formatter: v => '$' + v.toLocaleString('es-MX') } },
        tooltip: { y: { formatter: v => '$' + v.toLocaleString('es-MX', {minimumFractionDigits:2}) } },
        grid: { borderColor: '#f1f5f9' },
    }).render();

    @endrole


    @role('Almacen')

    // ── Stock por categoría ────────────────────────────────────────────────
    const stockData = @json($stockPorCategoria);
    new ApexCharts(document.getElementById('chartStockCategoria'), {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        series: [{ name: 'Unidades', data: stockData.map(d => d.stock) }],
        xaxis: { categories: stockData.map(d => d.categoria) },
        colors: ['#3b82f6'],
        plotOptions: { bar: { borderRadius: 4, horizontal: true } },
        tooltip: { y: { formatter: v => v + ' uds.' } },
        grid: { borderColor: '#f1f5f9' },
    }).render();

    @endrole

});
</script>
@endsection