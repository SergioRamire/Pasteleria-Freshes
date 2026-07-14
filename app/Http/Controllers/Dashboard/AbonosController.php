<?php


namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Caja;
use App\Models\Customer;
use App\Models\Branche;
use App\Models\Payment;
use App\Models\Transaction;

class AbonosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Metodo para registrar un abono a una venta e imprimir el ticket del abono
     */
   public function store(Request $request)
    {
        $validatedData = $request->validate([
            'venta_id'      => 'required|exists:orders,id',
            'monto'         => 'required|numeric|min:0.01',
            'metodo'        => 'required|string',
            'num_ticket'    => 'required_if:metodo,tarjeta|nullable|string|max:100',
            'num_tarjeta'   => 'required_if:metodo,tarjeta|nullable|string|size:4',
            'observacion'   => 'nullable|string|max:500',
        ],[
            'monto.required'       => 'Debe ingresar el monto del abono.',
            'monto.min'            => 'El monto debe ser mayor a cero.',
            'metodo.required'      => 'Debe seleccionar un método de pago.',
            'num_ticket.required_if' => 'Debe ingresar el número de ticket.',
            'num_tarjeta.required_if' => 'Debe ingresar los últimos 4 dígitos de la tarjeta.',
        ]);

        DB::beginTransaction();

        try {

            $venta = Order::findOrFail($validatedData['venta_id']);

            if ($validatedData['monto'] > $venta->due) {
                return Redirect::back()->with('error', 'El monto del abono no puede ser mayor a la deuda pendiente.');
            }

            // Generar código único
            do {
                $code = 'PAY-' . strtoupper(Str::random(10));
            } while (Payment::where('codigo', $code)->exists());

            // Actualizar la venta
            $venta->pay += $validatedData['monto'];
            $venta->due -= $validatedData['monto'];

            // Si terminó de pagar
            if ($venta->due <= 0) {
                $venta->due = 0;
                // Si manejas un estado puedes descomentar esta línea
                $venta->payment_status = 'pagado';
                $venta->metodo_pago = $validatedData['metodo'];
            }

            $venta->save();

            // Registrar el abono
            $hoy = now()->timezone('America/Mexico_City')->toDateString();

            $payment = Payment::create([
                'codigo'         => $code,
                'order_id'       => $venta->id,
                'fecha'          => $hoy,
                'monto'          => $validatedData['monto'],
                'metodo_pago'    => $validatedData['metodo'],
                'num_ticket'     => $validatedData['num_ticket'] ?? null,
                'num_tarjeta'    => $validatedData['num_tarjeta'] ?? null,
                'observacion'    => $validatedData['observacion'] ?? null,
            ]);

            DB::commit();

            // Apartado para registrar la transacción en caja y sumar efectivo a la caja
            $empleado = auth()->user();

            $caja = Caja::where('branche_id', $empleado->branche_id)
                        ->where('user_id', $empleado->id)
                        ->whereDate('fecha', $hoy)
                        ->where('estado', 'abierta')
                        ->firstOrFail();


            $descripcion = 'Abono de la venta numero ' . $venta->invoice_no . '. ';
            if ($validatedData['metodo'] == "efectivo") {
                $descripcion .= 'Efectivo: $' . number_format($validatedData['monto'], 2) . '. ';
                $caja->monto_final += $validatedData['monto'];
                $caja->save();
            }
            if ($validatedData['metodo'] == "tarjeta") {
                $descripcion .= 'Tarjeta: $' . number_format($validatedData['monto'], 2). '. '. ' Número de ticket (Terminal): ' . $validatedData['num_ticket'] . '. ';
            }
                    // Registrar transacción a partir de la forma de pago
            Transaction::create([
                        'tipo_transaccion' => 'Abono de venta',
                        'metodo_pago'      => $validatedData['metodo'],
                        'fecha'            => $hoy,
                        'hora'             => Carbon::now()->timezone('America/Mexico_City')->format('Y-m-d H:i:s'),
                        'monto'            => $validatedData['monto'],
                        'total'            => $validatedData['monto'],
                        'descripcion'      => $descripcion,
                        'caja_id'          => $caja->id,
                        ]);

            // return Redirect::route('order.pendingDue')
            //     ->with('success', 'Abono registrado correctamente.');
            return redirect()->route('abonos.ticket', ['id' => $payment->id]);

        } catch (\Exception $e) {

            DB::rollBack();

            return Redirect::back()->with('error', $e->getMessage());
        }
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

    
    public function verindex($id)
    {
        // consulta que verifica si la caja está abierta, perteneciente al usuario y a la sucursal
         $hoy = now()->timezone('America/Mexico_City')->toDateString();
         $user = auth()->user();

        $caja = DB::table('cajas')
                ->where('branche_id', $user->branche_id)      // misma sucursal
                ->where('user_id', $user->id)                 // mismo usuario
                ->whereDate('fecha', $hoy)                    // misma fecha
                ->where('estado', 'abierta')                  // debe estar abierta
                ->first();


        if (!$caja) {
            return redirect()->route('order.pendingDue')->with('error', 'Debe abrir una caja antes de realizar un abono del pedido.');
        }
        $venta = Order::query()
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->select('orders.*','customers.name as customer_name')
            ->where('orders.id', $id)
            ->first();
       return view('abonos.abonar', compact('venta'));

        // return response()
        // ->view('abonos.abonar', compact('venta'))
        // ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        // ->header('Pragma', 'no-cache')
        // ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }


    public function abonar(Request $request)
    {
        dd($request->all());
        // Validate the request data
        $request->validate([
            'monto_abono' => 'required|numeric|min:0.01',
        ]);

        // Find the order by ID
        $venta = Order::findOrFail($id);

        // Update the order's due amount
        $venta->due -= $request->input('monto_abono');

        // If the due amount is less than or equal to zero, mark the order as paid
        if ($venta->due <= 0) {
            $order->payment_status = 'pagado';
            $order->due = 0; // Ensure due is not negative
        }

        // Save the updated order
        $order->save();

        // Optionally, you can create a new Payment record here to log the payment

        return redirect()->back()->with('success', 'Abono realizado exitosamente.');
    }

    // Metodo para imprimir el ticket como pdf
    public function showTicket($id)
    {
        $abono = Payment::findOrFail($id);
        $cajero = auth()->user();
        $order = Order::query()
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->select('orders.*','customers.name as customer_name')
            ->where('orders.id', $abono->order_id)
            ->first();
        $sucursal = Branche::findOrFail($cajero->branche_id);
        
        // dd($abono, $cajero, $order, $sucursal);

        return view('abonos.ticket', compact('abono', 'cajero', 'order', 'sucursal'));
    }
}
