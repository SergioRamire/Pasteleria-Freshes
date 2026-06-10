<?php


namespace App\Http\Controllers\Dashboard;

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
use App\Models\Supplier; // ✅ AÑADIDO


class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        $row = (int) request('row', 30);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        $inventarios = Inventario::query()
            ->Join('products', 'products.id', '=', 'inventarios.product_id')
            ->Join('branches', 'branches.id', '=', 'inventarios.branche_id')
            ->select(
                'inventarios.id',
                'inventarios.stock',
                'inventarios.stock_minimo',
                'inventarios.estado',
                'products.product_name as producto',
                'products.product_code as product_code',
                'products.codigo_barras as codigo_barras',
                'products.selling_price as precio_publico',
                'products.product_image as product_image',
                'branches.nombre as sucursal',
            )
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('products.product_name', 'like', '%' . $search . '%')
                    ->orWhere('products.product_code', 'like', '%' . $search . '%')
                    ->orWhere('products.codigo_barras', 'like', '%' . $search . '%')
                    ->orWhere('products.product_code', 'like', '%' . $search . '%')
                    ->orWhere('inventarios.stock', 'like', '%' . $search . '%');
                });
            })
            ->when(request('sucursal'), function ($query, $sucursal) {
                $query->where('inventarios.branche_id', $sucursal);
            })
            ->orderBy('inventarios.id', 'asc')
            ->paginate($row)
            ->appends(request()->query());

        $sucursales = Branche::all();

        return view('inventarios.index', compact('inventarios', 'sucursales'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe estar entre 1 y 100.');
        }

        $query = Product::select('products.*')
            ->join('categories as c', 'products.category_id', '=', 'c.id')
            ->join('marcas as m', 'products.marca_id', '=', 'm.id')
            ->addSelect([
                'c.name as category_name',
                'm.nombre as marca_nombre',
            ]);

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%")
                    ->orWhere('codigo_barras', 'like', "%{$search}%");
            });
        }

        if ($categoryId = request('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($marcaId = request('marca_id')) {
            $query->where('marca_id', $marcaId);
        }

        if ($proveedorId = request('proveedor_id')) {
            $query->whereHas('marca.supplier', function ($q) use ($proveedorId) {
                $q->where('id', $proveedorId);
            });
        }

        $query = $query->sortable([
            'product_name',
            'category_name',
            'marca_nombre',
            'selling_price'
        ]);

        $products = $query->paginate($row)->appends(request()->query());

        $categories = Category::all();
        $marcas = Marca::all();
        $sucursales = Branche::all();
        $proveedores = Supplier::all(); // ✅ AÑADIDO

        return view('inventarios.create', compact(
            'sucursales',
            'marcas',
            'categories',
            'products',
            'proveedores' // ✅ AÑADIDO
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:products,id',
            'sucursal_id' => 'required|exists:branches,id',
            'stock_minimo'=>'required|integer',
            'stock'=>'required|integer',
        ]);

        Inventario::create([
            'product_id' => $request->producto_id,
            'branche_id' => $request->sucursal_id,
            'estado'=>1,
            'disponibilidad'=>1,
            'stock' => $request->stock,
            'stock_minimo'=>$request->stock_minimo,
        ]);

        return redirect()->route('inventarios.index')->with('success', 'Producto agregado a la sucursal correctamente.');
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
    public function edit(Inventario $inventario)
    {
        $id = $inventario->id;
        $invent = DB::table('inventarios')
        ->join('products', 'inventarios.product_id', '=', 'products.id')
        ->join('marcas','marcas.id','=','products.marca_id')
        ->join('categories as c', 'products.category_id', '=', 'c.id')
        ->join('suppliers','suppliers.id','=','marcas.suppliers_id')
        ->join('branches', 'inventarios.branche_id', '=', 'branches.id')
        ->leftJoin('equivalencias', 'equivalencias.id', '=', 'products.equivalencia_id')
        ->leftJoin('satclaves', 'satclaves.id', '=', 'products.satclave_id')
        ->select(
            'inventarios.id as id_inventarios',
            'inventarios.estado',
            'inventarios.disponibilidad',
            'inventarios.stock_minimo',
            'inventarios.stock',
            'products.*',
            'branches.nombre as nombre_sucursal',
            'marcas.nombre as marca',
            'suppliers.name as proveedor',
            'c.name as categoria',
            'equivalencias.nombre as unidad',
            'satclaves.c_ClaveProdServ as codigo_sat'
        )
        ->where('inventarios.id', $id)
        ->first();

        return view('inventarios.edit', compact('invent'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventario $inventario)
    {
       $rules = [
            'estado' => 'required|boolean',
            'disponibilidad' => 'required|boolean',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
        ];

        $validatedData = $request->validate($rules);

        Inventario::where('id', $inventario->id)->update($validatedData);

        return redirect()->route('inventarios.index')->with('success', '¡El inventario ha sido actualizado exitosamente!');
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
            return Redirect::route('inventarios.index')
                ->with('error', '¡No se puede eliminar el producto porque tiene una venta registrada!');
        }

        $transaccion->delete();

        return redirect()->route('inventarios.index')->with('success', 'Producto eliminada correctamente del inventario.');
    }

}
