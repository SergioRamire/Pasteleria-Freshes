<?php

namespace App\Http\Controllers\Dashboard;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Models\Caja;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Branche;

class CajaSucursalController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Caja::select('cajas.*', 'users.name as nombre_usuario', 'branches.nombre as nombre_sucursal')
            ->join('users', 'cajas.user_id', '=', 'users.id')
            ->join('branches', 'cajas.branche_id', '=', 'branches.id')
            ->where('cajas.branche_id', '=', $user->branche_id);

        // Filtro de búsqueda
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('cajas.estado', 'like', "%$search%")
                ->orWhere('cajas.monto_inicial', 'like', "%$search%")
                ->orWhere('cajas.numero_caja', 'like', "%$search%")
                ->orWhere('users.name', 'like', "%$search%");
            });
        }

        // Filtro por estado exacto
        if ($estado = $request->input('caja')) {
            $query->where('cajas.estado', '=', $estado);
        }

        // Filtro por fecha exacta
        if ($date = $request->input('fecha')) {
            $query->whereDate('fecha', $date);
        }

        // Ordenamiento
        $hasSortable = $request->has('sort');
        if (!$hasSortable) {
            $hoy = now()->toDateString();
            $query->orderByRaw("fecha = ? DESC", [$hoy])
                ->orderBy('fecha', 'desc')
                ->orderBy('hora_apertura', 'asc');
        }

        $query = $query->sortable([
            'fecha',
            'hora_apertura',
            'numero_caja',
        ]);

        $row = $request->input('row', 10); // Por defecto 10 si no se envía
        $cajas = $query->paginate($row)->appends($request->query());

        $sucursales = DB::table('branches')->get();

        return view('corte_caja_sucursal.index', compact('cajas', 'sucursales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $empleados = User::where('branche_id','=',$user->branche_id)->get();
        $sucursal = Branche::where('id',$user->branche_id)->first();
        return view('corte_caja_sucursal.create', compact('empleados','sucursal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $rules = [
            'user_id' => 'required|exists:users,id',
            'numero_caja' => 'required|integer',
            'monto_inicial' => 'required|integer',
        ];

        $user = auth()->user();

        // consulta para determinar si el empleado ya tiene una caja abierta
        $hoy = now()->timezone('America/Mexico_City')->toDateString();

        $caja = DB::table('cajas')
                ->where('branche_id', $user->branche_id)      // misma sucursal
                ->where('user_id', $request->user_id)                 // mismo usuario
                ->whereDate('fecha', $hoy)                    // misma fecha
                ->where('estado', 'abierta')                  // debe estar abierta
                ->first();

        if ($caja) {
            return redirect()->back()->withInput()
                ->with('error', 'El cajero seleccionado ya tiene asignada una caja abierta el día de hoy.');
        }

        // Validar si el número de caja ya está en uso por otro usuario
        $caja_numero = DB::table('cajas')
            // ->where('branche_id', $user->branche_id)
            ->where('numero_caja', $request->numero_caja)
            ->whereDate('fecha', $hoy)
            ->where('estado', 'abierta')
            ->where('user_id', '!=', $request->user_id) // Asegura que sea otro usuario
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

        Caja::create($validatedData);

        return Redirect::route('caja_sucursal.index')->with('success', 'Caja abierta!');
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

        return view('corte_caja_sucursal.show', compact('caja', 'transacciones'));
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

        return view('corte_caja_sucursal.close', compact('caja', 'transacciones'));
    }


    /**
     * Update the specified resource in storage.
     */
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
         $urlImprimir = route('cajas.imprimir_cerrar_caja', ['id' => $caja]);

        // Pasamos la URL a la sesión
        return redirect()->route('caja_sucursal.index')->with([
            'success' => '¡La caja se ha cerrado exitosamente!',
            'imprimir_url' => $urlImprimir
        ]);

    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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


}
