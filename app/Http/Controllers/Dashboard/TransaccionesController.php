<?php

namespace App\Http\Controllers\Dashboard;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Models\Caja;
use App\Models\Transaction;
use App\Models\User;


class TransaccionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
            $user = auth()->user();
            $row = (int) request('row', 10);

            if ($row < 1 || $row > 100) {
                abort(400, 'El parámetro por página debe estar entre 1 y 100.');
            }

            $query = Transaction::select('transactions.*', 'users.name as nombre_usuario', 'branches.nombre as nombre_sucursal', 'cajas.id as numero_caja')
                ->join('cajas', 'transactions.caja_id', '=', 'cajas.id')
                ->join('users', 'cajas.user_id', '=', 'users.id')
                ->join('branches', 'cajas.branche_id', '=', 'branches.id');
            // Filtro de búsqueda
            if ($search = request('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('tipo_transaccion', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%")
                    ->orWhere('monto', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%"); // ← búsqueda por nombre de usuario
                });
            }

            // Filtro por fecha exacta
            if ($date = request('fecha')) {
                $query->whereDate('transactions.fecha', $date);
            }

            // Filtro por metodo_pago
           if ($tipo = request('tipo_transaccion')) {
                $query->where('tipo_transaccion', $tipo); // corregido: era whereDate, ahora es where normal
            }

            // Ordenamiento
            $query = $query->sortable([
                'fecha',
                'hora_apertura',
                'numero_caja',
            ]);

            $query->orderBy('fecha', 'desc');

            // Paginación con parámetros conservados
            $transacciones = $query->paginate($row)->appends(request()->query());

            // $sucursales = DB::table('branches')->get();

            return view('transacciones.index', compact('transacciones'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $empleado = auth()->user();
         // consulta que verifica si la caja está abierta, perteneciente al usuario y a la sucursal
         $hoy = now()->timezone('America/Mexico_City')->toDateString();
        // dd($user);

        $caja = DB::table('cajas')
                ->where('branche_id', $empleado->branche_id)      // misma sucursal
                ->where('user_id', $empleado->id)                 // mismo usuario
                ->whereDate('fecha', $hoy)                    // misma fecha
                ->where('estado', 'abierta')                  // debe estar abierta
                ->first();


        if (!$caja) {
            return redirect()->route('transacciones.index')->with('error', 'Debe tener una caja abierta y asignada para realizar una nueva transacción!.');
        }
        // Si la caja está abierta, se procede a crear la transacción

        return view('transacciones.create', compact('empleado', 'caja'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'tipo_transaccion' => 'required|string|max:255',
            'metodo_pago' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
        ];

        $hoy = now()->timezone('America/Mexico_City')->toDateString();

        $empleado = auth()->user();
        $monto = abs($request->monto);


        $caja = Caja::where('branche_id', $empleado->branche_id)
            ->where('user_id', $empleado->id)
            ->whereDate('fecha', $hoy)
            ->where('estado', 'abierta')
            ->firstOrFail();

        // Verificar si el monto es menor que el monto_final de la caja
        if ($monto >= $caja->monto_final) {
            return redirect()->back()->with('error', 'El monto ingresado de la transacción no puede ser mayor o igual al monto final de la caja abierta.');
        }

        $validatedData = $request->validate($rules);
        $validatedData['fecha'] = $hoy;
        $validatedData['hora'] = now()->timezone('America/Mexico_City')->format('H:i:s');

        if ($request->tipo_transaccion === 'devolucion' || $request->tipo_transaccion === 'retiro') {
                $validatedData['monto'] = -$monto; // convertir a negativo si no lo es
                $validatedData['total'] = -$monto;
                $caja->monto_final -= $monto; // restar al monto final
            } else if($request->tipo_transaccion === 'ingreso') {
                $validatedData['monto'] = $monto;
                 $validatedData['total'] = $monto;
                $caja->monto_final += $monto; // sumar si es ingreso
            }

        $validatedData['caja_id'] = $caja->id; // asignar el ID de la caja abierta

        // dd($validatedData);
         Transaction::create($validatedData);
         $caja->save();

         return Redirect::route('transacciones.index')->with('success', 'Transacción creada exitosamente!');

    }

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        // $transaccion = Transaccione::findOrFail($id);
        $transaccion = Transaction::query()
            ->join('cajas', 'transactions.caja_id', '=', 'cajas.id')
            ->join('users', 'cajas.user_id', '=', 'users.id')
            ->join('branches', 'cajas.branche_id', '=', 'branches.id')
            ->where('transactions.id', $id)
            ->select('transactions.*', 'users.name as nombre_usuario', 'branches.nombre as nombre_sucursal', 'cajas.numero_caja')
            ->first();

        return view('transacciones.show', compact('transaccion'));
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
