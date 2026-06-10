<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class SupplierController extends Controller
{
    /**
     * Mostrar la lista de proveedores.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro de elementos por página debe ser un número entre 1 y 100.');
        }

        return view('suppliers.index', [
            'suppliers' => Supplier::filter(request(['search']))->sortable()->paginate($row)->appends(request()->query()),
        ]);
    }

    /**
     * Mostrar el formulario para crear un nuevo proveedor.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Almacenar un nuevo proveedor en la base de datos.
     */
    public function store(Request $request)
    {
        $rules = [
            'photo' => 'image|file|max:1024',
            'name' => 'required|string|max:50',
            'email' => ['required','email','max:50','unique:suppliers,email','regex:/^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,}$/'],
            'phone' => 'required|string|max:10|unique:suppliers,phone',
            'shopname' => 'required|string|max:50',
            'rfc' => ['required', 'regex:/^([A-ZÑ&]{3,4})?([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[01])[A-Z0-9]{3}$/','unique:suppliers,rfc'],
            'tipo_persona' => 'required|string|max:25',
            'type' => 'nullable|string|max:25',
            'account_holder' => 'max:50',
            'account_number' => 'nullable|string|max:25|unique:suppliers,account_number',
            'bank_name' => 'nullable|string|max:25',
            'city' => 'nullable|string|max:50',
            'address' => 'required|string|max:100',
        ];

        $validatedData = $request->validate($rules);

        // Manejo de carga de imagen
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
            $path = 'public/suppliers/';

            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        Supplier::create($validatedData);

        return Redirect::route('suppliers.index')->with('success', '¡Proveedor creado exitosamente!');
    }

    /**
     * Mostrar la información de un proveedor específico.
     */
    public function show(Supplier $supplier)
    {
        return view('suppliers.show', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Mostrar el formulario para editar un proveedor.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', [
            'supplier' => $supplier
        ]);
    }

    /**
     * Actualizar un proveedor específico.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $rules = [
            'photo' => 'nullable|image|file|max:1024',
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:suppliers,email,' . $supplier->id,
            'phone' => ['required', 'string', 'max:10', 'min:10', 'unique:suppliers,phone,' . $supplier->id, 'regex:/^\d{10}$/'],
            'shopname' => 'required|string|max:50',
            'rfc' => ['required', 'regex:/^([A-ZÑ&]{3,4})?([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[01])[A-Z0-9]{3}$/', 'unique:suppliers,rfc,' . $supplier->id],
            'type' => 'required|string|max:25',
            'account_holder' => 'nullable|string|max:50',
            'account_number' => ['nullable', 'string', 'max:18', 'min:16', 'regex:/^\d{16,18}$/'],
            'bank_name' => 'nullable|string|in:Banamex (Citibanamex),BBVA México,HSBC México,Scotiabank Inverlat,Banorte,Santander México,Banco Azteca,Bancoppel|max:25',
            'tipo_persona' => 'required|string|max:25',
            'city' => 'nullable|string|max:50',
            'address' => 'required|string|max:100',
        ];

        $validatedData = $request->validate($rules);

        // Manejo de carga de imagen
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
            $path = 'public/suppliers/';

            // Eliminar foto anterior si existe
            if($supplier->photo){
                Storage::delete($path . $supplier->photo);
            }

            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        Supplier::where('id', $supplier->id)->update($validatedData);

        return Redirect::route('suppliers.index')->with('success', '¡Proveedor actualizado exitosamente!');
    }

    /**
     * Eliminar un proveedor específico.
     */
    public function destroy(Supplier $supplier)
    {
        // Eliminar imagen si existe
        if($supplier->photo){
            Storage::delete('public/suppliers/' . $supplier->photo);
        }

        Supplier::destroy($supplier->id);

        return Redirect::route('suppliers.index')->with('success', '¡Proveedor eliminado exitosamente!');
    }
}
