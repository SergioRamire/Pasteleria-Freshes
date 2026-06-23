<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;

use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Caja;
use App\Models\Branche;
use App\Models\OrderDetails;
use App\Models\Inventario;

class EditOrderController extends Controller
{
    
    public function editarIndex(Request $request)
    {
        $user = auth()->user();

        $query = Inventario::query()
            ->join('products', 'products.id', '=', 'inventarios.product_id')
            ->select(
                'inventarios.id as inventario_id',
                'inventarios.stock',
                'products.id as id',
                'products.product_name',
                'products.product_code',
                'products.product_image',
                'products.selling_price',
                'products.dealer_price',
            )
            ->where('inventarios.branche_id', $user->branche_id)
            ->where('inventarios.stock', '>', 0);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'like', "%{$search}%")
                ->orWhere('products.product_code', 'like', "%{$search}%");
            });
        }

        $order = Order::with('customer')->findOrFail(session('editing_order_id'));

        $products = $query->paginate((int) $request->input('row', 20))
                        ->appends($request->query());

        return view('orders.editOrderDue', [
            'order'       => $order,
            'products'    => $products,
            'productItem' => Cart::instance('editar_venta')->content(),
        ]);
    }

    //Metodo para cargar una venta en el carrito para su edición
    public function editarOrder($id)
    {
        Cart::instance('editar_venta')->destroy();

        $order = Order::with('customer')->findOrFail($id);

        $detalles = OrderDetails::with('product')
            ->where('order_id', $order->id)
            ->get();

        foreach ($detalles as $detalle) {
            $inventario = Inventario::where('product_id', $detalle->product_id)
                ->where('branche_id', auth()->user()->branche_id)
                ->first();

            Cart::instance('editar_venta')->add([
                'id'     => $detalle->product_id,
                'name'   => $detalle->product->product_name, // ✅ desde relación
                'qty'    => $detalle->quantity,
                'price'  => $detalle->unitcost,
                'weight' => 1,
                'options' => [
                    'inventario_id' => $inventario?->id,
                    'editing'       => true,
                ]
            ]);
        }

        session(['editing_order_id' => $order->id]);

        return redirect()->route('ventas.editar.index');
    }

public function addCartEditar(Request $request)
{
    $inventario = Inventario::findOrFail(
        $request->inventario_id
    );

    if($inventario->stock <= 0){
        return back()->with('error','Sin stock');
    }

    $item = Cart::instance('editar_venta')->search(function ($cartItem) use ($request) {
        return $cartItem->id == $request->id;
    })->first();

    if($item){

        Cart::instance('editar_venta')
            ->update($item->rowId,$item->qty + 1);

    }else{

        Cart::instance('editar_venta')->add([
            'id'=>$request->id,
            'name'=>$request->name,
            'qty'=>1,
            'price'=>$request->price,
            'options'=>[
                'inventario_id'=>$request->inventario_id
            ]
        ]);
    }

    $inventario->decrement('stock');

    return back()->with('success','Producto agregado');
}

    private function obtenerProductosSucursal()
    {
        $products = Product::whereHas('inventarios', function ($query) {
            $query->where('branche_id', auth()->user()->branche_id);
        })->get();

        return $products;
    }

    public function addCartEdit(Request $request)
{
    $inventario = Inventario::findOrFail(
        $request->inventario_id
    );

    if ($inventario->stock <= 0) {
        return back()->with(
            'error',
            'Sin stock disponible'
        );
    }

    $cartItem = Cart::instance('editar_venta')
        ->search(function ($item) use ($request) {
            return $item->id == $request->id;
        })
        ->first();

    if ($cartItem) {

        Cart::instance('editar_venta')
            ->update(
                $cartItem->rowId,
                $cartItem->qty + 1
            );

    } else {

        Cart::instance('editar_venta')->add([
            'id' => $request->id,
            'name' => $request->name,
            'qty' => 1,
            'price' => $request->price,
            'weight' => 1,
            'options' => [
                'inventario_id' => $request->inventario_id,
                'dealer_price' => $request->dealer_price
            ]
        ]);
    }

    $inventario->decrement('stock');

    return back()->with(
        'success',
        'Producto agregado'
    );
}
public function updateCartEditar(Request $request,$rowId)
{
    $item = Cart::instance('editar_venta')->get($rowId);

    $inventario = Inventario::findOrFail(
        $request->inventario_id
    );

    $diferencia = $request->qty - $item->qty;

    if($diferencia > $inventario->stock){
        return back()->with(
            'error',
            'No hay suficiente stock'
        );
    }

    $inventario->stock -= $diferencia;
    $inventario->save();

    Cart::instance('editar_venta')
        ->update($rowId,$request->qty);

    return back();
}

public function deleteCartEditar($rowId)
{
    $item = Cart::instance('editar_venta')->get($rowId);

    $inventario = Inventario::find(
        $item->options->inventario_id
    );

    if($inventario){
        $inventario->stock += $item->qty;
        $inventario->save();
    }

    Cart::instance('editar_venta')->remove($rowId);

    return back()->with(
        'success',
        'Producto eliminado'
    );
}

public function guardarEdicionVenta()
{
    DB::beginTransaction();

    try {
        $order = Order::findOrFail(session('editing_order_id'));

        OrderDetails::where('order_id', $order->id)->delete();

        $total = 0;

        foreach (Cart::instance('editar_venta')->content() as $item) {

            // ✅ Obtener inventario_id desde las opciones del carrito
            $inventario_id = $item->options->inventario_id ?? null;

            // Si no viene en opciones, buscarlo por product_id y sucursal
            if (!$inventario_id) {
                $inventario = Inventario::where('product_id', $item->id)
                    ->where('branche_id', auth()->user()->branche_id)
                    ->first();
                $inventario_id = $inventario?->id;
            }

            OrderDetails::create([
                'order_id'      => $order->id,
                'product_id'    => $item->id,
                'inventario_id' => $inventario_id, // ✅ Ahora sí se pasa
                'quantity'      => $item->qty,
                'unitcost'      => $item->price,
                'total'         => $item->subtotal,
            ]);

            $total += $item->subtotal;
        }

        $order->total     = $total;
        $order->sub_total = $total;
        $order->due       = max(0, $total - $order->pay);
        $order->save();

        Cart::instance('editar_venta')->destroy();
        session()->forget('editing_order_id');

        DB::commit();

        return redirect()
            ->route('order.pendingDue')
            ->with('success', 'Venta actualizada correctamente.');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with('error', $e->getMessage());
    }
}
}
