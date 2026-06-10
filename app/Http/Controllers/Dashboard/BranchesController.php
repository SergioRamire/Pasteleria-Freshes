<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Models\Branche;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;


class BranchesController extends Controller
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


        return view('sucursales.index', [
            'sucursales' => Branche::filter(request(['search']))->sortable()->paginate($row)->appends(request()->query()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sucursales.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'nombre' => 'required|string|max:50',
            'direccion' => 'required|string|max:80',
        ];

        $messages = [
            'nombre.required'   => 'El nombre de la sucursal es obligatorio.',
            'nombre.string'     => 'El nombre de la sucursal debe ser una cadena de texto.',
            'nombre.max'        => 'El nombre de la sucursal no debe exceder los 50 caracteres.',
            'direccion.required'=> 'La dirección es obligatoria.',
            'direccion.string'  => 'La dirección debe ser una cadena de texto.',
            'direccion.max'     => 'La dirección no debe exceder los 80 caracteres.',
        ];

        $validatedData = $request->validate($rules, $messages);

        Branche::create($validatedData);

        return Redirect::route('sucursales.index')->with('success', 'Sucursal agregada exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sucursal = Branche::findOrFail($id);

        return view('sucursales.show', compact('sucursal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $sucursal = Branche::findOrFail($id);

        return view('sucursales.edit', compact('sucursal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'nombre' => 'required|string|max:50',
            'direccion' => 'required|string|max:80',
        ];

        $validatedData = $request->validate($rules);

        /**
         * Handle upload image with Storage.
         */

        $sucursal = Branche::findOrFail($id);
        $sucursal->update($validatedData);

        return Redirect::route('sucursales.index')->with('success', 'Sucursal actualizada exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sucursal = Branche::find($id);

        if (!$sucursal) {
            return redirect()->back()->with('error', 'Sucursal no encontrada.');
        }

        // Verificar si hay usuarios asociados a la sucursal
        $usuariosAsociados = \App\Models\User::where('branche_id', $id)->count();
        if ($usuariosAsociados > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar la sucursal porque tiene usuarios asociados.');
        }

        try {
            $sucursal->delete();
            return redirect()->route('sucursales.index')->with('success', 'Sucursal eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al eliminar la sucursal.');
        }
    }
}
