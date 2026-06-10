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
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{


    //METODO MODIFICADO PARA QUE SI NO HAY CAJA ABIERTA NO DEJE HACER VENTAS
    public function index(Request $request)
    {
        $user = auth()->user();
        $row = (int) $request->input('row', 20);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        // consulta que verifica si la caja está abierta, perteneciente al usuario y a la sucursal
         $hoy = now()->timezone('America/Mexico_City')->toDateString();

        $caja = DB::table('cajas')
                ->where('branche_id', $user->branche_id)      // misma sucursal
                ->where('user_id', $user->id)                 // mismo usuario
                ->whereDate('fecha', $hoy)                    // misma fecha
                ->where('estado', 'abierta')                  // debe estar abierta
                ->first();


        if (!$caja) {
            return redirect()->route('dashboard')->with('error', 'Debe abrir una caja antes de realizar ventas.');
        }

        // Continúa con la carga de productos
        $query = Inventario::query()
            ->join('products', 'products.id', '=', 'inventarios.product_id')
            ->join('categories as c', 'products.category_id', '=', 'c.id')
            ->join('marcas as m', 'products.marca_id', '=', 'm.id')
            ->leftjoin('equivalencias','equivalencias.id','=','products.equivalencia_id')
            ->select(
                'inventarios.id as inventario_id',
                'inventarios.stock',
                'inventarios.estado',
                'products.id as id',
                'products.product_name as product_name',
                'products.codigo_barras as codigo_barras',
                'products.product_code as product_code',
                'products.product_image as product_image',
                'products.selling_price as selling_price',
                'products.dealer_price as dealer_price',
                'c.name as category_name',
                'm.nombre as marca_nombre',
                'equivalencias.abreviatura as equivalencia'
            )

            ->where('inventarios.branche_id', $user->branche_id)
            ->Where('inventarios.stock', '>', 0);

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
            $clienteX = Customer::where('name', 'Cliente General')->first(); // o ->find(1);
            $otrosClientes = Customer::where('id', '!=', optional($clienteX)->id)->orderBy('name')->get();

            $customers = collect([$clienteX])->filter()->merge($otrosClientes);

        return view('ventas.index', [
            'customers'    => $customers,
            'productItem'  => Cart::instance('venta')->content(),
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
            'inventario_id' => 'required|exists:inventarios,id',
            'dealer_price' => 'required|numeric',
        ];

        $messages = [
            'id.required' => 'El ID del producto es obligatorio.',
            'id.numeric' => 'El ID del producto debe ser numérico.',
            'name.required' => 'El nombre del producto es obligatorio.',
            'price.required' => 'El precio del producto es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'dealer_price.required' => 'El precio para distribuidor es obligatorio.',
            'dealer_price.numeric' => 'El precio para distribuidor debe ser numérico.',
            'inventario_id.required' => 'El ID de inventario es obligatorio.',
            'inventario_id.exists' => 'El inventario seleccionado no existe.',
        ];

        $validatedData = $request->validate($rules);

        // Verifica si ya existe en el carrito
        $cartItem = Cart::instance('venta')->search(function ($cartItem, $rowId) use ($validatedData) {
            return $cartItem->id == $validatedData['id'] &&
                $cartItem->options->inventario_id == $validatedData['inventario_id'];
        })->first();

        if ($cartItem) {
            // Si ya existe, incrementa la cantidad
            Cart::instance('venta')->update($cartItem->rowId, $cartItem->qty + 1);
        } else {
            // Si no existe, agrégalo
            Cart::instance('venta')->add([
                'id' => $validatedData['id'],
                'name' => $validatedData['name'],
                'qty' => 1,
                'price' => $validatedData['price'],
                'options' => [
                'inventario_id' => $validatedData['inventario_id'],
                'dealer_price' => $validatedData['dealer_price'],
                'equivalencia' => $request->input('equivalencia'), // AGREGAR ESTA LÍNEA
                'size' => 'large',
                ]
            ]);
        }

        // Disminuir stock del inventario
        $inventario = Inventario::find($validatedData['inventario_id']);
        $inventario->stock -= 1;
        $inventario->save();

        return Redirect::back()->with('success', 'Producto agregado al carrito.');
    }
    // Actualizar la cantidad de un producto en el carrito y ajustar el stock del inventario
    public function updateCart(Request $request, $rowId)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'qty' => 'required|numeric|min:1',
            'inventario_id' => 'required|exists:inventarios,id',
        ], [
            'qty.required' => 'La cantidad es obligatoria.',
            'qty.numeric' => 'La cantidad debe ser un número.',
            'qty.min' => 'La cantidad mínima es 1.',
            'inventario_id.required' => 'El inventario es obligatorio.',
            'inventario_id.exists' => 'El inventario especificado no existe.',
        ]);

        $inventario = Inventario::find($validatedData['inventario_id']);

        // Obtener el item actual en el carrito
        $cartItem = Cart::instance('venta')->get($rowId);
        $cantidadActual = $cartItem->qty;
        $cantidadNueva = $validatedData['qty'];

        // Calcular la diferencia
        $diferencia = $cantidadNueva - $cantidadActual;
        // dd($diferencia);

        // Si la diferencia requiere más stock, verificamos disponibilidad
        if ($diferencia > $inventario->stock) {
            return Redirect::back()->with('error', 'No hay suficiente stock disponible del producto!!!.');
        }

        // Actualizar el inventario: restar si se aumenta, sumar si se reduce
        $inventario->stock -= $diferencia;
        $inventario->save();

        // Actualizar carrito
       Cart::instance('venta')->update($rowId, $cantidadNueva);

        return Redirect::back()->with('success', 'Cantidad actualizada correctamente.');
    }

    // Eliminar un producto del carrito y restaurar el stock del inventario
    public function deleteCart(String $rowId)
    {

        $item = Cart::instance('venta')->get($rowId);
        // dd($item);
        if ($item) {
            // Obtener el inventario_id desde las opciones
            $inventarioId = $item->options->inventario_id ?? null;

            if ($inventarioId) {
                $inventario = Inventario::find($inventarioId);
                if ($inventario) {
                    // Restaurar el stock según la cantidad en el carrito
                    $inventario->stock += $item->qty;
                    $inventario->save();
                }
            }

            // Eliminar el item del carrito
            Cart::instance('venta')->remove($rowId);

            return Redirect::back()->with('success', 'Producto eliminado del carrito y stock restaurado.');
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

        // Buscar al cliente
        $customer = Customer::findOrFail($validatedData['customer_id']);

        // Obtener el carrito actual
        $cartContent = Cart::instance('venta')->content();

        // Revisar si es distribuidor para usar precios especiales
        $updatedCart = $cartContent;

        if ($customer->type_customer === 'distribuidor') {
            $updatedCart = $cartContent->map(function ($item) {
                $item->price = $item->options->dealer_price;
                $item->subtotal = $item->qty * $item->price;
                return $item;
            });
        }

        // Retornar la vista con los datos
        return view('ventas.create-invoice', [
            'customer' => $customer,
            'content'  => $updatedCart
        ]);
    }


    public function printInvoice(Request $request)
    {
        $rules = [
            'customer_id' => 'required'
        ];

        $validatedData = $request->validate($rules);
        $customer = Customer::where('id', $validatedData['customer_id'])->first();
        $content = Cart::instance('venta')->content();

        return view('ventas.print-invoice', [
            'customer' => $customer,
            'content' => $content
        ]);
    }

    // Método para vaciar por completo el carrito y actualizar el stock de inventario
   public function vaciarCarrito()
    {
        $cartItems = Cart::instance('venta')->content();

        foreach ($cartItems as $item) {
            $inventarioId = $item->options->inventario_id ?? null;
            $qty = $item->qty;

            if ($inventarioId) {
                $inventario = Inventario::find($inventarioId);
                if ($inventario) {
                    $inventario->stock += $qty;
                    $inventario->save();
                }
            }
        }

        Cart::instance('venta')->destroy(); // Vacía todo el carrito

        return redirect()->back()->with('success', 'Carrito vaciado y stock actualizado.');
    }

    public function addByBarcode(Request $request)
    {
        $barcode = $request->input('barcode');
        $user = auth()->user();

        // Buscar el producto con stock disponible en la sucursal del usuario
        $producto = Inventario::join('products', 'products.id', '=', 'inventarios.product_id')
            ->where('products.codigo_barras', $barcode)
            ->where('inventarios.branche_id', $user->branche_id)
            ->select(
                'inventarios.id as inventario_id',
                'inventarios.stock as st',
                'products.id as product_id',
                'products.product_name',
                'products.selling_price',
                'products.product_image',
                'products.dealer_price',
                'equivalencias.abreviatura as equivalencia' // AGREGAR ESTA LÍNEA
            )
            ->first();

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.'
            ]);
        }

        if ($producto->st <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Sin stock disponible.'
            ]);
        }

        // Buscar si ya está en el carrito (por producto_id + inventario_id)
        $cartItem = Cart::instance('venta')->search(function ($item, $rowId) use ($producto) {
            return $item->id == $producto->product_id &&
                $item->options->inventario_id == $producto->inventario_id;
        })->first();

        if ($cartItem) {
            // Incrementar la cantidad en el carrito si hay stock suficiente
            if ($producto->st >= $cartItem->qty + 1) {
                Cart::instance('venta')->update($cartItem->rowId, $cartItem->qty + 1);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente stock para aumentar la cantidad.'
                ]);
            }
        } else {
            // No está, agregar nuevo
            Cart::instance('venta')->add([
                'id'      => $producto->product_id,
                'name'    => $producto->product_name,
                'qty'     => 1,
                'price'   => $producto->selling_price,
                'weight'  => 1,
                'options' => [
                    'image' => $producto->product_image,
                    'inventario_id' => $producto->inventario_id,
                    'dealer_price'=> $producto->dealer_price,
                    'equivalencia' => $producto->equivalencia // AGREGAR ESTA LÍNEA
                ]
            ]);
        }

        // Disminuir stock del inventario
        $inventario = Inventario::find($producto->inventario_id);
        $inventario->stock -= 1;
        $inventario->save();

        return response()->json([
            'success' => true,
            'product_name' => $producto->product_name
        ]);
    }


    public function regresar_ventas(){
        $cartItems =  Cart::instance('venta')->content();

        foreach ($cartItems as $item) {
            $inventarioId = $item->options->inventario_id ?? null;
            $qty = $item->qty;

            if ($inventarioId) {
                $inventario = Inventario::find($inventarioId);
                if ($inventario) {
                    $inventario->stock += $qty;
                    $inventario->save();
                }
            }
        }

        Cart::instance('venta')->destroy(); // Vacía todo el carrito

        return Redirect::route('ventas.index')->with('success', 'Compra cancelada...');
    }

    public function cancelarVenta($id)
    {
        $empleado = auth()->user();
        $hoy = now()->timezone('America/Mexico_City')->toDateString();
        // Verifica si el usuario tiene una caja abierta
        $caja = Caja::where('user_id', $empleado->id)
            ->where('estado', 'abierta')
            ->whereDate('fecha', $hoy)
            ->first();

        // Si no hay caja abierta, redirige con un mensaje de error
        if (!$caja) {
            return redirect()->back()->with('error', 'Debe tener una caja abierta, antes de cancelar una venta.');
        }
        // Verifica si el pedido existe
        $order = Order::findOrFail($id);

       if ($order->total > $caja->monto_final) {
            return redirect()->back()->with('error', 'No es posible cancelar esta venta porque su monto total excede el dinero disponible en caja.');
        }


        // Verifica si el pedido ya está pagado
        if ($order->order_status === 'completada') {
            return redirect()->back()->with('error', 'No se puede cancelar un pedido ya completado.');
        }

        $productos = Product::query()
                    ->join('order_details', 'products.id', '=', 'order_details.product_id')
                    ->where('order_details.order_id', $order->id)
                    ->select('products.*', 'order_details.quantity')
                    ->get();

         $descripcion = 'Devolución en efectivo de la venta numero ' . $order->invoice_no . ', la cual fue cancelada por el empleado ' . $empleado->name .'.';

        Transaction::create([
                    'tipo_transaccion' => 'Venta cancelada',
                    'metodo_pago'      => 'Efectivo',
                    'fecha'            => $hoy,
                    'hora'             => Carbon::now()->timezone('America/Mexico_City')->format('Y-m-d H:i:s'),
                    'monto'            => $order->total,
                    'total'            => -$order->total,
                    'descripcion'      => $descripcion,
                    'caja_id'          => $caja->id,
                    ]);


        // Cambia el estado del pedido a cancelada
        $order->order_status = 'cancelada';
        $order->save();

        $caja->monto_final -= $order->total; // Resta el total de la venta cancelada
        $caja->save();


        return redirect()->route('order.pendingOrders')->with('success', 'Pedido cancelado exitosamente.');
    }

}
