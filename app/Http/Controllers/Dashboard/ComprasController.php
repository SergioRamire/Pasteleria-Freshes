<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Models\Compra;
// use App\Models\Inventario;
use App\Models\Comprasdetalle;
use App\Models\Category;
// use App\Models\Supplier;
use App\Models\Marca;

class ComprasController extends Controller
{
    /**
     * Index de tabla
     */
    public function index()
    {
         $hoy = now()->timezone('America/Mexico_City')->toDateString();
        $empleado = auth()->user();
        $row = (int) request('row', 20);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        $query = Compra::query()
            // ->leftjoin('traspasosdetalles', 'traspasosdetalles.traspaso_id', '=', 'traspasos.id')
            // ->leftjoin('products', 'products.id', '=', 'traspasosdetalles.producto_id')
            // ->leftjoin('branches as destino', 'destino.id', '=', 'traspasos.sucursal_destino')
            ->leftjoin('branches as origen', 'origen.id', '=', 'compras.sucursal_origen')
            ->select(
                'compras.*',
                'origen.nombre as sucursal_origen_nombre',
                // 'destino.nombre as sucursal_destino_nombre'
            )
            ->distinct()
            ->where('compras.sucursal_origen', $empleado->branche_id)
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('compras.codigo', 'like', '%' . $search . '%')
                    ->orWhere('compras.fecha', 'like', '%' . $search . '%');
                });
            })
            ->when(request('order_date'), function ($query, $order_date) {
                $query->whereDate('compras.fecha', $order_date);
            });

            // ->when(request('estado'), function ($query, $estado) {
            //     $query->where('traspasos.estado', $estado);
            // });

            // Ordenamiento
            $hasSortable = request()->has('sort');

            // Solo aplica orden fijo si NO hay sortable en la URL
            if (!$hasSortable) {
                $query->orderByRaw("fecha = ? DESC", [$hoy])
                    ->orderBy('fecha', 'desc')
                    ->orderBy('hora', 'asc');
            }

            $compras = $query->sortable([
                'codigo',
                'hora',
                'fecha',
            ])
            ->orderBy('compras.id', 'asc')
            ->paginate($row)
            ->appends(request()->query());

        return view('compras.index', compact('compras'));
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
    $compra = Compra::query()
        ->leftJoin('branches as origen', 'origen.id', '=', 'compras.sucursal_origen')
        ->leftJoin('users as user', 'user.id', '=', 'compras.responsable')
        ->select(
            'compras.*',
            'user.name as empleado',
            'origen.nombre as sucursal_origen_nombre'
        )
        ->where('compras.id', $id)
        ->firstOrFail();

    $productos_compras = Comprasdetalle::query()
        ->join('products', 'products.id', '=', 'comprasdetalles.producto_id')
        ->join('marcas as mar', 'mar.id', '=', 'products.marca_id')
        ->join('suppliers as su', 'su.id', '=', 'mar.suppliers_id')
        ->leftjoin('equivalencias','equivalencias.id','=','products.equivalencia_id')
        ->select(
            'comprasdetalles.cantidad',
            'products.product_name as producto',
            'products.product_code as codigo',
            'su.name as proveedor',
            'mar.nombre as marca',
            'equivalencias.nombre as equivalencia'
        )
        ->where('comprasdetalles.compra_id', $id)
        ->paginate(15);

    return view('compras.show', compact('compra', 'productos_compras'));
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
