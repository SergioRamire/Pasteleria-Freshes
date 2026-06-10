<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Compra;
use App\Models\Traspasosdetalle;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Marca;
use App\Models\Product;
use App\Models\Branche;
use App\Models\Inventario;
use App\Models\Historiale;
use App\Models\ComprasDetalle;

class ComprasSucursalController extends Controller
{
    //Index para seleccionar productos y mandar al carrito
    public function index(Request $request)
    {
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
            ->join('categories as c', 'products.category_id', '=', 'c.id')
            ->join('marcas as m', 'products.marca_id', '=', 'm.id')
            ->join('suppliers as su', 'm.suppliers_id', '=', 'su.id')
            ->leftjoin('equivalencias','equivalencias.id','=','products.equivalencia_id')
              ->select(
                    'products.*',
                    'c.name as category_name',
                    'm.nombre as marca_nombre',
                    'su.name as proveedor_nombre',
                    'su.id as proveedor_id',
                    'equivalencias.nombre as equivalencia'
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

        return view('compras.indexes', [
            'productItem'  => Cart::instance('compras')->content(),
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
        // $producto = Product::where('codigo_barras', $barcode)->first();
        $producto = Product::query()
                    ->leftjoin('equivalencias','equivalencias.id','=','products.equivalencia_id')
                    ->select(
                        'products.id as id',
                        'products.product_name as product_name',
                        'products.codigo_barras as codigo_barras',
                        'products.product_code as product_code',
                        'products.selling_price as selling_price',
                        'products.product_image as product_image',
                        'products.dealer_price as dealer_price',
                        'products.product_image as product_image',
                        'equivalencias.nombre as equivalencia'
                    )
                    ->where('products.codigo_barras',$barcode)->first();

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.'
            ], 404);
        }

        // Agregar al carrito 'compras'
        Cart::instance('compras')->add([
            'id'      => $producto->id,
            'name'    => $producto->product_name,
            'qty'     => 1,
            'price'   => $producto->selling_price,
            'weight'  => 1,
            'options' => [
                'image'         => $producto->product_image,
                'codigo_product' => $producto->product_code,
                'codigo_barras' => $producto->codigo_barras,
                'unidad'=> $producto->equivalencia,
            ]
        ]);

        return response()->json([
            'success'      => true,
            'product_name' => $producto->product_name
        ]);
    }
    public function addCart(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);
        // $producto = Product::where('id', $request->id)->first();

        $producto = Product::query()
                    ->leftjoin('equivalencias','equivalencias.id','=','products.equivalencia_id')
                    ->select(
                        'products.id as id',
                        'products.product_name as product_name',
                        'products.codigo_barras as codigo_barras',
                        'products.product_code as product_code',
                        'products.selling_price as selling_price',
                        'products.product_image as product_image',
                        'products.dealer_price as dealer_price',
                        'products.product_image as product_image',
                        'equivalencias.nombre as equivalencia'
                    )
                    ->where('products.id',$request->id)->first();

        Cart::instance('compras')->add([
            'id'      => $producto->id,
            'name'    => $producto->product_name,
            'qty'     => 1,
            'price'   => $producto->selling_price,
            'weight'  => 1,
            'options' => [
                'image'         => $producto->product_image,
                'codigo_product' => $producto->product_code,
                'codigo_barras' => $producto->codigo_barras,
                'unidad'=> $producto->equivalencia,
            ]
        ]);

