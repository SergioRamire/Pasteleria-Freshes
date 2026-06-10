<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class CustomerController extends Controller
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

        return view('customers.index', [
            'customers' => Customer::filter(request(['search']))->sortable()->paginate($row)->appends(request()->query()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $rules = [
        'photo' => 'nullable|image|file|max:1024',
        'rfc' => ['nullable','regex:/^([A-ZÑ&]{3,4})[0-9]{6}[A-Z0-9]{3}$/','unique:Customers,rfc'],
        'tipo_persona' => 'nullable|string|max:25',
        'name' => 'required|string|max:50|regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/',
        'shopname' => 'required|string|max:50|regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/',
        'email' => ['required','email','max:50','unique:customers,email','regex:/^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,}$/'],
        'phone' => 'required|string|size:10|unique:customers,phone|regex:/^[0-9]{10}$/',
        'phone2' => 'nullable|string|size:10|unique:customers,phone2|regex:/^[0-9]{10}$/',
        'type_customer' => 'required|string|max:50',
        'num_exterior' => 'required|string|max:50',
        'num_interior' => 'nullable|string|max:50',
        'cp' => 'required|string|size:5|regex:/^[0-9]{5}$/',
        'calle' => 'required|string|max:50',
        'colonia' => 'required|string|max:50',
        'rul_maps' => 'nullable|string|max:255',
        'municipio' => 'required|string|max:50',
        'estado' => 'required|string|max:50',
        'pais' => 'required|string|max:50',
        'regimen_fiscal'=>'nullable|string|max:50',
        'uso_cfdi'=>'nullable|string|max:50',
        'referencia'=>'nullable|string|max:50',
        ];

        $validatedData = $request->validate($rules);

        /**
         * Handle upload image with Storage.
         */
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
            $path = 'public/customers/';

            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        Customer::create($validatedData);

        return Redirect::route('customers.index')->with('success', '¡El cliente ha sido creado exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return view('customers.show', [
            'customer' => $customer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', [
            'customer' => $customer
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        // dd($request->all());
        $rules = [
            'photo' => 'nullable|image|file|max:1024',

            'rfc' => [
                'nullable',
                'regex:/^([A-ZÑ&]{3,4})[0-9]{6}[A-Z0-9]{3}$/',
                'unique:customers,rfc,' . $customer->id,
            ],

            'tipo_persona' => 'nullable|string|max:25',

            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/',
            ],

            'shopname' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/',
            ],

            'email' => [
                'required',
                'email',
                'max:50',
                'regex:/^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,}$/',
                'unique:customers,email,' . $customer->id,
            ],

            'phone' => [
                'required',
                'string',
                'size:10',
                'regex:/^[0-9]{10}$/',
                'unique:customers,phone,' . $customer->id,
            ],
            'phone2' => [
                'nullable',
                'string',
                'size:10',
                'regex:/^[0-9]{10}$/',
                'unique:customers,phone2,' . $customer->id,
            ],

            // Dirección
            'type_customer' => 'required|string|max:50',
            'num_exterior' => 'required|string|max:50',
            'num_interior' => 'nullable|string|max:50',
            'cp' => ['required', 'string', 'size:5', 'regex:/^[0-9]{5}$/'],
            'calle' => 'required|string|max:50',
            'colonia' => 'required|string|max:50',
            'municipio' => 'required|string|max:50',
            'estado' => 'required|string|max:50',
            'pais' => 'required|string|max:50',
            'rul_maps' => 'nullable|string|max:255',
            'regimen_fiscal'=>'nullable|string|max:50',
            'uso_cfdi'=>'nullable|string|max:50',
            'referencia'=>'nullable|string|max:50',

        ];

        $validatedData = $request->validate($rules);

        // Manejo de la imagen
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $path = 'public/customers/';

            // Eliminar foto antigua si existe
            if ($customer->photo) {
                Storage::delete($path . $customer->photo);
            }

            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        Customer::where('id', $customer->id)->update($validatedData);

        return Redirect::route('customers.index')->with('success', '¡El cliente ha sido actualizado exitosamente!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        /**
         * Delete photo if exists.
         */
        if($customer->photo){
            Storage::delete('public/customers/' . $customer->photo);
        }

        Customer::destroy($customer->id);

        return Redirect::route('customers.index')->with('success', '¡El cliente ha sido eliminado!');
    }
}
