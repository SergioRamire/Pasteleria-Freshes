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
use App\Models\OrderDetails;

class MyInventarioController extends Controller
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
                'products.product_image as product_image', // <-- ¡ESTA ES LA CLAVE!
                'branches.nombre as sucursal',
                'c.name as category_name',
                'm.nombre as marca_nombre'
            )
            ->where('inventarios.branche_id', $user->branche_id);

        // Filtro por búsqueda
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'like', "%$search%")
                    ->orWhere('products.product_code', 'like', "%$search%")
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

        // Ordenamiento sortable corregido (faltaba coma después de 'product_code')
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

        return view('my_inventario.index', compact('inventarios', 'sucursal', 'categories', 'marcas'))
            ->with('i', (request()->input('page', 1) - 1) * $row);
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
        $inventario = Inventario::findOrFail($id);

        $invent = DB::table('inventarios')
        ->join('products', 'inventarios.product_id', '=', 'products.id')
        ->join('marcas','marcas.id','=','products.marca_id')
        ->join('suppliers','suppliers.id','=','marcas.suppliers_id')
        ->join('categories as c', 'products.category_id', '=', 'c.id')
        ->join('branches', 'inventarios.branche_id', '=', 'branches.id')
        ->leftJoin('equivalencias', 'equivalencias.id', '=', 'products.equivalencia_id')
        ->leftJoin('satclaves', 'satclaves.id', '=', 'products.satclave_id')
        ->select(
            'inventarios.id as id_inventario',
            'inventarios.estado',
            'inventarios.disponibilidad',
            'inventarios.stock_minimo',
            'inventarios.stock',
            'products.product_name as nombre_producto',
            'branches.nombre as nombre_sucursal',
            'products.*',
            'c.name as category_name',
            'marcas.nombre as marca_nombre',
            'suppliers.name as proveedor',
            'equivalencias.nombre as unidad',
            'satclaves.c_ClaveProdServ as codigo_sat'
        )
         ->where('inventarios.id', $inventario->id)
        ->first();

        return view('my_inventario.show', compact('invent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $inventario = Inventario::findOrFail($id);

        $invent = DB::table('inventarios')
        ->join('products', 'inventarios.product_id', '=', 'products.id')
        ->join('categories as c', 'products.category_id', '=', 'c.id')
        ->join('marcas as m', 'products.marca_id', '=', 'm.id')
        ->join('suppliers','suppliers.id','=','m.suppliers_id')
        ->join('branches', 'inventarios.branche_id', '=', 'branches.id')
        ->leftJoin('equivalencias', 'equivalencias.id', '=', 'products.equivalencia_id')
        ->leftJoin('satclaves', 'satclaves.id', '=', 'products.satclave_id')
        ->select(
            'inventarios.id as id_inventario',
            'inventarios.estado',
            'inventarios.disponibilidad',
            'inventarios.stock_minimo',
            'inventarios.stock',
            'products.product_name as nombre_producto',
            'branches.nombre as nombre_sucursal',
            'products.*',
            'c.name as categoria',
            'm.nombre as marca_nombre',
            'suppliers.name as proveedor',
            'equivalencias.nombre as unidad',
            'satclaves.c_ClaveProdServ as codigo_sat'
        )
         ->where('inventarios.id', $inventario->id)
        ->first();

        return view('my_inventario.edit', compact('invent'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $inventario = Inventario::findOrFail($id);

        $rules = [
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'estado' => 'required|boolean',
            'disponibilidad' => 'required|boolean',
        ];

        $validatedData = $request->validate($rules);

        // Lógica adicional: si el stock es 0, la disponibilidad es 0 (inactivo)
        if ($validatedData['stock'] == 0) {
            $validatedData['disponibilidad'] = 0;
        } else {
            $validatedData['disponibilidad'] = 1;
        }

        $inventario->update($validatedData);

        return redirect()->route('myinventarios.index')
            ->with('success', '¡El inventario de tu sucursal ha sido actualizado exitosamente!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $transaccion = Inventario::findOrFail($id);

        $existeEnOrder = OrderDetails::where('inventario_id', $transaccion->id)->exists();

         if ($existeEnOrder) {
            // No se puede eliminar porque hay inventario relacionado
            return Redirect::route('myinventarios.index')
                ->with('error', '¡No se puede eliminar el producto porque tiene una venta registrada!');
        }

        $transaccion->delete();

        return redirect()->route('myinventarios.index')->with('success', 'Producto eliminada correctamente de mi inventario.');
    }
    public function imprimir_stock($id)
    {
        $sucursal = Branche::findOrFail($id);

        $query = Inventario::query()
            ->join('products', 'products.id', '=', 'inventarios.product_id')
            ->join('marcas as m', 'products.marca_id', '=', 'm.id')
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
                'm.nombre as marca_nombre'
            )
            ->where('inventarios.branche_id', $id)
            ->whereColumn('inventarios.stock', '<=', 'inventarios.stock_minimo') // ✅ Aquí es donde se corrige
            ->get();

        return view('my_inventario.imprimir_stock', compact('query', 'sucursal'));
    }

}
