<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;  // <-- Importar el controlador base
use Illuminate\Http\Request;
use App\Models\Marca;
use App\Models\suppliers;
use Illuminate\Support\Facades\Redirect;

class MarcaController extends Controller
{

    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        $marcas = Marca::query()
            ->leftJoin('suppliers', 'suppliers.id', '=', 'marcas.suppliers_id')
            ->select(
                'marcas.id',
                'marcas.nombre',
                'marcas.suppliers_id',
                'suppliers.name as supplier'
            )
            ->when(request('search'), function ($query, $search) {
                $query->where('marcas.nombre', 'like', '%' . $search . '%');
            })
            ->when(request('supplier'), function ($query, $supplier) {
                $query->where('marcas.suppliers_id', $supplier);
            })
            ->orderBy('marcas.nombre', 'asc')
            ->paginate($row)
            ->appends(request()->query());

        $suppliers = \App\Models\Supplier::all();

        return view('marcas.index', compact('marcas', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('marcas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $rules = [
            'nombre' => 'required|string|max:50',
            'suppliers_id' => 'required|exists:suppliers,id',
        ];

        $validatedData = $request->validate($rules);
        Marca::create($validatedData);

        return Redirect::route('marcas.index')->with('success', 'Marca creada exitosamente!');

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
    public function edit(Marca $marca)
    {
        return view('marcas.edit', [
            'marca' => $marca
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marca $marca)
    {
        $rules = [
            'nombre' => 'required|string|max:50',
            'suppliers_id' => 'required|exists:suppliers,id',
        ];

        $validatedData = $request->validate($rules);

        Marca::where('id', $marca->id)->update($validatedData);

        return Redirect::route('marcas.index')->with('success', 'Marca creada exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $marca = Marca::find($id);

        if (!$marca) {
            return redirect()->back()->with('error', '⚠️ La marca no fue encontrada.');
        }

        // Verifica si hay productos asociados a la marca
        $productosAsociados = \App\Models\Product::where('marca_id', $id)->count();

        if ($productosAsociados > 0) {
            return redirect()->back()->with('error', '⚠️ No se puede eliminar esta marca porque tiene productos asociados.');
        }

        try {
            $marca->delete();
            return redirect()->route('marcas.index')->with('success', '✅ Marca eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Ocurrió un error al intentar eliminar la marca.');
        }
    }
}
