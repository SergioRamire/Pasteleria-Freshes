<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Order;
use App\Models\Product;
use App\Models\Caja;
use App\Models\Branche;
use App\Models\Transaction;
use App\Models\Inventario;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user      = Auth::user();
        $brancheId = $user->branche_id;
        $hoy       = Carbon::today();
        $mes       = Carbon::now()->month;
        $anio      = Carbon::now()->year;

        // ── Base query filtrada por sucursal (excepto SuperAdmin ve todo) ──
        $ordersQuery = Order::query();
        if (!$user->hasRole('SuperAdmin')) {
            $ordersQuery->where('branche_id', $brancheId);
        }

        // ════════════════════════════════════════════
        //  SUPERADMIN + GERENTE
        // ════════════════════════════════════════════
        if ($user->hasRole(['SuperAdmin', 'Gerente'])) {

            // Tarjetas resumen
            $ventasHoy      = (clone $ordersQuery)->whereDate('order_date', $hoy)->sum('total');
            $ventasMes      = (clone $ordersQuery)->whereMonth('order_date', $mes)->whereYear('order_date', $anio)->sum('total');
            $ordenesPendientes = (clone $ordersQuery)->where('order_status', 'pendiente')->count();
            $ordenesCompletas  = (clone $ordersQuery)->where('order_status', 'completada')->count();
            $cjasAbiertas = Caja::where('estado', 'abierta')->where('fecha', $hoy)->count();

            // Gráfica 1: Ventas diarias últimos 30 días
            $ventasDiarias = (clone $ordersQuery)
                ->selectRaw('DATE(order_date) as fecha, SUM(total) as total')
                ->where('order_date', '>=', Carbon::now()->subDays(29))
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get()
                ->map(fn($r) => ['fecha' => $r->fecha, 'total' => (int)$r->total]);

            // Gráfica 2: Ventas por método de pago (pie)
            $ventasPorMetodo = (clone $ordersQuery)
                ->selectRaw('metodo_pago, COUNT(*) as total')
                ->whereNotNull('metodo_pago')
                ->groupBy('metodo_pago')
                ->get()
                ->map(fn($r) => ['metodo' => ucfirst($r->metodo_pago), 'total' => (int)$r->total]);

            // Gráfica 3: Ingresos vs Egresos por mes (últimos 6 meses)
            $transQuery = Transaction::query();
            if (!$user->hasRole('SuperAdmin')) {
                $transQuery->whereHas('caja', fn($q) => $q->where('branche_id', $brancheId));
            }
            $ingresosMensuales = (clone $transQuery)
                ->selectRaw("DATE_FORMAT(fecha, '%Y-%m') as mes, tipo_transaccion, SUM(monto) as total")
                ->where('fecha', '>=', Carbon::now()->subMonths(5)->startOfMonth())
                ->groupBy('mes', 'tipo_transaccion')
                ->orderBy('mes')
                ->get();

            // Gráfica 4: Top 5 productos más vendidos
            $topProductos = \DB::table('order_details')
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->when(!$user->hasRole('SuperAdmin'), fn($q) => $q->where('orders.branche_id', $brancheId))
                ->selectRaw('products.product_name, SUM(order_details.quantity) as vendidos')
                ->groupBy('products.product_name')
                ->orderByDesc('vendidos')
                ->limit(5)
                ->get();

            // Inventario bajo stock
            $bajoStock = Inventario::with('producto')
                ->when(!$user->hasRole('SuperAdmin'), fn($q) => $q->where('branche_id', $brancheId))
                ->whereRaw('stock <= stock_minimo')
                ->orderBy('stock')
                ->limit(8)
                ->get();

            // Caja activa
            $cajaAbierta = Caja::where('branche_id', $brancheId)
                ->where('estado', 'abierta')
                ->whereDate('fecha', $hoy)
                ->first();

            // Resumen de usuarios (solo SuperAdmin)
            $totalUsuarios = $user->hasRole('SuperAdmin') ? User::count() : null;
            $totalSucursales = $user->hasRole('SuperAdmin') ? \App\Models\Branche::count() : null;

            // dd($ventasHoy, $ventasMes, $ordenesPendientes, $ordenesCompletas, $ventasDiarias, $ventasPorMetodo, $ingresosMensuales, $topProductos, $bajoStock, $cajaAbierta, $totalUsuarios, $totalSucursales);

            return view('dashboard.index', compact(
                'ventasHoy', 'ventasMes', 'ordenesPendientes', 'ordenesCompletas', 'cjasAbiertas',
                'ventasDiarias', 'ventasPorMetodo', 'ingresosMensuales', 'topProductos',
                'bajoStock', 'cajaAbierta', 'totalUsuarios', 'totalSucursales'
            ));
        }

        // ════════════════════════════════════════════
        //  CAJERO
        // ════════════════════════════════════════════
        if ($user->hasRole('Cajero')) {

            $cajaHoy = Caja::where('user_id', $user->id)
                ->whereDate('fecha', $hoy)
                ->first();

            $ventasHoy = (clone $ordersQuery)
                ->where('user_id', $user->id)
                ->whereDate('order_date', $hoy)
                ->get();

            $totalCobradoHoy = $ventasHoy->sum('pay');
            $ordenesPendientes = (clone $ordersQuery)
                ->where('payment_status', 'pendiente')
                ->whereDate('order_date', $hoy)
                ->count();

            // Ventas por hora del día (gráfica simple)
            $ventasPorHora = (clone $ordersQuery)
                ->selectRaw('HOUR(created_at) as hora, COUNT(*) as total, SUM(total) as monto')
                ->whereDate('order_date', $hoy)
                ->groupBy('hora')
                ->orderBy('hora')
                ->get();

            return view('dashboard.index', compact(
                'cajaHoy', 'ventasHoy', 'totalCobradoHoy',
                'ordenesPendientes', 'ventasPorHora'
            ));
        }

        // ════════════════════════════════════════════
        //  ALMACÉN
        // ════════════════════════════════════════════
        if ($user->hasRole('Almacen')) {

            $bajoStock = Inventario::with('producto')
                ->where('branche_id', $brancheId)
                ->whereRaw('stock <= stock_minimo')
                ->orderBy('stock')
                ->get();

           $sinStock = Inventario::with('producto')
                ->where('branche_id', $brancheId)
                ->where('stock', 0)
                ->get();

            $productosRecientes = Product::orderByDesc('buying_date')->limit(10)->get();

            $totalProductos  = Product::count();
            $totalInventario = Inventario::where('branche_id', $brancheId)->sum('stock');

            // Gráfica: stock por categoría
            $stockPorCategoria = \DB::table('inventarios')
                ->join('products', 'inventarios.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where('inventarios.branche_id', $brancheId)
                ->selectRaw('categories.name as categoria, SUM(inventarios.stock) as stock')
                ->groupBy('categories.name')
                ->orderByDesc('stock')
                ->limit(8)
                ->get();

            return view('dashboard.index', compact(
                'bajoStock', 'sinStock', 'productosRecientes',
                'totalProductos', 'totalInventario', 'stockPorCategoria'
            ));
        }

        // Fallback genérico
        return view('dashboard.index', []);
    }
}
