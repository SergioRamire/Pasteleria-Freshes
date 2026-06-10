<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Models\Traspaso;
use App\Models\Inventario;
use App\Models\Traspasosdetalle;

class TraspasosRecibidosController extends Controller
{
   public function index()
    {
         $hoy = now()->timezone('America/Mexico_City')->toDateString();
        $empleado = auth()->user();
        $row = (int) request('row', 20);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        $query = Traspaso::query()
            ->leftjoin('branches as destino', 'destino.id', '=', 'traspasos.sucursal_destino')
            ->leftjoin('branches as origen', 'origen.id', '=', 'traspasos.sucursal_origen')
            ->select(
                'traspasos.*',
                'origen.nombre as sucursal_origen_nombre',
                'destino.nombre as sucursal_destino_nombre'
            )
            ->distinct()
            ->where('traspasos.sucursal_destino', $empleado->branche_id)
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('traspasos.codigo', 'like', '%' . $search . '%')
                    ->orWhere('traspasos.fecha', 'like', '%' . $search . '%');
                });
            })
            ->when(request('order_date'), function ($query, $order_date) {
                $query->whereDate('traspasos.fecha', $order_date);
            })

            ->when(request('estado'), function ($query, $estado) {
                $query->where('traspasos.estado', $estado);
            });

            // Ordenamiento
            $hasSortable = request()->has('sort');

            // Solo aplica orden fijo si NO hay sortable en la URL
            if (!$hasSortable) {
                $query->orderByRaw("fecha = ? DESC", [$hoy])
                    ->orderBy('fecha', 'desc')
                    ->orderBy('hora', 'asc');
            }

            $traspasos = $query->sortable([
                'codigo',
                'hora',
                'fecha',
            ])
            ->paginate($row)
            ->appends(request()->query());

        return view('traspasos.index_destino', compact('traspasos'));
    }

    public function show($id)
    {
        // SOLUCIÓN: Agregar el ID al objeto traspaso
        $traspaso = Traspaso::query()
            ->leftJoin('branches as destino', 'destino.id', '=', 'traspasos.sucursal_destino')
            ->leftJoin('branches as origen', 'origen.id', '=', 'traspasos.sucursal_origen')
            ->select(
                'traspasos.id as id', // ← AGREGAR ESTA LÍNEA
                'traspasos.codigo as codigo',
                'traspasos.fecha as fecha',
                'traspasos.hora as hora',
                'traspasos.estado as estado',
                'origen.nombre as sucursal_origen_nombre',
                'destino.nombre as sucursal_destino_nombre'
            )
            ->where('traspasos.id', $id)
            ->first();

        $productos_traspasos = Traspasosdetalle::query()
            ->join('products', 'products.id', '=', 'traspasosdetalles.producto_id')
            ->leftjoin('equivalencias','equivalencias.id','=','products.equivalencia_id')
            ->select(
                'traspasosdetalles.cantidad as cantidad',
                'products.product_name as producto',
                'products.product_image as product_image',
                'products.product_code',
                'equivalencias.nombre as unidad',
            )
            ->where('traspasosdetalles.traspaso_id', $id)
              ->paginate(15);

        return view('traspasos.show_destino', compact('traspaso', 'productos_traspasos','id'));
    }

    public function markAsDespachado(Traspaso $traspaso)
    {
        $traspaso->estado = 'despachado';
        $traspaso->updated_at= now()->timezone('America/Mexico_City')->format('Y-m-d H:i:s');
        $traspaso->save();

        $inventarios = Inventario::query()
        ->join('traspasosdetalles','inventarios.id','=','traspasosdetalles.inventario_id')
        ->join('traspasos', 'traspasos.id', '=', 'traspasosdetalles.traspaso_id')
        ->select(
            'inventarios.id as id',
            'inventarios.stock as stock',
            'traspasosdetalles.cantidad as cantidad'
        )
        ->where('traspasos.id', $traspaso->id)
        ->get();

        foreach($inventarios as $inventario){
             $inventario->stock = max(0, $inventario->stock - $inventario->cantidad);
            $inventario->save();
        }

        return redirect()->route('listTraspasosRecibidos.index')->with('success', 'Traspaso despachado y enviado!!.');
    }

   
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

    public function imprimir($id)
    {
        $traspaso = Traspaso::query()
            ->leftJoin('branches as destino', 'destino.id', '=', 'traspasos.sucursal_destino')
            ->leftJoin('branches as origen', 'origen.id', '=', 'traspasos.sucursal_origen')
            ->leftJoin('users','users.id','=','traspasos.responsable')
            ->select(
                'traspasos.*',
                'users.name as responsable',
                'origen.nombre as sucursal_origen_nombre',
                'destino.nombre as sucursal_destino_nombre'
            )
            ->where('traspasos.id', $id)
            ->first();

        $productos_traspasos = Traspasosdetalle::query()
            ->join('products', 'products.id', '=', 'traspasosdetalles.producto_id')
            ->leftJoin('equivalencias','equivalencias.id','=','products.equivalencia_id') // ← AGREGAR JOIN
            ->select(
                'traspasosdetalles.cantidad as cantidad',
                'products.product_name as producto',
                'products.product_image as product_image',
                'products.product_code', // ← CAMBIAR de codigo_barras a product_code
                'products.selling_price', // ← AGREGAR precio de venta
                'equivalencias.nombre as unidad' // ← AGREGAR unidad
            )
            ->where('traspasosdetalles.traspaso_id', $id)
            ->get(); // ← CAMBIAR de paginate a get() para la impresión

        return view('traspasos.imprimir', compact('traspaso', 'productos_traspasos'));
    }
}
