<?php

namespace App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\Product;
use App\Models\Category;
use App\Models\Marca;
use App\Models\Branche;
use App\Models\Equivalencia;
use App\Models\ConversionHistory;

use Milon\Barcode\DNS1D;
use Picqer\Barcode\BarcodeGeneratorHTML;


class ConversionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $row = (int) request('row', 30);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        // Inicio de la consulta
        $query = Inventario::query()
            ->join('products', 'products.id', '=', 'inventarios.product_id')
            ->join('categories as c', 'products.category_id', '=', 'c.id')
            ->join('marcas as m', 'products.marca_id', '=', 'm.id')
            ->join('branches', 'branches.id', '=', 'inventarios.branche_id')
            ->leftjoin('equivalencias', 'equivalencias.id', '=', 'products.equivalencia_id')
            ->select(
                'inventarios.id',
                'inventarios.stock',
                'inventarios.stock_minimo',
                'inventarios.estado',
                'products.product_name as producto',
                'products.product_code as product_code',
                'products.codigo_barras as codigo_barras',
                'products.buying_price as precio_compra',
                'products.selling_price as precio_venta',
                'products.product_image as product_image',
                'branches.nombre as sucursal',
                'c.name as category_name',
                'm.nombre as marca_nombre',
                'equivalencias.abreviatura as unidad'
            )
            ->where('inventarios.branche_id', $user->branche_id)
            ->where('inventarios.stock', '>',0);

        // Filtro por búsqueda
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'like', "%$search%")
                    ->orWhere('products.product_code', 'like', "%$search%")
                    ->orWhere('equivalencias.nombre', 'like', "%$search%")
                    ->orWhere('products.codigo_barras', 'like', "%$search%");
            });
        }

        // Filtro por categoría
        if ($categoryId = request('category_id')) {
            $query->where('products.category_id', $categoryId);
        }

        // Filtro por marca
        if ($marcaId = request('marca_id')) {
            $query->where('products.marca_id', $marcaId);
        }

        // Filtro por unidad
        if ($unidadId = request('unidad_id')) {
            $query->where('products.equivalencia_id', $unidadId);
        }

        // Ordenamiento sortable corregido
        $query = $query->sortable([
            'producto',
            'product_code',
            'category_name',
            'marca_nombre',
            'codigo_barras'
        ]);

        $inventarios = $query
            ->orderBy('inventarios.id', 'asc')
            ->paginate($row)
            ->appends(request()->query());

        // Obtener datos de la sucursal
        $sucursal = Branche::find($user->branche_id);

        $categories = Category::all();
        $marcas = Marca::all();
        $unidades = Equivalencia::all();

        return view('conversiones.index', compact('inventarios', 'sucursal', 'categories', 'marcas', 'unidades'))
            ->with('i', (request()->input('page', 1) - 1) * $row);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invent = Inventario::query()
            ->join('products', 'products.id', '=', 'inventarios.product_id')
            ->join('categories as c', 'products.category_id', '=', 'c.id')
            ->join('marcas as m', 'products.marca_id', '=', 'm.id')
            ->join('branches', 'branches.id', '=', 'inventarios.branche_id')
            ->join('equivalencias', 'equivalencias.id', '=', 'products.equivalencia_id')
            ->select(
                'inventarios.id',
                'inventarios.stock',
                'inventarios.stock_minimo',
                'inventarios.estado',
                'products.product_name as producto',
                'products.product_code as product_code',
                'products.codigo_barras as codigo_barras',
                'equivalencias.nombre as unidad',
                'products.product_image as product_image',
                'c.name as category_name',
                'm.nombre as marca_nombre',
            )
            ->where('inventarios.id', $id)
            ->firstOrFail();

        // Barcode Generator
        $generator = new BarcodeGeneratorHTML();

        $barcode = $generator->getBarcode($invent->codigo_barras, $generator::TYPE_CODE_128);

        return view('conversiones.edit', compact('invent', 'barcode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'codigo_producto' => 'required|string|max:255',
            'unidades' => 'required|integer|min:1',
            'equivalencia' => 'required|integer|min:1',
        ]);

        $user = auth()->user();

        $inventario = Inventario::findOrFail($id);

        if ($request->unidades > $inventario->stock) {
            return redirect()->back()->withInput()
                    ->with('error', '¡Ups! Hay algunos errores: La cantidad no puede ser mayor al stock disponible.');
        }

        $productCode = $request->input('codigo_producto');

        // Buscar producto por código
        $producto = \App\Models\Product::with('equivalencia')->where('product_code', $productCode)->first();
        if (!$producto) {
            return redirect()->back()->withInput()
                    ->with('error', '¡Ups! Hay algunos errores: No se encontró un producto con ese código.');
        }

        // Obtener el producto origen con su equivalencia
        $productoOrigen = $inventario->producto()->with('equivalencia')->first();

        // Buscar inventario de destino
        $inventarioDestino = Inventario::where('product_id', $producto->id)
                        ->where('branche_id', $user->branche_id)
                        ->first();

        // Calcular total
        $totalUnidades = $request->unidades * $request->equivalencia;

        // Guardar stocks anteriores para el historial
        $stockOrigenAnterior = $inventario->stock;
        $stockDestinoAnterior = $inventarioDestino ? $inventarioDestino->stock : 0;

        try {
            DB::beginTransaction();

            // Si no existe el inventario destino, crearlo
            if (!$inventarioDestino) {
                $inventarioDestino = new Inventario();
                $inventarioDestino->product_id = $producto->id;
                $inventarioDestino->branche_id = $user->branche_id;
                $inventarioDestino->stock_minimo = 3;
                $inventarioDestino->stock = $totalUnidades;
                $inventarioDestino->estado = 1;
                $inventarioDestino->disponibilidad = 1;
                $inventarioDestino->save();
            } else {
                // Si existe, solo sumamos stock
                $inventarioDestino->stock += $totalUnidades;
                $inventarioDestino->save();
            }

            // Descontar del inventario origen
            $inventario->stock -= $request->unidades;
            $inventario->save();

            // Registrar en el historial
            ConversionHistory::registrarConversion(
                $user->id,
                $user->branche_id,
                $inventario,
                $productoOrigen,
                $request->unidades,
                $inventarioDestino,
                $producto,
                $totalUnidades,
                $request->equivalencia,
                $stockOrigenAnterior,
                $stockDestinoAnterior,
                "Conversión realizada desde panel de conversiones"
            );

            DB::commit();

            return redirect()->route('conversiones.index')->with('success', 'Inventario actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()
                    ->with('error', 'Error al procesar la conversión: ' . $e->getMessage());
        }
    }

    /**
     * Display conversion history
     */
    public function historial()
    {
        $user = auth()->user();
        $row = (int) request('row', 30);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        // Query base para el historial
        $query = ConversionHistory::query()
            ->with(['user', 'branche'])
            ->where('branche_id', $user->branche_id);

        // Filtros
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('producto_origen_nombre', 'like', "%$search%")
                  ->orWhere('producto_destino_nombre', 'like', "%$search%")
                  ->orWhere('producto_origen_codigo', 'like', "%$search%")
                  ->orWhere('producto_destino_codigo', 'like', "%$search%");
            });
        }

        if ($estado = request('estado')) {
            $query->where('estado', $estado);
        }

        if ($fechaInicio = request('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $fechaInicio);
        }

        if ($fechaFin = request('fecha_fin')) {
            $query->whereDate('created_at', '<=', $fechaFin);
        }

        if ($usuarioId = request('user_id')) {
            $query->where('user_id', $usuarioId);
        }

        $historial = $query
            ->orderBy('created_at', 'desc')
            ->paginate($row)
            ->appends(request()->query());

        // Obtener usuarios para el filtro
        $usuarios = \App\Models\User::where('branche_id', $user->branche_id)
            ->select('id', 'name')
            ->get();

        return view('conversiones.historial', compact('historial', 'usuarios'))
            ->with('i', (request()->input('page', 1) - 1) * $row);
    }

    /**
     * Show conversion history details
     */
    public function mostrarDetalle($id)
    {
        $user = auth()->user();

        $conversion = ConversionHistory::with(['user', 'branche', 'productoOrigen', 'productoDestino'])
            ->where('id', $id)
            ->where('branche_id', $user->branche_id)
            ->firstOrFail();

        return view('conversiones.detalle', compact('conversion'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Buscar producto por código para conversiones
     */
    public function buscarProductoPorCodigo($codigo)
    {
        try {
            $producto = Product::query()
                ->join('equivalencias', 'equivalencias.id', '=', 'products.equivalencia_id')
                ->select(
                    'products.id',
                    'products.product_name',
                    'products.product_code',
                    'products.codigo_barras',
                    'equivalencias.nombre as unidad',
                    'equivalencias.abreviatura as unidad_abrev'
                )
                ->where('products.product_code', $codigo)
                ->orWhere('products.codigo_barras', $codigo)
                ->first();

            if ($producto) {
                return response()->json([
                    'success' => true,
                    'producto' => [
                        'id' => $producto->id,
                        'product_name' => $producto->product_name,
                        'product_code' => $producto->product_code,
                        'codigo_barras' => $producto->codigo_barras,
                        'unidad' => $producto->unidad . ' (' . $producto->unidad_abrev . ')'
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}
