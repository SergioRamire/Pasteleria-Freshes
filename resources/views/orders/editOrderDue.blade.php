@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @if(session()->has('success'))
                <div id="alert-success" class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-text">{{ session('success') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif
            @if(session()->has('error'))
                <div id="alert-error" class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-text">{{ session('error') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0">
                    <i class="ri-edit-line text-warning me-1"></i> Editando Venta
                    <i class="fas fa-info-circle text-primary ms-1"
                       data-toggle="tooltip" data-placement="right"
                       title="Modifica los productos de esta venta pendiente. Los cambios se reflejarán en el inventario y en el total de la orden.">
                    </i>
                </h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">
                        Folio: <strong>{{ $order->invoice_no }}</strong> &mdash;
                        Cliente: <strong>{{ ucwords(strtolower($order->customer->name)) }}</strong>
                    </span>
                    <a href="{{ route('order.DetailsDue', $order->id) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Regresar
                    </a>
                </div>
            </div>
        </div>

        {{-- IZQUIERDA: Catálogo --}}
        <div class="col-lg-6 col-md-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">

                    <form action="{{ route('ventas.editar.index') }}" method="get">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">

                            <div class="col-md-3 mb-3">
                                <label for="row" class="form-label">
                                    <i class="ri-align-justify me-1"></i> Fila
                                </label>
                                <select class="form-control" name="row">
                                    <option value="10"  @if(request('row')=='10')  selected @endif>10</option>
                                    <option value="25"  @if(request('row')=='25')  selected @endif>25</option>
                                    <option value="50"  @if(request('row')=='50')  selected @endif>50</option>
                                    <option value="100" @if(request('row')=='100') selected @endif>100</option>
                                </select>
                            </div>

                            <div class="form-group col-md-9">
                                <label for="search"><i class="ri-search-line"></i> Buscar</label>
                                <div class="input-group">
                                    <input type="text" id="search" class="form-control" name="search"
                                           placeholder="Buscar producto" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                        <a href="{{ route('ventas.editar.index') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>

                    {{-- Tabla de productos --}}
                    <div class="table-responsive rounded mb-3 border-none">
                        <table class="table table-sm mb-0 small">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth-data text-center">
                                    <th>Código</th>
                                    <th>Foto</th>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td>{{ $product->product_code }}</td>
                                    <td>
                                        <img class="avatar-40 rounded"
                                             src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/images/product/default.webp') }}">
                                    </td>
                                    <td>{{ $product->product_name }}</td>
                                    <td>${{ number_format($product->selling_price, 2) }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        <form action="{{ route('ventas.editar.addCart') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="inventario_id" value="{{ $product->inventario_id }}">
                                            <input type="hidden" name="id"            value="{{ $product->id }}">
                                            <input type="hidden" name="name"          value="{{ $product->product_name }}">
                                            <input type="hidden" name="price"         value="{{ $product->selling_price }}">
                                            <input type="hidden" name="dealer_price"  value="{{ $product->dealer_price ?? $product->selling_price }}">
                                            <button type="submit"
                                                    class="btn btn-primary btn-sm"
                                                    title="Agregar al carrito"
                                                    {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                                <i class="far fa-plus mr-0"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Producto no encontrado o sin stock.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive rounded mb-3 border-none">
                        {{ $products->links() }}
                    </div>

                </div>
            </div>
        </div>

        {{-- DERECHA: Carrito edición --}}
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">

                    <h5 class="card-title mb-4">
                        Carrito de Edición <i class="fa-solid fa-cart-shopping text-warning me-2"></i>
                    </h5>

                    @php
                        $cartEditar  = Cart::instance('editar_venta');
                        $totalEdit   = floatval(str_replace(',', '', $cartEditar->subtotal()));
                        $subtotalEdit = $totalEdit / 1.16;
                        $ivaEdit     = $subtotalEdit * 0.16;
                    @endphp

                    <div class="table-responsive1 rounded shadow-sm border mb-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Nombre</th>
                                    <th style="min-width:120px;">Cantidad</th>
                                    <th>Precio U.</th>
                                    <th>Subtotal</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productItem as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <form action="{{ route('ventas.editar.updateCart', $item->rowId) }}"
                                              method="POST"
                                              class="d-flex justify-content-center align-items-center">
                                            @csrf
                                            <input type="hidden" name="inventario_id" value="{{ $item->options->inventario_id }}">
                                            <input type="number"
                                                   class="form-control form-control-sm text-center"
                                                   name="qty" min="1" step="1" required
                                                   value="{{ $item->qty }}"
                                                   style="max-width: 70px;"
                                                   oninput="this.value = this.value.replace(/\D+/g, '')">
                                            <button type="submit" class="btn btn-success btn-sm ms-2" title="Actualizar cantidad">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">${{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">${{ number_format($item->subtotal, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('ventas.editar.deleteCart', $item->rowId) }}"
                                           class="btn btn-danger btn-sm"
                                           title="Borrar producto"
                                           onclick="return confirm('¿Eliminar este producto del carrito?')">
                                            <i class="fa-solid fa-trash mr-0"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Carrito Vacío.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Totales --}}
                    <div class="row text-center mt-4">
                        <div class="col-6 col-sm-3 mb-2">
                            <div class="bg-warning text-white rounded py-2">
                                <strong>Cantidad</strong><br>
                                {{ $cartEditar->count() }}
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 mb-2">
                            <div class="bg-secondary text-white rounded py-2">
                                <strong>Subtotal</strong><br>
                                ${{ number_format($subtotalEdit, 2) }}
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 mb-2">
                            <div class="bg-info text-white rounded py-2">
                                <strong>IVA 16%</strong><br>
                                ${{ number_format($ivaEdit, 2) }}
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 mb-2">
                            <div class="bg-success text-white rounded py-2">
                                <strong>Total</strong><br>
                                ${{ number_format($totalEdit, 2) }}
                            </div>
                        </div>
                    </div>

                    {{-- Info de la orden --}}
                    <div class="row text-center mt-2">
                        <div class="col-6">
                            <div class="border rounded py-2">
                                <small class="text-muted d-block">Ya pagado</small>
                                <strong class="text-success">${{ number_format($order->pay, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded py-2">
                                <small class="text-muted d-block">Deuda actual</small>
                                <strong class="text-danger">${{ number_format($order->due, 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Botón guardar --}}
                    @if($cartEditar->count() > 0)
                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <form action="{{ route('ventas.editar.guardar') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="btn btn-warning"
                                        onclick="return confirm('¿Guardar los cambios en la venta?')">
                                    <i class="ri-save-line me-1"></i> Guardar Cambios
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Vaciar carrito edición --}}
                    <div class="d-flex justify-content-end mt-4">
                        <form id="vaciar-carrito-form"
                              action="{{ route('ventas.editar.deleteCart', 'all') }}"
                              method="POST">
                            @csrf
                            {{-- Botón solo visual, vaciar se maneja diferente --}}
                        </form>
                        <a href="{{ route('order.DetailsDue', $order->id) }}"
                           class="btn btn-danger"
                           onclick="return confirm('¿Cancelar edición? Los cambios no se guardarán.')">
                            <i class="ri-close-line me-1"></i> Cancelar Edición
                        </a>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

<style>
  .table-responsive1 {
    max-height: 600px;
    overflow-x: auto;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    border: 1px solid #e6dfde;
    border-radius: 0.25rem;
    padding: 0.1rem;
    background-color: #fff;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        ['alert-success', 'alert-error'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.style.transition = 'opacity 0.5s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }
        });
    }, 5000);
});
</script>