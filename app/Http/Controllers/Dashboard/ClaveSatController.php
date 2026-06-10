<?php


namespace App\Http\Controllers\Dashboard;

use App\Models\Satclave;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;


class ClaveSatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 30);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro de elementos por página debe ser un número entre 1 y 100.');
        }

        return view('satclaves.index', [
            'claves' => Satclave::filter(request(['search']))->sortable()->paginate($row)->appends(request()->query()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('satclaves.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'c_ClaveProdServ' => 'required|string|max:10|unique:satclaves,c_ClaveProdServ',
            'descripcion' => 'required|string|max:100',
        ]);

        // Asignar estado activo por defecto
        $validatedData['activo'] = 1;

        // Crear la equivalencia
        Satclave::create($validatedData);

        // Redirigir con mensaje de éxito
        return redirect()
            ->route('satclaves.index')
            ->with('success', '¡Clave Sat creada exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show($clave)
    {
       $clave = Satclave::find($clave);
        return view('satclaves.show', compact('clave'));
    }

    public function edit($clave)
    {
        $clave = Satclave::find($clave);
        return view('satclaves.edit', compact('clave'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $clave = Satclave::findOrFail($id);
        // dd($request->all());
        $request->validate([
            'c_ClaveProdServ' => 'required|string|max:50|unique:satclaves,c_ClaveProdServ,' . $clave->id . ',id',
            'descripcion' => 'required|string|max:255',
            'activo' => 'required|in:0,1',
        ]);


        $clave->update([
            'c_ClaveProdServ' => $request->c_ClaveProdServ,
            'descripcion' => $request->descripcion,
            'activo' => $request->activo,
        ]);

        return redirect()->route('satclaves.index')->with('success', 'Clave actualizada correctamente.');
    }

    public function verProductos(Request $request, $id)
    {

        $query = Product::where('satclave_id', $id);

        // Filtrar por nombre o código del producto si hay búsqueda
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->search . '%')
                ->orWhere('product_code', 'like', '%' . $request->search . '%');
            });
        }

        // Número de filas por página (por defecto 5)
        $row = $request->input('row', 20);

        // Obtener productos paginados
        $productos = $query->orderBy('product_name')->paginate($row);

        $clave = Satclave::find($id);

        return view('satclaves.verproductos', compact('productos', 'clave'));
    }



    public function agregarASatClave(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'busqueda' => 'required|string',
            'satclave_id' => 'required|exists:satclaves,id'
        ]);

        // Buscar producto por nombre o código
        $producto = Product::where('product_name', 'like', '%' . $request->busqueda . '%')
            ->orWhere('product_code', 'like', '%' . $request->busqueda . '%')
            ->orWhere('codigo_barras', 'like', '%' . $request->busqueda . '%')
            ->first();

        if ($producto) {
            $producto->satclave_id = $request->satclave_id;
            $producto->save();

            return redirect()->back()->with('success', 'Producto asociado correctamente.');
        }

        return redirect()->back()->with('error', 'Producto no encontrado.');
    }

    public function quitarProducto(Request $request)
    {
        $satclaveId = $request->input('satclave_id');
        $productoId = $request->input('producto_id');

        // Aquí la lógica para quitar el producto de la clave SAT
        // Ejemplo: suponiendo que es una relación en la tabla productos, que guarda satclave_id

        $producto = Product::find($productoId);

        if ($producto && $producto->satclave_id == $satclaveId) {
            // Puedes desvincularlo o poner satclave_id a null según lógica
            $producto->satclave_id = null;
            $producto->save();

            return redirect()->back()->with('success', 'Producto quitado correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo quitar el producto.');
    }

    public function destroy_product($id)
    {
        $producto = Product::findOrFail($id);

        $producto->satclave_id = null; // desvincular la clave SAT
        $producto->save();

        return redirect()->back()->with('success', 'Producto desvinculado correctamente.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($clave)
    {
        $clave = SatClave::find($clave);

        $existeClave = Product::where('satclave_id', $clave->id)->exists();

        if ($existeClave) {
            // No se puede eliminar porque hay inventario relacionado
            return Redirect::route('satclaves.index')
                ->with('error', '¡No se puede eliminar la Clave porque tiene asigando un producto!');
        }

        // Eliminar el producto
        $clave->delete();

        return Redirect::route('satclaves.index')
            ->with('success', '¡La Clave ha sido eliminada exitosamente!');
    }
}
