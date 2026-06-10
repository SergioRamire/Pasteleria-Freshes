<?php

namespace App\Http\Controllers\Dashboard;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Models\Caja;
use App\Models\Transaction;
use App\Models\User;

class MiCajaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hoy = now()->timezone('America/Mexico_City')->toDateString();
        $user = auth()->user();
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe estar entre 1 y 100.');
        }

        $query = Caja::select('cajas.*', 'users.name as nombre_usuario', 'branches.nombre as nombre_sucursal')
            ->join('users', 'cajas.user_id', '=', 'users.id')
            ->join('branches', 'cajas.branche_id', '=', 'branches.id')
            ->where('cajas.user_id', '=', $user->id);

        // Filtro de búsqueda
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('cajas.estado', 'like', "%{$search}%")
                ->orWhere('cajas.monto_inicial', 'like', "%{$search}%")
                ->orWhere('cajas.numero_caja', 'like', "%{$search}%")
                ->orWhere('users.name', 'like', "%{$search}%")
                ->orWhere('branches.nombre', 'like', "%{$search}%");
            });
        }

        // Filtro por fecha exacta
        if ($date = request('fecha')) {
            $query->whereDate('cajas.fecha', $date);
        }

        // Filtro por estado exacto
        if ($estado = request('caja')) {
            $query->where('cajas.estado', $estado);
        }

        // Ordenamiento
        $hasSortable = request()->has('sort');

        if (!$hasSortable) {
            $query->orderByRaw("cajas.fecha = ? DESC", [$hoy])
                ->orderBy('cajas.fecha', 'desc')
                ->orderBy('cajas.hora_apertura', 'asc');
        }

        $query = $query->sortable([
            'fecha',
            'hora_apertura',
            'numero_caja',
        ]);

        $cajas = $query->paginate($row)->appends(request()->query());

        $sucursales = DB::table('branches')->get();

        return view('mi_caja.index', compact('cajas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        // $empleados = User::where('branche_id','=',$user->branche_id)->get();

        return view('mi_caja.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            // 'user_id' => 'required|exists:users,id',
            'numero_caja' => 'required|integer',
            'monto_inicial' => 'required|integer',
        ];

        $user = auth()->user();

        // consulta para determinar si el empleado ya tiene una caja abierta
        $hoy = now()->timezone('America/Mexico_City')->toDateString();

        $caja = DB::table('cajas')
                ->where('branche_id', $user->branche_id)      // misma sucursal
                ->where('user_id', $user->id)                 // mismo usuario
                ->whereDate('fecha', $hoy)                    // misma fecha
                ->where('estado', 'abierta')                  // debe estar abierta
                ->first();

        if ($caja) {
            return redirect()->back()->withInput()->with('error', 'Ya tiene asignada una caja el dia de hoy.');
        }

        $caja_numero = DB::table('cajas')
            // ->where('branche_id', $user->branche_id)
            ->where('numero_caja', $request->numero_caja)
            ->whereDate('fecha', $hoy)
            ->where('estado', 'abierta')
            ->where('user_id', '!=', $user->id) // Asegura que sea otro usuario
            ->first();

        if ($caja_numero) {
            return redirect()->back()->withInput()
                ->with('error', 'El número de caja ya está asignado a otro cajero el día de hoy.');
        }


        $validatedData = $request->validate($rules);
        $validatedData['estado'] = "abierta";
        $validatedData['fecha'] = $hoy;
        $validatedData['hora_apertura'] = now()->timezone('America/Mexico_City');
        $validatedData['monto_final'] = $request->monto_inicial; // Asignar el monto inicial como monto final
        $validatedData['branche_id'] = $user->branche_id;
        $validatedData['user_id'] = $user->id;

        Caja::create($validatedData);

        return Redirect::route('mis_cajas.index')->with('success', 'Caja abierta!');
    }

    /**
     * Display the specified resource.
     */
    public function show($caja)
    {
         $caja = Caja::query()
                    ->join('users', 'cajas.user_id', '=', 'users.id')
                    ->join('branches', 'cajas.branche_id', '=', 'branches.id')
                    ->where('cajas.id', $caja)
                    ->select('cajas.*', 'users.name as nombre_usuario','users.id as id_user' ,'branches.nombre as nombre_sucursal')
                    ->first();

        $transacciones = Transaction::query()
                ->join('cajas', 'transactions.caja_id', '=', 'cajas.id')
                ->where('cajas.id', $caja->id)
                ->select('transactions.*')
                ->orderBy('transactions.hora', 'desc')
                ->paginate(10);
        // dd($transacciones);

        return view('mi_caja.show', compact('caja', 'transacciones'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($caja)
    {
        $caja = Caja::query()
                    ->join('users', 'cajas.user_id', '=', 'users.id')
                    ->join('branches', 'cajas.branche_id', '=', 'branches.id')
                    ->where('cajas.id', $caja)
                    ->select('cajas.*', 'users.name as nombre_usuario','users.id as id_user' ,'branches.nombre as nombre_sucursal')
                    ->first();

        $transacciones = Transaction::query()
                ->join('cajas', 'transactions.caja_id', '=', 'cajas.id')
                ->where('cajas.id', $caja->id)
                ->select('transactions.*')
                ->orderBy('transactions.hora', 'desc')
                ->paginate(10);
        // dd($transacciones);

        return view('mi_caja.close', compact('caja', 'transacciones'));
    }



    public function update(Request $request, $caja)
    {
        $hora_cierre = now()->timezone('America/Mexico_City')->format('H:i:s');
        $estado = 'cerrada';

        $validatedData = [
            'hora_cierre' => $hora_cierre,
            'estado' => $estado,
        ];

        Caja::where('id', $caja)->update($validatedData);



        // return redirect()->route('caja_sucursal.index')->with('success', '¡La caja se ha cerrado exitosamente!');
         $urlImprimir = route('mis_cajas.imprimir_cerrar_caja', ['id' => $caja]);

        // Pasamos la URL a la sesión
        return redirect()->route('mis_cajas.index')->with([
            'success' => '¡La caja se ha cerrado exitosamente!',
            'imprimir_url' => $urlImprimir
        ]);

    }

    public function imprimir_reporte_show(Request $request)
    {
        $cajaId = $request->input('caja_id'); // Recupera el valor enviado por el formulario

        return $this->imprimir($cajaId);
    }

    //Metodo para imprimir en una nueva vista el corte de caja con todas sus transacciones
    public function imprimir($id)
    {
            $caja = Caja::query()
                        ->join('users', 'cajas.user_id', '=', 'users.id')
                        ->join('branches', 'cajas.branche_id', '=', 'branches.id')
                        ->where('cajas.id', $id)
                        ->select('cajas.*', 'users.name as nombre_usuario','users.id as id_user' ,'branches.nombre as nombre_sucursal')
                        ->first();

            $transacciones = Transaction::query()
                    ->join('cajas', 'transactions.caja_id', '=', 'cajas.id')
                    ->where('cajas.id', $caja->id)
                    ->select('transactions.*')
                    ->orderBy('transactions.hora', 'desc')
                    ->get();
            $tota_ventas = Transaction::query()
                    ->join('cajas', 'transactions.caja_id', '=', 'cajas.id')
                    ->where('cajas.id', $caja->id)
                    ->where('transactions.tipo_transaccion', 'venta') // Ojo aquí: 'transactions', no 'transacciones'
                    ->select('transactions.total')
                    ->orderBy('transactions.hora', 'desc')
                    ->get();
            // dd($tota_ventas);
            // Calcular el total de cambio entregado
        $cambios = $transacciones->filter(function ($t) {
            return preg_match('/Cambio:\s*\$-?\d+[\.,]?\d*/', $t->descripcion);
        })->sum(function ($t) {
            preg_match('/Cambio:\s*\$(-?\d+(?:[\.,]\d+)?)/', $t->descripcion, $match);
            return isset($match[1]) ? floatval(str_replace(',', '', $match[1])) : 0;
        });

        $tarjetas = $transacciones->filter(function ($t) {
            return preg_match('/Tarjeta:\s*\$-?\d+[\.,]?\d*/', $t->descripcion);
        })->sum(function ($t) {
            preg_match('/Tarjeta:\s*\$(-?\d+(?:[\.,]\d+)?)/', $t->descripcion, $match);
            return isset($match[1]) ? floatval(str_replace(',', '', $match[1])) : 0;
        });

        $efectivo = $transacciones->filter(function ($t) {
            return preg_match('/Efectivo:\s*\$-?\d+[\.,]?\d*/', $t->descripcion);
        })->sum(function ($t) {
            preg_match('/Efectivo:\s*\$(-?\d+(?:[\.,]\d+)?)/', $t->descripcion, $match);
            return isset($match[1]) ? floatval(str_replace(',', '', $match[1])) : 0;
        });

        $retiros =   Transaction::query()
                    ->join('cajas', 'transactions.caja_id', '=', 'cajas.id')
                    ->where('cajas.id', $caja->id)
                    ->where('transactions.tipo_transaccion', 'retiro') // Ojo aquí: 'transactions', no 'transacciones'
                    ->orWhere('transactions.tipo_transaccion', 'devolucion') // Incluye devoluciones
                    ->select('transactions.total')
                    ->orderBy('transactions.hora', 'desc')
                    ->get();

        return view('corte_caja_sucursal.imprimir', compact('caja', 'transacciones', 'tarjetas','efectivo','tota_ventas','cambios','retiros'));
    }

    public function create_transaccion($id)
    {
         // consulta que verifica si la caja está abierta, perteneciente al usuario y a la sucursal
         $hoy = now()->timezone('America/Mexico_City')->toDateString();
        $empleado = auth()->user();

        $caja = DB::table('cajas')
                ->where('id', $id)      // misma caja
                ->whereDate('fecha', $hoy)                    // misma fecha
                ->where('estado', 'abierta')                  // debe estar abierta
                ->first();


        if (!$caja) {
            return redirect()->route('mis_cajas.index')->with('error', '¡Debe tener una caja abierta y asignada el día de hoy, para realizar una nueva transacción!.');
        }
        // Si la caja está abierta, se procede a crear la transacción

        return view('mi_caja.create_transaccion', compact('empleado', 'caja'));
    }

    public function store_transaccion(Request $request)
    {
        $rules = [
            'tipo_transaccion' => 'required|string|max:255',
            'metodo_pago' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            // 'caja_id' => 'required|integer',
        ];

        $hoy = now()->timezone('America/Mexico_City')->toDateString();

        $empleado = auth()->user();
        $monto = abs($request->monto);


        $caja = Caja::where('branche_id', $empleado->branche_id)
            ->where('id', $request->caja_id)
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

         Transaction::create($validatedData);
         $caja->save();

         return Redirect::route('mis_cajas.index')->with('success', 'Transacción creada exitosamente!');

    }

    public function destroy(string $id)
    {
        //
    }
}
