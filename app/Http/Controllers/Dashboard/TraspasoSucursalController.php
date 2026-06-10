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
use App\Models\User;
use App\Models\Branche;
use App\Models\Traspaso;
use App\Models\Traspasosdetalle;
use Illuminate\Support\Facades\DB;
use Haruncpi\LaravelIdGenerator\IdGenerator;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TraspasoSucursalController extends Controller
{


    public function index(Request $request)
    {
        $empleado = auth()->user();
        $row = (int) $request->input('row', 20);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        $sucursalId = $request->input('branche_id');
        $categoriaId = $request->input('category_id');
        $busqueda = $request->input('search');

        $query = Inventario::query()
            ->join('products', 'products.id', '=', 'inventarios.product_id')
            ->join('categories as c', 'products.category_id', '=', 'c.id')
            ->join('marcas as m', 'products.marca_id', '=', 'm.id')
            ->join('branches as b', 'b.id', '=', 'inventarios.branche_id')
            ->leftjoin('equivalencias','equivalencias.id','=','products.equivalencia_id')
            ->select(
                'inventarios.id as inventario_id',
                'inventarios.stock',
                'inventarios.estado',
                'products.id as id',
                'products.product_name as product_name',
                'products.codigo_barras as codigo_barras',
                'products.product_code as product_code',
                'products.selling_price as selling_price',
                'products.dealer_price as dealer_price',
                'c.name as category_name',
                'm.nombre as marca_nombre',
                'b.nombre as sucursal_nombre',
                'equivalencias.nombre as equivalencia'
            )
            ->where('inventarios.stock', '>', 0)
            ->where('inventarios.branche_id','!=', $empleado->branche_id);

        // Aplicar filtros si existen
        if ($sucursalId) {
            $query->where('inventarios.branche_id', $sucursalId);
        }

        if ($categoriaId) {
            $query->where('products.category_id', $categoriaId);
        }

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('products.product_name', 'LIKE', "%$busqueda%")
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
            'selling_price',
        ])
            ->paginate($row)
            ->appends($request->query());

        $sucursales = Branche::where('id', '!=', $empleado->branche_id)->get();

        return view('traspasos.index', [
            'productItem'  => Cart::instance('traspaso')->content(),
            'products'     => $products,
            'categories'   => Category::all(),
            'sucursales' => $sucursales,
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

        $validatedData = $request->validate($rules);
        // dd(Cart::instance('traspaso')->content());

        $cartItem = Cart::instance('traspaso')->search(function ($cartItem, $rowId) use ($validatedData) {
                return $cartItem->id == $validatedData['id'] &&
                    $cartItem->options->inventario_id == $validatedData['inventario_id'];
            })->first();

        if ($cartItem) {
            // Si ya existe, incrementa la cantidad
            $newQty = $cartItem->qty + 1;
            if($newQty > Inventario::find($validatedData['inventario_id'])->stock) {
                return Redirect::back()->with('error', 'No hay suficiente stock disponible de este producto en la sucursal.');
            }

            Cart::instance('traspaso')->update($cartItem->rowId, $cartItem->qty + 1);
        } else {

            $producto = Inventario::join('products', 'products.id', '=', 'inventarios.product_id')
                         ->leftjoin('equivalencias','equivalencias.id','=','products.equivalencia_id')
                        ->where('inventarios.id', $request->inventario_id)
                        ->select(
                            'products.codigo_barras',
                            'products.product_code',
                            'equivalencias.nombre as unidad',
                        )->first();

            Cart::instance('traspaso')->add([
                'id' => $validatedData['id'],
                'name' => $validatedData['name'],
                'qty' => 1,
                'price' => $validatedData['price'],
                'options' => [
                    'inventario_id' => $validatedData['inventario_id'],
                    'dealer_price' => $validatedData['dealer_price'],
                    'size' => 'large',
                    'codigo_barras' => $producto->codigo_barras,
                    'codigo_producto' => $producto->product_code,
                    'unidad' => $producto->unidad,
                ]
            ]);
        }

        return Redirect::back()->with('success', 'Producto agregado al carrito!');
    }

    public function createInvoice(Request $request)
    {
            $empleado = auth()->user();
            $sucursal_emisora = Branche::find($empleado->branche_id);

            // Obtiene el contenido actual del carrito
            $cartContent = Cart::instance('traspaso')->content();
            // dd($cartContent);
            $primerItem = $cartContent->first();
            // dd($primerItem->options->inventario_id);
            $producto = Inventario::find($primerItem->options->inventario_id);
            $sucursal_destino = Branche::find($producto->branche_id);

            return view('traspasos.create-traspaso', [
                'empleado' => $empleado,
                'content' => $cartContent,
                'sucursal_emisora' => $sucursal_emisora,
                'sucursal_destino' => $sucursal_destino,
            ]);
    }


    public function updateCart(Request $request, $rowId)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'qty' => 'required|numeric|min:1',
            'inventario_id' => 'required|exists:inventarios,id',
        ]);

        $inventario = Inventario::find($validatedData['inventario_id']);

        // Obtener el item actual en el carrito
        $cartItem = Cart::instance('traspaso')->get($rowId);


        $cantidadActual = $cartItem->qty;
        $cantidadNueva = $validatedData['qty'];

        // Calcular la diferencia
        $diferencia = $cantidadNueva - $cantidadActual;

        // Si la diferencia requiere más stock, verificamos disponibilidad
        if ($cantidadNueva > $inventario->stock) {
            return Redirect::back()->with('error', 'No hay suficiente stock disponible.');
        }

        // Actualizar carrito
        Cart::instance('traspaso')->update($rowId, $cantidadNueva);

        return Redirect::back()->with('success', 'Cantidad actualizada correctamente.');
    }

    public function deleteCart(String $rowId)
    {

        $item = Cart::instance('traspaso')->get($rowId);
        // dd($item);
        if ($item) {
            // Obtener el inventario_id desde las opciones
            $inventarioId = $item->options->inventario_id ?? null;
            // Eliminar el item del carrito
            Cart::instance('traspaso')->remove($rowId);

            return Redirect::back()->with('success', 'Producto eliminado del carrito y stock restaurado.');
        }

        return Redirect::back()->with('error', 'Producto no encontrado en el carrito.');
    }
    /**
     * Show the form for creating a new resource.
     */

     public function vaciarCarrito()
    {
        $cartItems = Cart::instance('traspaso')->content();

        foreach ($cartItems as $item) {
            $inventarioId = $item->options->inventario_id ?? null;
            $qty = $item->qty;
        }

        Cart::instance('traspaso')->destroy(); // Vacía todo el carrito

        return redirect()->back()->with('success', 'Carrito vaciado y stock actualizado.');
    }


    public function addByBarcode(Request $request)
    {
        $empleado = auth()->user();
        $barcode = $request->input('barcode');

        // Buscar el producto con stock disponible en la sucursal del usuario
        $producto = Inventario::join('products', 'products.id', '=', 'inventarios.product_id')
            ->leftjoin('equivalencias','equivalencias.id','=','products.equivalencia_id')
            ->where('products.codigo_barras', $barcode)
            ->where('inventarios.branche_id','!=', $empleado->branche_id)
            ->select(
                'inventarios.id as inventario_id',
                'inventarios.stock as st',
                'products.id as product_id',
                'products.product_name',
                'products.selling_price',
                'products.product_image',
                'products.dealer_price',
                'products.codigo_barras',
                'products.product_code',
                'equivalencias.nombre as unidad'
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
        $cartItem = Cart::instance('traspaso')->search(function ($item, $rowId) use ($producto) {
            return $item->id == $producto->product_id &&
                $item->options->inventario_id == $producto->inventario_id;
        })->first();

        if ($cartItem) {
            // Incrementar la cantidad en el carrito si hay stock suficiente
            if ($producto->st >= $cartItem->qty + 1) {
                Cart::instance('traspaso')->update($cartItem->rowId, $cartItem->qty + 1);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente stock para aumentar la cantidad.'
                ]);
            }
        } else {
            // No está, agregar nuevo
           Cart::instance('traspaso')->add([
                'id'      => $producto->product_id,
                'name'    => $producto->product_name,
                'qty'     => 1,
                'price'   => $producto->selling_price,
                'weight'  => 1,
                'options' => [
                    'image' => $producto->product_image,
                    'inventario_id' => $producto->inventario_id,
                    'dealer_price'=> $producto->dealer_price,
                    'codigo_barras' => $producto->codigo_barras,
                    'codigo_producto' => $producto->product_code,
                    'unidad' => $producto->unidad,
                    ]
            ]);
        }
        // Agregar al carrito
        // Cart::instance('traspaso')->add([
        //     'id'      => $producto->product_id,
        //     'name'    => $producto->product_name,
        //     'qty'     => 1,
        //     'price'   => $producto->selling_price,
        //     'weight'  => 1,
        //     'options' => [
        //         'image' => $producto->product_image,
        //         'inventario_id' => $producto->inventario_id,
        //         'dealer_price'=> $producto->dealer_price,
        //         'codigo_barras' => $producto->codigo_barras,
        //     ]
        // ]);

        return response()->json([
            'success' => true,
            'product_name' => $producto->product_name
        ]);
    }

    public function regresar_ventas()
    {
        $cartItems = Cart::instance('traspaso')->content();

        foreach ($cartItems as $item) {
            $inventarioId = $item->options->inventario_id ?? null;
            $qty = $item->qty;
        }

        Cart::instance('traspaso')->destroy(); // Vacía todo el carrito

        return redirect()
            ->route('traspasos.index')
            ->with('success', 'El traspaso ha sido cancelado y el stock fue restaurado correctamente.');
    }

    public function storeOrder(Request $request)
    {

        $empleado = auth()->user();

        $rules = [
            'observaciones' => 'nullable|string',
        ];

        $codigo = IdGenerator::generate([
            'table' => 'traspasos',
            'field' => 'codigo',
            'length' => 10,
            'prefix' => 'TRAS-'.$empleado->branche_id.'-',
        ]);


        $validatedData = $request->validate($rules);
        $validatedData['codigo'] = $codigo;
        $validatedData['fecha'] = Carbon::now()->timezone('America/Mexico_City')->format('Y-m-d');
        $validatedData['hora'] = Carbon::now()->timezone('America/Mexico_City')->format('H:i:s');
        $validatedData['estado'] = 'solicitado';
        $validatedData['responsable'] = $empleado->id;
        $validatedData['sucursal_origen'] = $empleado->branche_id;
        $validatedData['sucursal_destino'] = $request->sucursal_destino_id;

        do {
            $secuencia = str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT); // Ej: '001', '045', etc.
            $codigo = 'TRAS-' . $empleado->branche_id . '-' . $secuencia;
        } while (Traspaso::where('codigo', $codigo)->exists());

        $validatedData['codigo'] = $codigo;
        $traspaso_id = Traspaso::insertGetId($validatedData);


        // Create Order Details
        $contents = Cart::instance('traspaso')->content();
        $oDetails = array();

        foreach ($contents as $content) {
            // Crear detalle del traspaso
            TraspasosDetalle::create([
                'traspaso_id'   => $traspaso_id,
                'producto_id'   => $content->id,
                'cantidad'      => $content->qty,
                'inventario_id' => $content->options->inventario_id,
            ]);
        }

        // Delete Cart Sopping History
        Cart::instance('traspaso')->destroy();

        //Acción para el envio de la notificacion por whatsApp
        $sucursal_destino= Branche::find($empleado->branche_id);
        try {
            $sid    = env('TWILIO_SID');
            $token  = env('TWILIO_AUTH_TOKEN');
            $from   = env('TWILIO_WHATSAPP_FROM');

            $client = new Client($sid, $token);

            $sucursalDestino = Branche::find($request->sucursal_destino_id);

            $mensaje = "📦 *Nuevo traspaso solicitado*\n"
                    . "🧾 Código de traspaso: {$codigo}\n"
                    . "🏢 Sucursal Origen: {$sucursal_destino->nombre}\n"
                    . "📍 Sucursal Destino: " . ($sucursalDestino ? $sucursalDestino->nombre : 'Desconocida') . "\n"
                    . "👤 Responsable: {$empleado->name}\n"
                    . "🕒 Fecha: " . now('America/Mexico_City')->format('d/m/Y H:i'). "\n"
                    . "Mensaje Generado Atuomaticamente, favor de no responder...";

            $usuarios = User::whereNotNull('cellphone')
                        ->where('branche_id',$sucursal_destino->id)
                        ->where('estado',1)
                        ->get();

            foreach ($usuarios as $usuario) {
                $numero = 'whatsapp:' . $usuario->cellphone;

                $client->messages->create($numero, [
                    'from' => $from,
                    'body' => $mensaje,
                ]);

                Log::info("Mensaje enviado correctamente a: {$numero}");
            }
        } catch (\Exception $e) {
            Log::error('❌ Error al enviar WhatsApp: ' . $e->getMessage());
        }

        // return Redirect::route('dashboard')->with('success', 'Traspaso solicitado exitosamente!');
        // return $this->imprimir($traspaso_id);
         $urlImprimir = route('traspasos.imprimir_traspaso', ['id' => $traspaso_id]);

        // Pasamos la URL a la sesión
        return redirect()->route('listTraspasos.index')->with([
            'success' => '¡El traspaso se ha solicitado exitosamente a la sucursal!',
            'imprimir_url' => $urlImprimir
        ]);
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function enviarWhatsAppTest()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_WHATSAPP_FROM');
        $to = env('WHATSAPP_DESTINO');

        $client = new Client($sid, $token);
        $client->messages->create($to, [
            'from' => $from,
            'body' => '🚀 Hola Saul Esto es una prueba de envío desde Laravel vía WhatsApp con Twilio Sandbox.',
        ]);

        return '✅ Mensaje de prueba enviado por WhatsApp';
    }
}
