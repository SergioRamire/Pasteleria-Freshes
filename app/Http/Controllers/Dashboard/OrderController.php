<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Caja;
use App\Models\Branche;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\Inventario;
use App\Models\Payment;
use Illuminate\Support\Str;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pendingOrders()
    {
        $empleado = auth()->user();
        $row = (int) request('row', 20);
        $search = request('search');
        $order_date = request('order_date');
        $envio = request('enviar_id');

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro de filas debe ser entre 1 y 100.');
        }

        $hoy = now()->timezone('America/Mexico_City')->toDateString();
        $hasSortable = request()->has('sort');

        $orders = Order::where('payment_status', 'pagado')
                ->where('order_status', 'pendiente')
                ->where('branche_id',$empleado->branche_id)
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('invoice_no', 'like', "%{$search}%")
                        ->orWhere('total', 'like', "%{$search}%");
                    });
                })
                ->when($order_date, function ($query) use ($order_date) {
                    $query->whereDate('order_date', $order_date);
                })
                ->when($envio !== null && $envio !== '', function ($query) use ($envio) {
                    $query->where('enviar', (bool) $envio);
                });

        // Solo aplicar orden manual si el usuario no está usando sortable
        if (!$hasSortable) {
            $orders = $orders->orderByRaw("order_date = ? DESC", [$hoy])
                            ->orderBy('order_date', 'desc');
        }

        $orders = $orders->with('customer')
                        ->sortable([
                            'order_date',
                            'invoice_no',
                            'total'
                        ])
                        ->paginate($row)
                        ->appends(request()->query());


        return view('orders.pending-orders', compact('orders'));
    }


    public function completeOrders()
    {
        $empleado = auth()->user();
        $row = (int) request('row', 20);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        $hoy = now()->toDateString();
        $hasSortable = request()->has('sort');

        $query = Order::where('order_status', 'completada')
                    ->where('branche_id',$empleado->branche_id);

        // Filtro por búsqueda de texto
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                ->orWhere('sub_total', 'like', "%{$search}%");
            });
        }

        // Filtro por fecha exacta
        if ($date = request('order_date')) {
            $query->whereDate('order_date', $date);
        }
        $enviar = request('enviar_id');
        if ($enviar !== null && $enviar !== '') {
            $query->where('enviar', $enviar);
        }


        // Ordena primero los pedidos del día de hoy si no se está usando sort manual
        if (!$hasSortable) {
            $query->orderByRaw("order_date = ? DESC", [$hoy])
                ->orderBy('order_date', 'desc');
        }

        $orders = $query->sortable([
                        'order_date',
                        'invoice_no',
                        'pay',
                    ])
                    ->paginate($row)
                    ->appends(request()->all());

        return view('orders.complete-orders', compact('orders'));
    }

    public function stockManage()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        return view('stock.index', [
            'products' => Product::with(['category', 'supplier'])
                ->filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    public function storeOrder(Request $request)
    {
        // dd($request->all());
        $rules = [
            'customer_id' => 'required|numeric',
            'payment_option' => 'required|in:pending,cash,card,mixed',
            'pay_cash' => 'nullable|numeric|min:0',
            'pay_card' => 'nullable|numeric|min:0',
        ];
          $validated = $request->validate($rules);
        // Validar si el checkbox de envío está marcado
        $envio = $request->input('envio', 0); // 1 si está marcado, 0 si no

        $paymentOption = $validated['payment_option'];
        $num_ticket = $request->input('num_ticket', null);
        $num_tarjeta = $request->input('last_four_digits', null);

        // Validar que el número de ticket no exista ya registrado (solo si se envió)
        if ($num_ticket && Order::where('num_ticket', $num_ticket)->exists()) {
            return redirect()->route('ventas.index')
                ->withInput()
                ->with('error', 'Este número de ticket ya fue registrado en otra venta. Intentalo otra vez.');
        }


        $hoy = now()->timezone('America/Mexico_City')->toDateString();
        $empleado = auth()->user();


        $validated = $request->validate($rules);
        // $carito = Cart::instance('venta')->content();
        $subTotal = floatval(str_replace(',', '', Cart::instance('venta')->subtotal()));
        $payCash = floatval($validated['pay_cash'] ?? 0);
        $payCard = floatval($validated['pay_card'] ?? 0);
        $paymentOption = $validated['payment_option'];

        if ($paymentOption === 'pending') {
            $payCash = 0;
            $payCard = 0;
        }

        $totalPaid = $payCash + $payCard;
        $due = $subTotal - $totalPaid;
        $status = $totalPaid >= $subTotal ? 'pagado' : 'pendiente';

        // $invoice_no = IdGenerator::generate([
        //     'table' => 'orders',
        //     'field' => 'invoice_no',
        //     'length' => 10,
        //     'prefix' => 'INV-'
        // ]);

        // Generar código único
        do {
            $invoice_no = 'INV-' . strtoupper(Str::random(10));
        } while (Order::where('invoice_no', $invoice_no)->exists());

        // Define método de pago para el campo (puedes ajustarlo según lógica)
        if ($request->pay_cash > 0 && $request->pay_card > 0) {
            $metodoPago = 'Efectivo/Tarjeta';
        } elseif ($request->pay_cash > 0) {
            $metodoPago = 'Efectivo';
        } elseif ($request->pay_card > 0) {
            $metodoPago = 'Tarjeta';
        } else {
            $metodoPago = null;
        }


        $order_id = Order::insertGetId([
            'customer_id' => $validated['customer_id'],
            'payment_status' => $status,
            'order_date' => $hoy,
            'order_status' => 'pendiente',
            'total_products' => Cart::instance('venta')->count(),
            'sub_total' => $subTotal,
            'vat' => Cart::instance('venta')->tax(),
            'total' => $subTotal,
            'pay' => $payCash,
            'due' => $due,
            'enviar' => $envio,
            'num_ticket' => $num_ticket, // Guardar número de ticket si se proporciona
            'num_tarjeta' => $num_tarjeta, // Guardar últimos 4 dígitos de la tarjeta si se proporciona
            'metodo_pago'=> $metodoPago,
            'invoice_no' => $invoice_no,
            'user_id' => $empleado->id,
            'branche_id' =>$empleado->branche_id,
            'created_at' => Carbon::now()->timezone('America/Mexico_City')->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->timezone('America/Mexico_City')->format('Y-m-d H:i:s')
        ]);


        foreach (Cart::instance('venta')->content() as $item) {
            OrderDetails::create([
                'order_id' => $order_id,
                'product_id' => $item->id,
                'inventario_id'=>$item->options->inventario_id,
                'quantity' => $item->qty,
                'unitcost' => $item->price,
                'total' => $item->qty * $item->price,
            ]);
        }

        $caja = Caja::where('branche_id', $empleado->branche_id)
                ->where('user_id', $empleado->id)
                ->whereDate('fecha', $hoy)
                ->where('estado', 'abierta')
                ->firstOrFail();


        if( $request->pay_card > 0 && $request->pay_cash > 0) {
             $caja->monto_final += $payCash;

        }else if ($request->pay_cash > 0 && $request->pay_card <= 0) {
             $caja->monto_final += $payCash + $due;
        }
        $caja->save();

        if ($request->payment_option !== 'pending') {
            $descripcion = 'Pago de venta numero ' . $invoice_no . '. ';
            if ($request->pay_cash > 0) {
                $descripcion .= 'Efectivo: $' . number_format($request->pay_cash, 2) . '. '.' Cambio: $' . number_format($due, 2) . '. ';
            }
            if ($request->pay_card > 0) {
                $descripcion .= 'Tarjeta: $' . number_format($request->pay_card, 2) . '. '. ' Número de ticket (Terminal): ' . $num_ticket . '. ';
            }

                        // Registrar transacción
            Transaction::create([
                'tipo_transaccion' => 'venta',
                'metodo_pago'      => $metodoPago,
                'fecha'            => $hoy,
                'hora'             => Carbon::now()->timezone('America/Mexico_City')->format('Y-m-d H:i:s'),
                'monto'            => $totalPaid,
                'total'            => $subTotal,
                'descripcion'      => $descripcion,
                'caja_id'          => $caja->id,
                ]);
        }
       Cart::instance('venta')->destroy();

       return $request->payment_option === 'pending'
            ? redirect()->route('ventas.index')->with('success', 'Orden registrada exitosamente.')
            : redirect()->route('order.ticket', ['id' => $order_id]);
    //     return $request->payment_option === 'pending'
    // ? redirect()->route('ventas.index')->with('success', 'Orden registrada exitosamente.')
    // : redirect()->route('ventas.print', ['id' => $order_id]);

    }


    /**
     * Métodos para ver los detalles de las ventas
     */
    private function obtenerDetallesOrden(Int $order_id)
    {
        return OrderDetails::query()
            ->join('products', 'products.id', '=', 'order_details.product_id')
            // ->leftJoin('satclaves', 'satclaves.id', '=', 'products.satclave_id')
            ->leftJoin('equivalencias', 'equivalencias.id', '=', 'products.equivalencia_id')
            ->select(
                'order_details.*',
                'products.product_name as product_name',
                'products.product_image as product_image',
                'products.selling_price as selling_price',
                'products.codigo_barras as codigo_barras',
                // 'satclaves.c_ClaveProdServ as clave',
                'equivalencias.nombre as equivalencia'
            )
            ->where('order_id', $order_id)
            ->orderBy('id', 'DESC')
            ->get();
    }
    public function orderDetails(Int $order_id)
    {
        $order = Order::find($order_id);
        $orderDetails = $this->obtenerDetallesOrden($order_id);

        return view('orders.details-order', compact('order', 'orderDetails'));
    }

    public function orderDetailsComplete(Int $order_id)
    {
        $order = Order::find($order_id);
        $orderDetails = $this->obtenerDetallesOrden($order_id);

        return view('orders.detailsCompletes', compact('order', 'orderDetails'));
    }

    public function orderDetailsDue(Int $order_id)
    {
        $order = Order::find($order_id);
        $orderDetails = $this->obtenerDetallesOrden($order_id);
        $abonos = Payment::where('order_id',$order->id)
            ->latest()
            ->get();
        // dd($order, $orderDetails, $abonos);

        return view('orders.detailsDue', compact('order', 'orderDetails', 'abonos'));
    }

    public function orderDetailsCancel(Int $order_id)
    {
        $order = Order::find($order_id);
        $orderDetails = $this->obtenerDetallesOrden($order_id);

        return view('orders.detailsCancel', compact('order', 'orderDetails'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request)
    {
        $order_id = $request->id;

        // Reduce the stock
        $products = OrderDetails::where('order_id', $order_id)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                    ->update(['product_store' => DB::raw('product_store-'.$product->quantity)]);
        }

        Order::findOrFail($order_id)->update(['order_status' => 'completada']);

        return Redirect::route('order.pendingOrders')->with('success', 'Order despachada exitosamente!');
    }

    public function invoiceDownload(Int $order_id)
    {
        $order = Order::where('id', $order_id)->first();
        $orderDetails = OrderDetails::with('product')
                        ->where('order_id', $order_id)
                        ->orderBy('id', 'DESC')
                        ->get();

        $sucursal = Branche::findOrFail($order->branche_id);

        // show data (only for debugging)
        return view('orders.invoice-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
            'sucursal' => $sucursal
        ]);
    }

    public function pendingDue()
    {
        $empleado = auth()->user();
        $row = (int) request('row', 20);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro filas por página debe ser un entero entre 1 y 100.');
        }

        $search = request('search');
        $orderDate = request('order_date'); // filtro por fecha (YYYY-MM-DD)

        $query = Order::where('due', '>', 0)
                    ->where('order_status', 'pendiente')
                    ->where('branche_id',$empleado->branche_id);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                ->orWhereHas('customer', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($orderDate) {
            // filtrar por orden con fecha igual a la seleccionada
            $query->whereDate('order_date', $orderDate);
        }

        $orders = $query->sortable()->paginate($row)->withQueryString();

        return view('orders.pending-due', compact('orders'));
    }



    public function orderDueAjax(Int $id)
    {
        $order = Order::findOrFail($id);

        return response()->json($order);
    }

    public function updateDue(Request $request)
    {
        // dd($request->all());
        $rules = [
            'order_id' => 'required|exists:orders,id',
            'pago_efectivo' => 'nullable|numeric|min:0',
            'pago_tarjeta' => 'nullable|numeric|min:0',
        ];

        $validatedData = $request->validate($rules);

        $order = Order::findOrFail($request->order_id);

        $pago_efectivo = $validatedData['pago_efectivo'] ?? 0;
        $pago_tarjeta = $validatedData['pago_tarjeta'] ?? 0;
        $total_pagado = $pago_efectivo + $pago_tarjeta;

        $deuda_actual = $order->due;

        // Validar que el pago sea suficiente
        if ($total_pagado < $deuda_actual) {
            return back()->withErrors(['pago_efectivo' => 'El monto total pagado no cubre la deuda pendiente.']);
        }

        // Calcular nuevo estado de la orden
        $nuevo_saldo = $order->pay + $total_pagado;
        $nuevo_due = $deuda_actual - $total_pagado;

        if ($request->pago_efectivo > 0 && $request->pago_tarjeta > 0) {
                    $metodoPago = 'Efectivo/Tarjeta';
                } elseif ($request->pago_efectivo > 0) {
                    $metodoPago = 'Efectivo';
                } elseif($request->pago_tarjeta > 0) {
                    $metodoPago = 'Tarjeta';
                }

        Order::findOrFail($request->order_id)->update([
                'due' => $nuevo_due,
                'pay' => $nuevo_saldo,
                'payment_status'=>'pagado',
                'num_ticket' => $request->num_ticket, // Guardar número de ticket si se proporciona
                'num_tarjeta' => $request->num_tarjeta, // Guardar últimos 4 dígitos de la tarjeta si se proporciona
                'metodo_pago' => $metodoPago,
            ]);

        // Apartado para registrar la transacción en caja y sumar efectivo a la caja
        $hoy = now()->timezone('America/Mexico_City')->toDateString();
        $empleado = auth()->user();

        $caja = Caja::where('branche_id', $empleado->branche_id)
                    ->where('user_id', $empleado->id)
                    ->whereDate('fecha', $hoy)
                    ->where('estado', 'abierta')
                    ->firstOrFail();


            $descripcion = 'Pago de venta numero ' . $order->invoice_no . '. ';
                if ($request->pago_efectivo > 0) {
                    $descripcion .= 'Efectivo: $' . number_format($request->pago_efectivo, 2) . '. ';
                    $caja->monto_final += $order->total;
                    $caja->save();
                }
                if ($request->pago_tarjeta > 0) {
                    $descripcion .= 'Tarjeta: $' . number_format($request->pago_tarjeta, 2). '. '. ' Número de ticket (Terminal): ' . $request->num_ticket . '. ';
                }
                // Registrar transacción a partir de la forma de pago
                Transaction::create([
                    'tipo_transaccion' => 'venta',
                    'metodo_pago'      => $metodoPago,
                    'fecha'            => $hoy,
                    'hora'             => Carbon::now()->timezone('America/Mexico_City')->format('Y-m-d H:i:s'),
                    'monto'            => $total_pagado,
                    'total'            => $order->total,
                    'descripcion'      => $descripcion,
                    'caja_id'          => $caja->id,
                    ]);

        // return redirect()->route('order.pendingDue')->with('success', 'Deuda pagada exitosamente!!.');
        return redirect()->route('order.ticket', ['id' => $request->order_id]);
    }

    public function cancelledOrders()
    {
        $empleado = auth()->user();
        $row = (int) request('row', 20);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        $hoy = now()->toDateString();
        $hasSortable = request()->has('sort');

        $query = Order::where('order_status', 'cancelada')
                    ->where('branche_id',$empleado->branche_id);

        // Filtro por búsqueda de texto
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                ->orWhere('sub_total', 'like', "%{$search}%");
            });
        }

        // Filtro por fecha exacta
        if ($date = request('order_date')) {
            $query->whereDate('order_date', $date);
        }

        // Ordena primero los pedidos del día de hoy si no se está usando sort manual
        if (!$hasSortable) {
            $query->orderByRaw("order_date = ? DESC", [$hoy])
                ->orderBy('order_date', 'desc');
        }

        $orders = $query->sortable([
                        'order_date',
                        'invoice_no',
                        'pay',
                    ])
                    ->paginate($row)
                    ->appends(request()->all());

        return view('orders.cancel-orders', compact('orders'));
    }
    // Metodo para imprimir el ticket como pdf
    public function showTicket($id)
    {
        $venta = Order::findOrFail($id);
        $cajero = auth()->user();
        $sucursal = Branche::findOrFail($venta->branche_id);

        $productos = orderDetails::query()
                ->join('orders','orders.id','=','order_details.order_id')
                ->join('products','products.id','=','order_details.product_id')
                ->leftjoin('equivalencias','equivalencias.id','=','products.equivalencia_id')
                ->select(
                    'products.product_name as producto',
                    'order_details.unitcost as coto_product',
                    'order_details.quantity as cantidad',
                    'order_details.total as total',
                    'equivalencias.abreviatura as unidad',
                    )
                ->where('order_details.order_id',$venta->id)
                ->get();
        // $sucursal= Sucursal::where('id',)

        // dd($venta, $productos);

        return view('ventas.ticket', compact('venta','productos','cajero','sucursal'));
    }

    public function cambiarenvio(Int $order_id)
    {
        $order = Order::findOrFail($order_id);

        $order->enviar = 1; // o 1 si la columna es tipo entero
        $order->save();

        return redirect()->route('order.pendingOrders')
            ->with('success', 'El estado de envío fue actualizado correctamente.');
    }

    // Metodo para imprimir el ticket con QZ
    public function rawTicket($id)
    {
        $venta = Order::findOrFail($id);
        $empleado = auth()->user();
        $productos = OrderDetails::with('product')->where('order_id', $id)->get();

        $ticket = "FERRETERA ACUARIO\n";
        $ticket .= "AV. Universidad Mz 12 Lote 5\n";
        $ticket .= "------------------------------\n";
        $ticket .= "Venta: {$venta->invoice_no}\n";
        $ticket .= "Fecha: " . now()->format('d/m/Y H:i') . "\n";
        $ticket .= "Cajero: {$empleado->name}\n";
        $ticket .= "\n\n";


        $ticket .= "\n\n";
        foreach ($productos as $item) {
            $ticket .= "{$item->product->product_name}\n";
            $ticket .= "{$item->quantity} x $" . number_format($item->unitcost, 2) .
                    " = $" . number_format($item->total, 2) . "\n";
        }

        $ticket .= "------------------------------\n";
        $ticket .= "Subtotal: $" . number_format($venta->sub_total, 2) . "\n";
        $ticket .= "Pagado:   $" . number_format($venta->pay, 2) . "\n";
        $ticket .= "Cambio:   $" . number_format($venta->due, 2) . "\n";
        $ticket .= "------------------------------\n";
        $ticket .= "¡Gracias por su compra!\n\n";

        return response($ticket)->header('Content-Type', 'text/plain');
    }