        return Redirect::back()->with('success', 'Producto agregado al carrito!');
    }

    public function updateCart(Request $request, $rowId)
    {
        $validatedData = $request->validate([
            'qty' => 'required|numeric|min:1',
            'product_id' => 'required|exists:products,id',
        ]);

        $producto = Product::where('id', $request->product_id)->first();

        // Obtener el item actual en el carrito
        $cartItem = Cart::instance('compras')->get($rowId);
        $cantidadActual = $cartItem->qty;
        $cantidadNueva = $validatedData['qty'];

        // Calcular la diferencia
        $diferencia = $cantidadNueva - $cantidadActual;

        // Actualizar carrito
       Cart::instance('compras')->update($rowId, $cantidadNueva);

        return Redirect::back()->with('success', 'Cantidad actualizada correctamente.');
    }

    public function deleteCart(String $rowId)
    {
        $item = Cart::instance('compras')->get($rowId);
        if ($item) {
            // Eliminar el item del carrito
            Cart::instance('compras')->remove($rowId);

            return Redirect::back()->with('success', 'Producto eliminado del carrito.');
        }

        return Redirect::back()->with('error', 'Producto no encontrado en el carrito.');
    }

    public function vaciarCarrito()
    {
        Cart::instance('compras')->destroy(); // Vacía todo el carrito

        return redirect()->back()->with('success', 'Carrito vaciado y stock actualizado.');
    }

    public function createInvoice(Request $request)
    {
            // Obtiene el contenido actual del carrito
            $cartContent = Cart::instance('compras')->content();
            $empleado = auth()->user();
            $sucursal = Branche::where('id',$empleado->branche_id)->first();

            return view('compras.create-invoice', [
                'empleado' => $empleado,
                'content' => $cartContent,
                'sucursal' => $sucursal
            ]);
    }

    public function storeOrder(Request $request)
    {
        // Procesar cambios de precios
        $purchasePrices = $request->input('purchase_price');//precio_compra
        $sellingPrices = $request->input('selling_price');//precio venta
        $codigoBarras = $request->input('codigo_barras');

        foreach ($purchasePrices as $rowId => $purchasePrice) {
            $purchasePrice = floatval($purchasePrice);
            $sellingPrice = floatval($sellingPrices[$rowId] ?? 0);
            $codigoBarrasValue = $codigoBarras[$rowId] ?? null;
             if ($purchasePrice > 0 && $sellingPrice > 0 && $codigoBarrasValue) {
                 // Buscar producto por código de barras
                 if($sellingPrice <= $purchasePrice){
                    //  Cart::instance('compras')->destroy();
                    return Redirect::route('nuevascompras.index')->with('error', 'El precio de venta de los productos debe ser mayor que el precio de compra.');
                 }
             }
        }

        foreach ($purchasePrices as $rowId => $purchasePrice) {
            $purchasePrice = floatval($purchasePrice);//precio compra
            $sellingPrice = floatval($sellingPrices[$rowId] ?? 0);//precio venta
            $codigoBarrasValue = $codigoBarras[$rowId] ?? null;

            $empleado = auth()->user();

            // Solo si ambos precios son mayores a cero y hay código de barras
            if ($purchasePrice > 0 && $sellingPrice > 0 && $codigoBarrasValue) {
                // Buscar producto por código de barras
                    $producto = Product::where('codigo_barras', $codigoBarrasValue)->first();

                    if($producto->buying_price != $purchasePrice || $producto->selling_price != $sellingPrice) {
                        $descripcion = [];

                        if ($producto->buying_price != $purchasePrice) {
                            $descripcion[] = "Precio Compra, Antes: $" . number_format($producto->buying_price, 2) . ", Despues: $" . number_format($purchasePrice, 2);
                        }
                        if ($producto->selling_price != $sellingPrice) {
                            $descripcion[] = "Precio Venta, Antes: $" . number_format($producto->selling_price, 2) . ", Despues $" . number_format($sellingPrice, 2);
                        }
                        // Actualizar precios del producto
                            $historial = new Historiale();
                            $historial->fecha = now()->timezone('America/Mexico_City')->toDateTimeString();
                            $historial->accion = 'Actualización de precios';
                            $historial->descripcion = implode(", ", $descripcion);
                            $historial->user_id = $empleado->id;
                            $historial->product_id = $producto->id;
                            $historial->save();
                    }

                    if ($producto) {
                        $producto->buying_price = $purchasePrice;
                        $producto->selling_price = $sellingPrice;
                        $producto->save();
                    }
            }
            if ($purchasePrice > 0 ) {
                // Buscar producto por código de barras
                    $producto = Product::where('codigo_barras', $codigoBarrasValue)->first();

                    if($producto->buying_price != $purchasePrice || $producto->selling_price != $sellingPrice) {
                        $descripcion = [];

                        if ($producto->buying_price != $purchasePrice) {
                            $descripcion[] = "Precio Compra, Antes: $" . number_format($producto->buying_price, 2) . ", Despues: $" . number_format($purchasePrice, 2);
                        }
                        // Actualizar precios del producto
                            $historial = new Historiale();
                            $historial->fecha = now()->timezone('America/Mexico_City')->toDateTimeString();
                            $historial->accion = 'Actualización de precios';
                            $historial->descripcion = implode(", ", $descripcion);
                            $historial->user_id = $empleado->id;
                            $historial->product_id = $producto->id;
                            $historial->save();
                    }

                    if ($producto) {
                        $producto->buying_price = $purchasePrice;
                        $producto->save();
                    }

            }
        }

        $contents = Cart::instance('compras')->content();

        // Datos base de la compra
        $validatedData['fecha'] = Carbon::now()->timezone('America/Mexico_City')->format('Y-m-d');
        $validatedData['hora'] = Carbon::now()->timezone('America/Mexico_City')->format('H:i:s');
        $validatedData['responsable'] = $empleado->id;
        $validatedData['sucursal_origen'] = $request->sucursal_id;
        $validatedData['observaciones'] = $request->observaciones;

        // Generar código único tipo COMPR-[sucursal]-[secuencia]
        do {
            $secuencia = str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT); // Ej: '001'
            $codigo = 'COMPR-' . $empleado->branche_id . '-' . $secuencia;
        } while (Compra::where('codigo', $codigo)->exists());

        $validatedData['codigo'] = $codigo;

        // Insertar compra
        $compra_id = Compra::insertGetId($validatedData);

        // Procesar productos del carrito
        foreach ($contents as $content) {
            // Buscar inventario existente
            $inventario = Inventario::where('product_id', $content->id)
                ->where('branche_id', $request->sucursal_id)
                ->first();

            if ($inventario) {
                // Si existe, aumentar stock
                $inventario->stock += $content->qty;
                $inventario->save();
            } else {
                // Si no existe, crear nuevo inventario
                $inventario = Inventario::create([
                    'product_id'     => $content->id,
                    'branche_id'     => $request->sucursal_id,
                    'stock'          => $content->qty,
                    'stock_minimo'   => 3,
                    'estado'         => 1,
                    'disponibilidad' => 1,
                ]);
            }

            // Crear detalle de compra
            ComprasDetalle::create([
                'compra_id'     => $compra_id,
                'producto_id'   => $content->id,
                'cantidad'      => $content->qty,
                'inventario_id' => $inventario->id,
            ]);
        }

        // Vaciar carrito
        Cart::instance('compras')->destroy();

        return Redirect::route('compras.index')->with('success', 'Compra registrada exitosamente.');
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
