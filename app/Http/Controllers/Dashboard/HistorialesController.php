<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\Product;
use App\Models\Historiale;
// use App\Models\Marca;
// use App\Models\Branche;
// use App\Models\OrderDetails;

class HistorialesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 20);
        $hoy = now()->toDateString();
        $hasSortable = request()->has('sort');

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe estar entre 1 y 100.');
        }

        $query = Historiale::query()
            ->join('products', 'historiales.product_id', '=', 'products.id')
            ->select('historiales.*', 'products.product_name as producto', 'products.product_code as codigo');

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'like', "%{$search}%")
                ->orWhere('products.product_code', 'like', "%{$search}%")
                ->orWhere('historiales.accion', 'like', "%{$search}%");
            });
        }

        if ($orderDate = request('order_date')) {
            $query->whereDate('historiales.fecha', $orderDate);
        }

        // Ordenar por registros del día actual primero, luego los demás por fecha descendente
        if (!$hasSortable) {
            $query->orderByRaw("DATE(fecha) = ? DESC", [$hoy])  // Primero los de hoy
                ->orderBy('fecha', 'desc');                   // Luego en orden descendente
        }

        $query = $query->sortable([
            'fecha',
            'accion',
        ]);

        $historiales = $query->paginate($row)->appends(request()->query());

        return view('historial.index', compact('historiales'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $historial = Historiale::query()
            ->join('users', 'historiales.user_id', '=', 'users.id')
            ->join('products', 'historiales.product_id', '=', 'products.id')
            ->select('historiales.*', 'products.product_name as producto', 'products.product_code as codigo', 'users.name as usuario')
            ->where('historiales.id', $id)
            ->firstOrFail();

        return view('historial.show', compact('historial'));
    }

    public function analisis(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->subMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->now()->timezone('America/Mexico_City')->toDateString());

        $buscar = $request->input('search');

        $query = Historiale::query()
            ->join('products', 'historiales.product_id', '=', 'products.id')
            ->select(
                'historiales.id',
                'historiales.fecha',
                'historiales.descripcion',
                'products.product_name as producto',
                'products.product_code as codigo'
            )
            // ->whereBetween('historiales.fecha', [$fechaInicio, $fechaFin]);
            ->whereDate('historiales.fecha', '>=', $fechaInicio)
            ->whereDate('historiales.fecha', '<=', $fechaFin);

        // Filtro de búsqueda (si existe)
        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('products.product_name', 'like', "%{$buscar}%")
                ->orWhere('products.product_code', 'like', "%{$buscar}%");
            });
        }

        $query->orderBy('products.product_name')
            ->orderBy('historiales.fecha');

        $historial = $query->get();

        return view('historial.analisis', compact('historial', 'fechaInicio', 'fechaFin', 'buscar'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
