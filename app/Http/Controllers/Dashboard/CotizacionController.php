<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Marca;
use App\Models\Inventario;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Caja;
use Illuminate\Support\Facades\DB;

class CotizacionController extends Controller
{
    //METODO MODIFICADO PARA QUE SI NO HAY CAJA ABIERTA NO DEJE HACER VENTAS
    public function index(Request $request)
    {
        $row = (int) $request->input('row', 30);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        $query = Product::query()
            // ->join('products', 'products.id', '=', 'inventarios.product_id')
            ->join('categories as c', 'products.category_id', '=', 'c.id')
            ->join('marcas as m', 'products.marca_id', '=', 'm.id')
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
                'c.name as category_name',
                'm.nombre as marca_nombre',
                'equivalencias.abreviatura as equivalencia'
            );

        // Filtro de búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'like', "%{$search}%")
                ->orWhere('products.codigo_barras', 'like', "%{$search}%")
                ->orWhere('products.product_code', 'like', "%{$search}%")
                ->orWhere('c.name', 'like', "%{$search}%")
                ->orWhere('m.nombre', 'like', "%{$search}%");
            });
        }
        // Filtros...
        $products = $query->sortable([
            'product_name',
                    'category_name',
                    'marca_nombre',
                    'codigo_barras',
                    'selling_price,'
        ])
            ->paginate($row)
            ->appends($request->query());

            // filtra los clientes pero pone al inicio al client por deafult
            $clienteX = Customer::where('name', 'Cliente X')->first(); // o ->find(1);
            $otrosClientes = Customer::where('id', '!=', optional($clienteX)->id)->orderBy('name')->get();

            $customers = collect([$clienteX])->filter()->merge($otrosClientes);

        return view('cotizaciones.index', [
            'customers'    => $customers,
            'productItem'  => Cart::instance('cotizacion')->content(),
            'products'     => $products,
            'categories'   => Category::all(),
            'marcas'       => Marca::all(),
        ]);
    }

    public function addCart(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'dealer_price' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        // Verifica si ya existe en el carrito
        $cartItem = Cart::instance('cotizacion')->search(function ($cartItem, $rowId) use ($validatedData) {
            return $cartItem->id == $validatedData['id'];
        })->first();

        if ($cartItem) {
            Cart::instance('cotizacion')->update($cartItem->rowId, $cartItem->qty + 1);
        } else {
            // Si no existe, agrégalo
            Cart::instance('cotizacion')->add([
                'id' => $validatedData['id'],
                'name' => $validatedData['name'],
                'qty' => 1,
                'price' => $validatedData['price'],
                'options' => [
                    // 'inventario_id' => $validatedData['inventario_id'],
                    'dealer_price' => $validatedData['dealer_price'],
                    'product_code' => $request->product_code, // ✅ Aquí se guarda
                    'unidad' => $request->equivalencia,
                    'equivalencia' => $request->input('equivalencia'), // AGREGAR ESTA LÍNEA
                    'size' => 'large',
                ]
            ]);
        }

        return Redirect::back()->with('success', 'Producto agregado al carrito.');
    }
    // Actualizar la cantidad de un producto en el carrito y ajustar el stock del inventario
    public function updateCart(Request $request, $rowId)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'qty' => 'required|numeric|min:1',
        ]);

        $cartItem = Cart::instance('cotizacion')->get($rowId);

        // dd($cartItem);

        $cantidadActual = $cartItem->qty;
        $cantidadNueva = $validatedData['qty'];

        // Calcular la diferencia
        $diferencia = $cantidadNueva - $cantidadActual;
        // dd($diferencia);


        // Actualizar carrito
       Cart::instance('cotizacion')->update($rowId, $cantidadNueva);

        return Redirect::back()->with('success', 'Cantidad actualizada correctamente.');
    }

    // Eliminar un producto del carrito y restaurar el stock del inventario
    public function deleteCart(String $rowId)
    {
        // dd($rowId);

        $item = Cart::instance('cotizacion')->get($rowId);
        // dd($item);
        if ($item) {

            // Eliminar el item del carrito
            Cart::instance('cotizacion')->remove($rowId);

            return Redirect::back()->with('success', 'Producto eliminado del carrito.');
        }

        return Redirect::back()->with('error', 'Producto no encontrado en el carrito.');
    }


    public function createInvoice(Request $request)
    {
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            // Puedes agregar más validaciones aquí si agregas nuevos campos
        ];

        $messages = [
            'customer_id.required' => '⚠️ Debes seleccionar un cliente antes de crear la venta.',
            'customer_id.exists'   => 'El cliente seleccionado no existe en el sistema.',
        ];

        $validatedData = $request->validate($rules, $messages);

            // Busca el cliente en la BD
            $customer = Customer::findOrFail($validatedData['customer_id']);

            // Obtiene el contenido actual del carrito
            $cartContent = Cart::instance('cotizacion')->content();

            $updatedCart = $cartContent;
            // Si el cliente es distribuidor, reemplaza el precio con dealer_price
            if ($customer->type_customer == 'distribuidor') {
                $updatedCart = $cartContent->map(function ($item) {
                    $item->price = $item->options->dealer_price; // usa dealer_price como precio
                    $item->subtotal = $item->qty * $item->price; // actualiza el subtotal
                    return $item;
                });
            }
            // dd($updatedCart);
            return view('cotizaciones.create-invoice', [
                'customer' => $customer,
                'content' => $updatedCart
            ]);
    }


    public function printInvoice(Request $request)
    {
        $rules = [
            'customer_id' => 'required'
        ];

        $validatedData = $request->validate($rules);
        $customer = Customer::where('id', $validatedData['customer_id'])->first();
        $content = Cart::instance('cotizacion')->content();

        return view('cotizaciones.print-invoice', [
            'customer' => $customer,
            'content' => $content
        ]);
    }

    // Método para vaciar por completo el carrito y actualizar el stock de inventario
   public function vaciarCarrito()
    {
        Cart::instance('cotizacion')->destroy(); // Vacía todo el carrito

        return redirect()->back()->with('success', 'Carrito vaciado correctamente.');
    }

    public function addByBarcode(Request $request)
    {
        $barcode = $request->input('barcode');

        $producto = Product::query()
            ->leftjoin('equivalencias','equivalencias.id','=','products.equivalencia_id')
            ->where('products.codigo_barras', $barcode)
            // ->where('inventarios.branche_id', $user->branche_id)
            ->select(
                // 'inventarios.id as inventario_id',
                // 'inventarios.stock as st',
                'products.id as product_id',
                'products.product_name',
                'products.selling_price',
                'products.product_image',
                'products.dealer_price',
                'equivalencias.nombre as equivalencia',
            )
            ->first();

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.'
            ]);
        }

        // Buscar si ya está en el carrito (por producto_id + inventario_id)
        $cartItem = Cart::instance('cotizacion')->search(function ($item, $rowId) use ($producto) {
            return $item->id == $producto->product_id;
        })->first();

        if ($cartItem) {
            Cart::instance('cotizacion')->update($cartItem->rowId, $cartItem->qty + 1);
        } else {
            // No está, agregar nuevo
            Cart::instance('cotizacion')->add([
                'id'      => $producto->product_id,
                'name'    => $producto->product_name,
                'qty'     => 1,
                'price'   => $producto->selling_price,
                'weight'  => 1,
                'options' => [
                    'image' => $producto->product_image,
                    // 'inventario_id' => $producto->inventario_id,
                    'unidad' => $producto->equivalencia,
                    'dealer_price'=> $producto->dealer_price,
                    'equivalencia' => $producto->equivalencia // AGREGAR ESTA LÍNEA
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'product_name' => $producto->product_name
        ]);
    }


    public function regresar_ventas(){

        Cart::instance('cotizacion')->destroy(); // Vacía todo el carrito

        return Redirect::route('cotizaciones.index')->with('success', 'Cotización cancelada...');
    }

}
