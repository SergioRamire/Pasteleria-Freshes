<?php
namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Gloudemans\Shoppingcart\Facades\Cart;
// use App\Models\Compra;
// use App\Models\Traspasosdetalle;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Marca;
use App\Models\Product;
use App\Models\Branche;
// use App\Models\Inventario;
// use App\Models\Historiale;
// use App\Models\ComprasDetalle;
use App\Models\Listproduct;
use App\Models\Detailslistproduct;

class CompraListProveedorController extends Controller
{
    //Index para seleccionar productos y mandar al carrito
    public function index(Request $request)
    {
        // dd("hola");
        // $empleado = auth()->user();
        $row = (int) $request->input('row', 20);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        $marcaId = $request->input('marca_id');
        $proveedorId = $request->input('proveedor_id');
        $categoriaId = $request->input('category_id');
        $busqueda = $request->input('search');

        $query = Product::query()
            // ->join('products', 'products.id', '=', 'inventarios.product_id')
            ->join('categories as c', 'products.category_id', '=', 'c.id')
            ->join('marcas as m', 'products.marca_id', '=', 'm.id')
            ->join('suppliers as su', 'm.suppliers_id', '=', 'su.id')
              ->select(
                    'products.*',
                    'c.name as category_name',
                    'm.nombre as marca_nombre',
                    'su.name as proveedor_nombre',
                    'su.id as proveedor_id'
                );

        // Aplicar filtros si existen
        if ($marcaId) {
            $query->where('products.marca_id', $marcaId);
        }

        if (!empty($proveedorId)) {
            $query->where('su.id', $proveedorId); // ← aquí está el proveedor real
        }

        if ($categoriaId) {
            $query->where('products.category_id', $categoriaId);
        }

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('products.product_name', 'LIKE', "%$busqueda%")
                ->orWhere('su.name', 'LIKE', "%$busqueda%")
                 ->orWhere('products.codigo_barras', 'LIKE', "%$busqueda%")
                 ->orWhere('products.product_code', 'LIKE', "%$busqueda%");
            });
        }
        // Filtros...
        $products = $query->sortable([
            'product_name',
            'category_name',
            'marca_nombre',
            'codigo_barras',
            'product_code',
        ])
            ->paginate($row)
            ->appends($request->query());

        return view('carritocomprasproveedor.index', [
            'productItem'  => Cart::instance('compraslitproveedor')->content(),
            'products'     => $products,
            'categories'   => Category::all(),
            'marcas' => Marca::all(),
            'proveedores' => Supplier::all(),
        ]);
    }

    public function addByBarcode(Request $request)
    {
        // Validar entrada
        $barcode = $request->input('barcode');

        if (!$barcode) {
            return response()->json([
                'success' => false,
                'message' => 'Código de barras no proporcionado.'
            ], 400);
        }

        // Buscar producto por código de barras
        $producto = Product::where('codigo_barras', $barcode)->first();

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.'
            ], 404);
        }

        $proveedor = Marca::query()
            ->join('suppliers', 'suppliers.id', '=', 'marcas.suppliers_id')
            ->select('marcas.nombre as marca', 'suppliers.name as proveedor')
            ->where('marcas.id', $producto->marca_id)
            ->first();

        // Agregar al carrito 'compras'
        Cart::instance('compraslitproveedor')->add([
            'id'      => $producto->id,
            'name'    => $producto->product_name,
            'qty'     => 1,
            'price'   => $producto->selling_price,
            'weight'  => 1,
            'options' => [
                'image'         => $producto->product_image,
                'product_code'  => $producto->product_code,  // ← Cambiar a 'product_code'
                'codigo_barras' => $producto->codigo_barras,
                'marca'         => $proveedor->marca ?? 'Sin marca',
                'proveedor'     => $proveedor->proveedor ?? 'Sin proveedor',
            ]
        ]);

        return response()->json([
            'success'      => true,
            'product_name' => $producto->product_name
        ]);
    }
    public function addCart(Request $request)
    {
        // Validar entrada
        $validatedData = $request->validate([
            'id' => 'required|numeric',
        ]);

        // Obtener el producto
        $producto = Product::find($validatedData['id']);

        if (!$producto) {
            return Redirect::back()->with('error', 'Producto no encontrado.');
        }

        // Obtener proveedor y marca relacionados
        $proveedor = Marca::query()
            ->join('suppliers', 'suppliers.id', '=', 'marcas.suppliers_id')
            ->select('marcas.nombre as marca', 'suppliers.name as proveedor')
            ->where('marcas.id', $producto->marca_id)
            ->first();

        // Agregar al carrito de compras del proveedor
        Cart::instance('compraslitproveedor')->add([
            'id'      => $producto->id,
            'name'    => $producto->product_name,
            'qty'     => 1,
            'price'   => $producto->selling_price,
            'weight'  => 1,
            'options' => [
                'image'         => $producto->product_image,
                'product_code'  => $producto->product_code,  // ← Cambiar a 'product_code'
                'codigo_barras' => $producto->codigo_barras,
                'marca'         => $proveedor->marca ?? 'Sin marca',
                'proveedor'     => $proveedor->proveedor ?? 'Sin proveedor',
            ]
        ]);

        return Redirect::back()->with('success', 'Producto agregado al carrito.');
    }


    public function updateCart(Request $request, $rowId)
    {
        $validatedData = $request->validate([
            'qty' => 'required|numeric|min:1',
            'product_id' => 'required|exists:products,id',
        ]);

        $producto = Product::where('id', $request->product_id)->first();

        // Obtener el item actual en el carrito
        $cartItem = Cart::instance('compraslitproveedor')->get($rowId);

        // dd($cartItem);

        $cantidadActual = $cartItem->qty;
        $cantidadNueva = $validatedData['qty'];

        // Calcular la diferencia
        $diferencia = $cantidadNueva - $cantidadActual;

        // Actualizar carrito
       Cart::instance('compraslitproveedor')->update($rowId, $cantidadNueva);

        return Redirect::back()->with('success', 'Cantidad actualizada correctamente.');
    }

    public function deleteCart(String $rowId)
    {
        $item = Cart::instance('compraslitproveedor')->get($rowId);
        if ($item) {
            // Eliminar el item del carrito
            Cart::instance('compraslitproveedor')->remove($rowId);

            return Redirect::back()->with('success', 'Producto eliminado del carrito.');
        }

        return Redirect::back()->with('error', 'Producto no encontrado en el carrito.');
    }

    public function vaciarCarrito()
    {
        Cart::instance('compraslitproveedor')->destroy(); // Vacía todo el carrito

        return redirect()->back()->with('success', 'Carrito vaciado y stock actualizado.');
    }

    public function createInvoice(Request $request)
    {
            // Obtiene el contenido actual del carrito
            $cartContent = Cart::instance('compraslitproveedor')->content();
            // dd($cartContent);
            $empleado = auth()->user();
            $sucursales = Branche::all();

            return view('carritocomprasproveedor.create-invoice', [
                'empleado' => $empleado,
                'content' => $cartContent,
                'sucursales' => $sucursales
            ]);
    }

    public function guardar(Request $request)
    {
        // dd($request->all());
        $rules = [
            'sucursal_origen' => 'required',
            'observaciones' =>'nullable|string',
        ];

        $list_code = IdGenerator::generate([
            'table' => 'listproducts',
            'field' => 'codigo',
            'length' => 8, // Puedes ajustar la longitud
            'prefix' => 'LIST-',
            'reset_on_prefix_change' => false
        ]);


        $empleado = auth()->user();
        $sucursal = Branche::where('id','empleado.branche_id')->get();
        $validatedData = $request->validate($rules);
        $validatedData['fecha'] = Carbon::now()->timezone('America/Mexico_City')->format('Y-m-d');
        $validatedData['hora'] = Carbon::now()->timezone('America/Mexico_City')->format('H:i:s');
        $validatedData['responsable'] = $empleado->id;
        $validatedData['codigo'] = $list_code;

        // Insertar lita
        $listaProduct_id = Listproduct::insertGetId($validatedData);

        $contents = Cart::instance('compraslitproveedor')->content();

        foreach ($contents as $content) {
            Detailslistproduct::create([
                'listproduct_id'     => $listaProduct_id,
                'producto_id'   => $content->id,
                'cantidad'      => $content->qty,
            ]);
        }
        Cart::instance('compraslitproveedor')->destroy();
        return Redirect::route('listasproductosproveedor.index')->with('success', 'Lista creada exitosamente!');
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
    public function show(string $id)
    {
        //
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
