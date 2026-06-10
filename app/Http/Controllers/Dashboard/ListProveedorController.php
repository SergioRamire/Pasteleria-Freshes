<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Models\Listproduct;
use App\Models\Detailslistproduct;
use App\Models\Branche;
use App\Models\User;
use App\Models\Product;

class ListProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        $listas = Listproduct::query()
            ->leftJoin('branches', 'branches.id', '=', 'listproducts.sucursal_origen')
            ->select(
                'listproducts.id',
                'listproducts.codigo',
                'listproducts.fecha',
                'listproducts.hora',
                'branches.nombre as sucursal'
            )
            ->when(request('search'), function ($query, $search) {
                $query->where('branches.nombre', 'like', '%' . $search . '%')
                    ->orWhere('listproducts.codigo', 'like', '%' . $search . '%');
            })
            ->when(request('sucursal'), function ($query, $sucursal) {
                $query->where('listproducts.sucursal_origen', $sucursal);
            })
            ->orderBy('listproducts.fecha', 'asc')
            ->paginate($row)
            ->appends(request()->query());

        $sucursales = \App\Models\Branche::all();

        return view('carritocomprasproveedor.indexes', compact('listas', 'sucursales'));
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
    public function show($id)
    {
        $lista= Listproduct::find($id);
        $sucursal=Branche::find($lista->sucursal_origen);
        $empleado = User::find($lista->responsable);

        $produtos = Product::query()
                    ->join('detailslistproducts','products.id','=','detailslistproducts.producto_id')
                    ->join('marcas','marcas.id','=', 'products.marca_id')
                    ->join('suppliers', 'suppliers.id', '=', 'marcas.suppliers_id')
                    ->leftjoin('equivalencias','equivalencias.id','=','products.equivalencia_id')
                    ->select('detailslistproducts.cantidad as cantidad',
                            'products.product_name as producto',
                            'products.product_code as product_code',
                            'products.codigo_barras as codigo_barras',
                            'marcas.nombre as marca','suppliers.name as proveedor','equivalencias.nombre as unidad')
                    ->where('detailslistproducts.listproduct_id',$lista->id)
                    ->get();

        return view('carritocomprasproveedor.print-invoice', [
        'empleado' => $empleado,
        'sucursal' => $sucursal,
        'content' => $produtos,
    ]);
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
