<?php
namespace App\Http\Controllers\Dashboard;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Models\Caja;
use App\Models\Transaction;
use App\Models\User;

class CajaController extends Controller
{
    public function index()
    {
         $hoy = now()->timezone('America/Mexico_City')->toDateString();
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe estar entre 1 y 100.');
        }

        $query = Caja::select('cajas.*', 'users.name as nombre_usuario', 'branches.nombre as nombre_sucursal')
                        ->join('users', 'cajas.user_id', '=', 'users.id')
                        ->join('branches', 'cajas.branche_id', '=', 'branches.id');

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
            $query->whereDate('fecha', $date);
        }

          // Filtro por sucursal (branche_id)
        if ($sucursal = request('sucursal')) {
            $query->where('cajas.branche_id', $sucursal);
        }

        // Ordenamiento
            $hasSortable = request()->has('sort');

            // Solo aplica orden fijo si NO hay sortable en la URL
            if (!$hasSortable) {
                $query->orderByRaw("fecha = ? DESC", [$hoy])
                    ->orderBy('fecha', 'desc')
                    ->orderBy('hora_apertura', 'asc');
            }

            // Aplicar sortable
            $query = $query->sortable([
                'fecha',
                'hora_apertura',
                'numero_caja',
            ]);


        // Paginación
        $cajas = $query->paginate($row)->appends(request()->query());

        $sucursales = DB::table('branches')->get();

        return view('corte_caja.index', compact('cajas', 'sucursales'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sucursales = DB::table('branches')->get();
        $usuarios = DB::table('users')->get();
        // $usuarios = DB::table('users')->where('role', 'cajero')->get();
        return view('corte_caja.create', compact('sucursales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $rules = [
            'user_id' => 'required|exists:users,id',
            'branche_id' => 'required|exists:users,id',
            'numero_caja' => 'required|integer',
            // 'fecha' => 'date_format:Y-m-d|max:10|nullable',
            // 'hora_apertura' => 'required|date_format:H:i',
            'monto_inicial' => 'required|integer',
        ];

        $hoy = now()->timezone('America/Mexico_City')->toDateString();

        $caja = DB::table('cajas')
                ->where('branche_id', $request->branche_id)      // misma sucursal
                ->where('user_id', $request->user_id)                 // mismo usuario
                ->whereDate('fecha', $hoy)                    // misma fecha
                ->where('estado', 'abierta')                  // debe estar abierta
                ->first();

        if ($caja) {
            return redirect()->back()->withInput()
                ->with('error', 'El cajero seleccionado ya tiene una caja asignada en esta sucursal para el día de hoy.');
        }


         // Validar si el número de caja ya está en uso por otro usuario
        $caja_numero = DB::table('cajas')
            ->where('branche_id', $request->branche_id)
            ->where('numero_caja', $request->numero_caja)
            ->whereDate('fecha', $hoy)
            ->where('estado', 'abierta')
            ->where('user_id', '!=', $request->user_id) // Asegura que sea otro usuario
            ->first();

        if ($caja_numero) {
            return redirect()->back()->withInput()
                ->with('error', 'El número de caja ya está asignado a otro cajero en esta sucursal el día de hoy.');
        }

        $validatedData = $request->validate($rules);
        $validatedData['estado'] = "abierta";
        $validatedData['monto_final'] = $request->monto_inicial;
        $validatedData['fecha'] = $hoy;
        $validatedData['hora_apertura'] = now()->timezone('America/Mexico_City');
        $sucursal = DB::table('branches')->where('id',$request->branche_id)->first();

        Caja::create($validatedData);

        return Redirect::route('cajas_sucursales.index')->with('success', 'Caja abierta en la sucursal '.$sucursal->nombre.'!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
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
                ->paginate(10);
        // dd($transacciones);

        return view('corte_caja.show', compact('caja', 'transacciones'));
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

    // Funcion para seleccionar a los empleados por sucursal en el select de create
    public function getBySucursal($id)
    {
        $empleados = User::where('branche_id', $id)->get(['id', 'name']); // ajusta los campos según tu modelo
        return response()->json($empleados);
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
