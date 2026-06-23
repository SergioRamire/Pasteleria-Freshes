@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">

    <!-- ENCABEZADO -->
    <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>
                    <h3 class="mb-1">
                        <i class="ri-shopping-bag-3-line"></i>
                        Detalle de Venta
                    </h3>
                    <small>Folio: {{ $order->invoice_no }}</small>
                </div>

                <div class="text-right">
                    <h2 class="mb-0">
                        ${{ number_format($order->total,2) }}
                    </h2>
                    <small>Total de la venta</small>
                </div>

            </div>
        </div>
    </div>

    <!-- CLIENTE -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <div class="row align-items-center">

                <div class="col-md-2 text-center">

                    <img
                        src="{{ $order->customer->photo ? asset('storage/customers/'.$order->customer->photo) : asset('assets/images/user/1.png') }}"
                        class="rounded-circle shadow border"
                        width="130">

                </div>

                <div class="col-md-10">

                    <h3 class="mb-1">
                        {{ ucfirst(strtolower($order->customer->name)) }}
                    </h3>

                    <p class="text-muted mb-3">
                        Información del cliente
                    </p>

                    <div class="row">

                        <div class="col-md-3">
                            <strong>Correo</strong>
                            <p>{{ $order->customer->email }}</p>
                        </div>

                        <div class="col-md-3">
                            <strong>Teléfono</strong>
                            <p>{{ $order->customer->phone }}</p>
                        </div>

                        <div class="col-md-3">
                            <strong>Fecha</strong>
                            <p>{{ $order->order_date }}</p>
                        </div>

                        <div class="col-md-3">
                            <strong>Estado</strong>

                            @if($order->payment_status == 'paid')
                                <span class="badge badge-success p-2">
                                    Pagado
                                </span>
                            @else
                                <span class="badge badge-danger p-2">
                                    Pendiente
                                </span>
                            @endif

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- RESUMEN -->
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="ri-file-list-line text-primary fa-2x"></i>
                    <h5 class="mt-2">{{ $order->invoice_no }}</h5>
                    <small>Folio</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="ri-money-dollar-circle-line text-success fa-2x"></i>
                    <h5 class="mt-2">
                        ${{ number_format($order->total,2) }}
                    </h5>
                    <small>Total</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="ri-wallet-3-line text-info fa-2x"></i>
                    <h5 class="mt-2">
                        ${{ number_format($order->pay,2) }}
                    </h5>
                    <small>Pagado</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center h-100">

                <div class="card-body">

                    @if($order->due > 0)

                        <i class="ri-error-warning-line text-danger fa-2x"></i>

                        <h5 class="mt-2">
                            ${{ number_format($order->due,2) }}
                        </h5>

                        <small>Saldo pendiente</small>

                    @else

                        <i class="ri-check-double-line text-success fa-2x"></i>

                        <h5 class="mt-2">
                            ${{ number_format(abs($order->due),2) }}
                        </h5>

                        <small>Cambio</small>

                    @endif

                </div>

            </div>
        </div>

    </div>


    <!-- ENTREGA -->
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="ri-truck-line"></i>
                Información de entrega
            </h5>
        </div>

        <div class="card-body">

            @if($order->enviar == 1)

                <div class="alert alert-info mb-0">

                    <div class="d-flex justify-content-between">

                        <div>
                            <strong>Entrega a domicilio</strong>

                            <p class="mb-0 mt-2">

                                {{ $order->customer->calle }}
                                {{ $order->customer->num_exterior }}

                                @if($order->customer->num_interior)
                                    Int. {{ $order->customer->num_interior }}
                                @endif

                                Col.
                                {{ $order->customer->colonia }},

                                {{ $order->customer->municipio }},
                                {{ $order->customer->estado }}

                            </p>

                        </div>

                        <div>

                            <a
                                href="{{ $order->customer->rul_maps }}"
                                target="_blank"
                                class="btn btn-danger">

                                <i class="ri-map-pin-line"></i>
                                Ver mapa

                            </a>

                        </div>

                    </div>

                </div>

            @else

                <div class="alert alert-success mb-0">
                    <i class="ri-store-2-line"></i>
                    El pedido será entregado directamente en sucursal.
                </div>

            @endif

        </div>

    </div>

    <!-- PRODUCTOS -->
    <div class="card shadow-sm border-0">

        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="ri-shopping-cart-line"></i>
                Productos vendidos
            </h5>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="bg-primary text-white">

                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Imagen</th>
                            <th>Producto</th>
                            <th class="text-center">Código</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Unidad</th>
                            <th class="text-center">Precio Unitario</th>
                            <th class="text-center">Total</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach($orderDetails as $item)

                        <tr>

                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>

                            <td class="text-center">

                                <img
                                    src="{{ $item->product->product_image ? asset('storage/products/'.$item->product->product_image) : asset('assets/images/product/default.webp') }}"
                                    class="rounded border"
                                    width="70">

                            </td>

                            <td>
                                <strong>
                                    {{ $item->product->product_name }}
                                </strong>
                            </td>

                            <td class="text-center">
                                {{ $item->product->product_code }}
                            </td>

                            <td class="text-center">
                                <span class="badge badge-primary">
                                    {{ $item->quantity }}
                                </span>
                            </td>

                            <td class="text-center">
                                {{ $item->equivalencia ?? 'Sin unidad' }}
                            </td>

                            <td class="text-center">
                                ${{ number_format($item->unitcost,2) }}
                            </td>

                            <td class="text-center font-weight-bold">
                                ${{ number_format($item->total,2) }}
                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <div class="mt-4 d-flex justify-content-end">
        <a href="{{ route('ventas.editar',$order->id) }}" class="btn btn-warning">
            <i class="ri-edit-line"></i>
            Editar Venta
        </a>
        <a href="{{ route('order.pendingDue') }}"
           class="btn btn-danger">

            <i class="ri-arrow-left-line"></i>
            Regresar

        </a>
    </div>
    <div class="mt-4">

    </div>
</div>

@include('components.preview-img-form')
@endsection