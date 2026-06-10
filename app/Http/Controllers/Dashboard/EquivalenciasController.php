<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Equivalencia;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class EquivalenciasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro de elementos por página debe ser un número entre 1 y 100.');
        }

        return view('equivalencias.index', [
            'equivalencias' => Equivalencia::filter(request(['search']))->sortable()->paginate($row)->appends(request()->query()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('equivalencias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        // dd($request);
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:50',
            'abreviatura' => 'required|string|max:6',
        ]);

        // Asignar estado activo por defecto
        $validatedData['activo'] = 1;

        // Crear la equivalencia
        Equivalencia::create($validatedData);

        // Redirigir con mensaje de éxito
        return redirect()
            ->route('equivalencias.index')
            ->with('success', '¡Equivalencia creada exitosamente!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Equivalencia $equivalencia)
    {
        return view('equivalencias.show', [
            'equivalencia' => $equivalencia
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equivalencia $equivalencia)
    {
        return view('equivalencias.edit', [
            'equivalencia' => $equivalencia
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'nombre' => 'required|string|max:50',
            'abreviatura' => 'required|string|max:6',
            'activo' => 'required|in:0,1',
        ]);

        $equivalencia = Equivalencia::findOrFail($id);
        $equivalencia->update([
            'nombre' => $request->nombre,
            'abreviatura' => $request->abreviatura,
            'descripcion' => $request->descripcion,
            'clave_sat' => $request->clave_sat,
            'tipo' => $request->tipo,
            'activo' => $request->activo,
        ]);

        return redirect()->route('equivalencias.index')->with('success', 'Equivalencia actualizada correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equivalencia $equivalencia)
    {
        $existeEquivalencia = Product::where('equivalencia_id', $equivalencia->id)->exists();

        if ($existeEquivalencia) {
            // No se puede eliminar porque hay inventario relacionado
            return Redirect::route('equivalencias.index')
                ->with('error', '¡No se puede eliminar la equivalencia porque tiene asigando un producto!');
        }

        // Eliminar el producto
        $equivalencia->delete();

        return Redirect::route('equivalencias.index')
            ->with('success', '¡La equivalencia ha sido eliminada exitosamente!');
    }

}