// public function rawTicket($id)
// {
//     // Función para validar texto (solo ASCII imprimible, espacios y saltos de línea)
//     function esTextoValido($texto) {
//         return preg_match('/^[\x20-\x7E\s\t\r\n]*$/', $texto);
//     }

//    function limpiarTexto($texto) {
//     // Convierte a ASCII, elimina tildes, e.g. "á" => "a"
//     $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
//     // Quita todo lo que no sea letra, número, espacio o signos básicos permitidos
//     return preg_replace('/[^A-Za-z0-9\s\.\,\-\#]/', '', $texto);
// }

// $empleado = auth()->user();
// $venta = Order::findOrFail($id);
// $sucursal = Branche::findOrFail($venta->branche_id);
// $productos = OrderDetails::with('product')->where('order_id', $id)->get();

// // Limpieza de campos específicos
// $empleado_nombre = limpiarTexto($empleado->name);
// $sucursal_nombre = limpiarTexto($sucursal->nombre);
// $sucursal_direccion = limpiarTexto($sucursal->direccion);
// $sucursal_telefono = limpiarTexto($sucursal->telefono);
// $venta_numero = limpiarTexto($venta->invoice_no);

// // Cuando armes el ticket, usa las variables limpias:
// $ticket = "FERRETERA ACUARIO\n";
// $ticket .= "Sucursal: " . $sucursal_nombre . "\n";
// $ticket .= $sucursal_direccion . "\n";
// $ticket .= "Tel: " . $sucursal_telefono . "\n";
// $ticket .= "------------------------------\n";
// $ticket .= "Cajero: " . $empleado_nombre . "\n";
// $ticket .= "Venta: {$venta_numero}\n";
// $ticket .= "Fecha: " . now()->timezone('America/Mexico_City')->format('d/m/Y H:i') . "\n";
// $ticket .= "------------------------------\n";

//     // foreach ($productos as $item) {
//     //     $ticket .= "{$item->product->product_name}\n";
//     //     $ticket .= "{$item->quantity} x $" . number_format($item->unitcost, 2) .
//     //                " = $" . number_format($item->total, 2) . "\n";
//     // }

//     // $ticket .= "------------------------------\n";
//     // $ticket .= "Subtotal: $" . number_format($venta->sub_total, 2) . "\n";
//     // $ticket .= "Pagado:   $" . number_format($venta->pay, 2) . "\n";
//     // $ticket .= "Cambio:   $" . number_format($venta->due, 2) . "\n";
//     // $ticket .= "------------------------------\n";
//     // $ticket .= "¡Gracias por su compra!\n\n\n";

//     // Validar texto antes de responder
//     if (!esTextoValido($ticket)) {
//         \Log::error("Ticket contiene caracteres inválidos. No se imprimirá. Venta ID: {$id}");
//         abort(422, "El ticket contiene caracteres inválidos para la impresora.");
//     }

//     return response($ticket)->header('Content-Type', 'text/plain');
// }




    public function printView($id)
    {
        $venta = Order::findOrFail($id); // solo necesitamos el ID para imprimir
        return view('ventas.auto_print', compact('venta'));
    }

    
}
