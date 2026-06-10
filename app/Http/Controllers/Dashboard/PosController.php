<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;

class PosController extends Controller
{


    public function index(Request $request)
    {
        $todayDate = Carbon::now()->toDateString();
        $row = (int) $request->input('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El número de filas por página debe estar entre 1 y 100.');
        }

        // Construimos la consulta base
        $query = Product::whereDate('expire_date', '>', $todayDate);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por proveedor
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Aplicamos ordenamiento y paginación
        $products = $query->sortable()->paginate($row)->appends($request->query());

        return view('pos.index', [
            'customers'    => Customer::orderBy('name')->get(),
            'productItem'  => Cart::content(),
            'products'     => $products,
            'categories'   => Category::all(),
            'suppliers'    => Supplier::all(),
        ]);
    }



    public function addCart(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'price' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::add([
            'id' => $validatedData['id'],
            'name' => $validatedData['name'],
            'qty' => 1,
            'price' => $validatedData['price'],
            'options' => ['size' => 'large']
        ]);

        return Redirect::back()->with('success', 'Product has been added!');
    }

    public function updateCart(Request $request, $rowId)
    {
        $rules = [
            'qty' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::update($rowId, $validatedData['qty']);

        return Redirect::back()->with('success', 'Cart has been updated!');
    }

    public function deleteCart(String $rowId)
    {
        Cart::remove($rowId);

        return Redirect::back()->with('success', 'Cart has been deleted!');
    }

    public function createInvoice(Request $request)
    {
        $rules = [
            'customer_id' => 'required'
        ];

        $validatedData = $request->validate($rules);
        $customer = Customer::where('id', $validatedData['customer_id'])->first();
        $content = Cart::content();

        return view('pos.create-invoice', [
            'customer' => $customer,
            'content' => $content
        ]);
    }

    public function printInvoice(Request $request)
    {
        $rules = [
            'customer_id' => 'required'
        ];

        $validatedData = $request->validate($rules);
        $customer = Customer::where('id', $validatedData['customer_id'])->first();
        $content = Cart::content();

        return view('pos.print-invoice', [
            'customer' => $customer,
            'content' => $content
        ]);
    }

    public function VaciarCarrito()
    {
        Cart::destroy(); // Vacía por completo el carrito
        return redirect()->route('pos.index')->with('success', 'El carrito ha sido vaciado correctamente.');
    }

public function addByBarcode(Request $request)
{
    $product = Product::where('codigo_barras', $request->barcode)
                      ->whereDate('expire_date', '>', now())
                      ->first();

    if (!$product) {
        return response()->json(['success' => false]);
    }

    Cart::add([
        'id'      => $product->id,
        'name'    => $product->product_name,
        'qty'     => 1,
        'price'   => $product->selling_price,
        'weight'  => 1,
        'options' => ['image' => $product->product_image]
    ]);

    return response()->json([
        'success' => true,
        'product_name' => $product->product_name
    ]);
}
}
