@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">

    <!-- ENCABEZADO -->
    <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>
                    <h3 class="mb-1">
                        <i class="ri-truck-line"></i>
                        Pedido Pendiente de Entrega
                    </h3>
                    <small>
                        Folio: {{ $order->invoice_no }}
                    </small>
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

                            @if($order->order_status == 'pendiente')
                                <span class="badge badge-warning p-2">
                                    Pendiente de Entrega
                                </span>
                            @else
                                <span class="badge badge-success p-2">
                                    Entregado
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

        <div class="col-md-2">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="ri-file-list-line text-primary fa-2x"></i>
                    <h6 class="mt-2">{{ $order->invoice_no }}</h6>
                    <small>Folio</small>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="ri-money-dollar-circle-line text-success fa-2x"></i>
                    <h6 class="mt-2">
                        ${{ number_format($order->total,2) }}
                    </h6>
                    <small>Total</small>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="ri-wallet-3-line text-info fa-2x"></i>
                    <h6 class="mt-2">
                        ${{ number_format($order->pay,2) }}
                    </h6>
                    <small>Pagado</small>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="ri-bank-card-line text-warning fa-2x"></i>
                    <h6 class="mt-2">
                        {{ $order->metodo_pago }}
                    </h6>
                    <small>Método de pago</small>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">

                    @if($order->due > 0)

                        <i class="ri-error-warning-line text-danger fa-2x"></i>

                        <h6 class="mt-2">
                            ${{ number_format($order->due,2) }}
                        </h6>

                        <small>Saldo pendiente</small>

                    @else

                        <i class="ri-check-double-line text-success fa-2x"></i>

                        <h6 class="mt-2">
                            ${{ number_format(abs($order->due),2) }}
                        </h6>

                        <small>Cambio</small>

                    @endif

                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">

                    @if($order->enviar == 1)
                        <i class="ri-truck-line text-primary fa-2x"></i>
                        <h6 class="mt-2">Domicilio</h6>
                    @else
                        <i class="ri-store-2-line text-success fa-2x"></i>
                        <h6 class="mt-2">Sucursal</h6>
                    @endif

                    <small>Entrega</small>

                </div>
            </div>
        </div>

    </div>

    <!-- INFORMACIÓN DE PAGO -->
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="ri-bank-card-line"></i>
                Información de Pago
            </h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">
                    <strong>Método de pago</strong>
                    <p>{{ $order->metodo_pago }}</p>
                </div>

                @if($order->metodo_pago == 'Tarjeta' || $order->metodo_pago == 'Efectivo/Tarjeta')

                    <div class="col-md-4">
                        <strong>Número de Ticket</strong>
                        <p>{{ $order->num_ticket }}</p>
                    </div>

                    <div class="col-md-4">
                        <strong>Últimos 4 dígitos</strong>
                        <p>{{ $order->num_tarjeta }}</p>
                    </div>

                @endif

            </div>

        </div>

    </div>

    <!-- ENTREGA -->
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="ri-truck-line"></i>
                Información de Entrega
            </h5>
        </div>

        <div class="card-body">

            @if($order->enviar == 1)

                <div class="alert alert-info mb-0">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <div>

                            <strong>
                                Entrega a domicilio
                            </strong>

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
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    <i class="ri-shopping-cart-line"></i>
                    Productos del Pedido
                </h5>

                <span class="badge badge-primary p-2">
                    {{ count($orderDetails) }} Productos
                </span>

            </div>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="bg-primary text-white">

                        <tr>

                            <th class="text-center">
                                #
                            </th>

                            <th class="text-center">
                                Imagen
                            </th>

                            <th>
                                Producto
                            </th>

                            <th class="text-center">
                                Código
                            </th>

                            <th class="text-center">
                                Cantidad
                            </th>

                            <th class="text-center">
                                Unidad
                            </th>

                            <th class="text-center">
                                Precio Unitario
                            </th>

                            <th class="text-center">
                                Total
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($orderDetails as $item)

                        <tr>

                            <td class="text-center align-middle">
                                {{ $loop->iteration }}
                            </td>

                            <td class="text-center align-middle">

                                <img
                                    src="{{ $item->product->product_image ? asset('storage/products/'.$item->product->product_image) : asset('assets/images/product/default.webp') }}"
                                    class="rounded border shadow-sm"
                                    width="70">

                            </td>

                            <td class="align-middle">

                                <strong>
                                    {{ $item->product->product_name }}
                                </strong>

                            </td>

                            <td class="text-center align-middle">
                                {{ $item->product->product_code }}
                            </td>

                            <td class="text-center align-middle">

                                <span class="badge badge-primary px-3 py-2">
                                    {{ $item->quantity }}
                                </span>

                            </td>

                            <td class="text-center align-middle">

                                <span class="badge badge-light border">

                                    {{ $item->equivalencia ?? 'Sin unidad' }}

                                </span>

                            </td>

                            <td class="text-center align-middle">

                                ${{ number_format($item->unitcost,2) }}

                            </td>

                            <td class="text-center align-middle">

                                <strong class="text-success">

                                    ${{ number_format($item->total,2) }}

                                </strong>

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                    <tfoot class="bg-light">

                        <tr>

                            <th colspan="7" class="text-right">

                                Total General:

                            </th>

                            <th class="text-center text-success">

                                ${{ number_format($order->total,2) }}

                            </th>

                        </tr>

                    </tfoot>

                </table>

            </div>

        </div>

    </div>

    <!-- RESUMEN FINANCIERO -->
    <div class="row mb-4">

        <div class="col-lg-4">

            <div class="card border-left-primary shadow-sm h-100">

                <div class="card-body">

                    <h6 class="text-muted">
                        Total de la Venta
                    </h6>

                    <h3 class="text-primary mb-0">

                        ${{ number_format($order->total,2) }}

                    </h3>

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card border-left-success shadow-sm h-100">

                <div class="card-body">

                    <h6 class="text-muted">
                        Monto Pagado
                    </h6>

                    <h3 class="text-success mb-0">

                        ${{ number_format($order->pay,2) }}

                    </h3>

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card border-left-danger shadow-sm h-100">

                <div class="card-body">

                    @if($order->due > 0)

                        <h6 class="text-muted">
                            Saldo Pendiente
                        </h6>

                        <h3 class="text-danger mb-0">

                            ${{ number_format($order->due,2) }}

                        </h3>

                    @else

                        <h6 class="text-muted">
                            Cambio Entregado
                        </h6>

                        <h3 class="text-success mb-0">

                            ${{ number_format(abs($order->due),2) }}

                        </h3>

                    @endif

                </div>

            </div>

        </div>

    </div>
        <!-- ACCIONES -->
    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div class="d-flex flex-wrap">

                    @if ($order->order_status == 'pendiente')

                        <form
                            action="{{ route('order.updateStatus') }}"
                            method="POST"
                            class="mr-2 mb-2">

                            @method('PUT')
                            @csrf

                            @if($order->due <= 0)

                                <input
                                    type="hidden"
                                    name="id"
                                    value="{{ $order->id }}">

                                <button
                                    type="submit"
                                    class="btn btn-success btn-lg shadow-sm">

                                    <i class="fas fa-check-circle mr-1"></i>
                                    Marcar como Entregado

                                </button>

                            @endif

                        </form>

                    @endif

                    @if($order->enviar == 1)

                        <a
                            href="{{ route('order.invoiceDownload', $order->id) }}"
                            target="_blank"
                            class="btn btn-primary btn-lg shadow-sm mr-2 mb-2">

                            <i class="fas fa-file-download mr-1"></i>
                            Documento de Entrega

                        </a>

                    @endif

                </div>

                <div>

                    <a
                        href="{{ route('order.pendingOrders') }}"
                        class="btn btn-danger btn-lg shadow-sm">

                        <i class="fas fa-arrow-left mr-1"></i>
                        Regresar

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

@include('components.preview-img-form')
@endsection