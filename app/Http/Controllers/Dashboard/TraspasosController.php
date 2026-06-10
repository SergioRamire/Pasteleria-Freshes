<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;

use Gloudemans\Shoppingcart\Facades\Cart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Traspaso;
use App\Models\Inventario;
use App\Models\Traspasosdetalle;


class TraspasosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
            ->where('traspasos.sucursal_origen', $empleado->branche_id)
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
            ->orderBy('traspasos.id', 'asc')
            ->paginate($row)
            ->appends(request()->query());

        return view('traspasos.index_origen', compact('traspasos'));
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
        $traspaso = Traspaso::query()
            ->leftJoin('branches as destino', 'destino.id', '=', 'traspasos.sucursal_destino')
            ->leftJoin('branches as origen', 'origen.id', '=', 'traspasos.sucursal_origen')
            ->select(
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
            ->leftJoin('equivalencias','equivalencias.id','=','products.equivalencia_id')
            ->select(
                'traspasosdetalles.cantidad as cantidad',
                'products.product_name as producto',
                'products.product_code as codigo_producto',
                'products.product_image as product_image',
                'products.selling_price',
                'equivalencias.nombre as unidad'
            )
            ->where('traspasosdetalles.traspaso_id', $id)
            ->paginate(15);

        return view('traspasos.show_origen', compact('traspaso', 'productos_traspasos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $detalle_traspasos = Traspasosdetalle::query()
            ->join('traspasos', 'traspasos.id', '=', 'traspasosdetalles.traspaso_id')
            ->select(
                'traspasos.sucursal_origen as sucursal_origen',
                'traspasos.sucursal_destino as sucursal_destino',
                'traspasosdetalles.producto_id as producto_id',
                'traspasosdetalles.cantidad as cantidad'
            )
            ->where('traspasos.id', $id)
            ->get();

        foreach ($detalle_traspasos as $item) {
            // 🔹 Buscar inventario en la sucursal DESTINO
            $inventario_destino = Inventario::where('product_id', $item->producto_id)
                ->where('branche_id', $item->sucursal_origen)
                ->first();

            if ($inventario_destino !== null) {
                // Si ya existe, solo aumentamos el stock
                $inventario_destino->stock += $item->cantidad;
                $inventario_destino->save();
            } else {
                // Si no existe, creamos un nuevo registro de inventario
                Inventario::create([
                    'product_id'     => $item->producto_id,
                    'branche_id'     => $item->sucursal_origen,
                    'stock'          => $item->cantidad,
                    'stock_minimo'   => 3,
                    'estado'         => 1,
                    'disponibilidad' => 1,
                ]);
            }
        }

        Traspaso::where('id', $id)->update(['estado' => 'recibido']);

        return redirect()->route('listTraspasos.index')->with('success', '¡Traspaso recibido correctamente!');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $empleado = auth()->user();
        // $inventario = Inventario::findOrFail($id);

        // $rules =[
        //     'cantidad' => 'required|integer|min:1',
        //     'observaciones' => 'nullable|string|max:255',
        // ];

        // $validatedData = $request->validate($rules);
        // $validatedData['producto_id'] = $inventario->product_id;
        // $validatedData['sucursal_origen_id'] = $empleado->branche_id; // Sucursal del empleado autenticado
        // $validatedData['sucursal_destino_id'] = $inventario->branche_id; // Sucursal del inventario
        // $validatedData['fecha'] = now()->timezone('America/Mexico_City')->toDateString();
        // $validatedData['hora'] = now()->timezone('America/Mexico_City')->format('H:i:s');
        // $validatedData['user_id'] = $empleado->id; // ID del usuario autenticado
        // $validatedData['estado'] = 'pendiente'; // Estado del traspaso

        // $traspaso = Traspaso::create($validatedData);

        // return redirect()->route('traspasos.index')->with('success', 'Traspaso Solicitado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        // dd($id);
        $traspaso = Traspaso::findOrFail($id);
        $detalle_traspasos = Traspasosdetalle::where('traspaso_id', $traspaso->id)->get();

        // Eliminar los detalles del traspaso
        foreach ($detalle_traspasos as $detalle) {
            $detalle->delete();
        }

        // dd($traspaso);
        $traspaso->delete();

         return redirect()->route('listTraspasos.index')->with('success', '¡Traspaso eliminado correctamente!');
    }
}
