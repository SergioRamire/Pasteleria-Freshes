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
                        Detalle de Ventas
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

                            @if($order->payment_status == 'pagado')
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

                    <h4 class="mt-2 ">
                        ${{ number_format($order->pay,2) }}
                    </h4>

                    <small>
                        Total Pagado
                    </small>
                    <br>
                    <button
                        class="btn btn-info btn-sm"
                        data-toggle="modal"
                        data-target="#historialAbonosModal">

                        <i class="ri-history-line"></i>
                        Historial de Abonos

                    </button>

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

        <div class="card-body ">

            @if($order->enviar)

                <div class="border rounded p-3">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-truck-line text-primary mr-2" style="font-size: 24px;"></i>
                                <div>
                                    <h6 class="mb-0 font-weight-bold">Entrega a domicilio</h6>
                                    <small class="text-muted">
                                        Dirección registrada para este pedido
                                    </small>
                                </div>
                            </div>

                            <div class="mt-3">
                                <p class="mb-1">
                                    <i class="ri-map-pin-2-line text-danger"></i>

                                    {{ $order->customer->calle }}
                                    {{ $order->customer->num_exterior }}

                                    @if($order->customer->num_interior)
                                        Int. {{ $order->customer->num_interior }}
                                    @endif
                                </p>

                                <p class="mb-1">
                                    <strong>Colonia:</strong>
                                    {{ $order->customer->colonia }}
                                </p>

                                <p class="mb-0">
                                    <strong>Municipio:</strong>
                                    {{ $order->customer->municipio }},
                                    {{ $order->customer->estado }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-3 mt-md-0">
                            <a href="{{ $order->customer->rul_maps }}"
                            target="_blank"
                            class="btn btn-danger">
                                <i class="ri-map-pin-line mr-1"></i>
                                Abrir en Google Maps
                            </a>
                        </div>

                    </div>

                </div>

            @else

                <div class="border rounded p-3 bg-light">

                    <div class="d-flex align-items-center">

                        <div class="mr-3">
                            <i class="ri-store-2-line text-success"
                            style="font-size: 32px;"></i>
                        </div>

                        <div>
                            <h6 class="mb-1 font-weight-bold">
                                Entrega en sucursal
                            </h6>

                            <p class="mb-0 text-muted">
                                El cliente recogerá este pedido directamente en la sucursal.
                            </p>
                        </div>

                    </div>

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
                            <!-- <th class="text-center">Unidad</th> -->
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
                                <!-- <td class="text-center">
                                    {{ $item->equivalencia ?? 'Sin unidad' }}
                                </td> -->
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
        <div class="mr-2 ">
            <a href="{{ route('ventas.editar',$order->id) }}" class="btn btn-warning ">
                <i class="ri-edit-line"></i> Editar Venta
            </a>
        </div>
        <div class="mr-2 ">
            <a href="{{ route('order.pendingDue') }}"class="btn btn-danger">
                <i class="ri-arrow-left-line"></i> Regresar

            </a>
        </div>
    </div>
    <div class="mt-4">

    </div>
    <div class="modal fade" id="historialAbonosModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="ri-history-line"></i>
                        Historial de Abonos
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @if($abonos->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>Código</th>
                                        <th>Método</th>
                                        <th>Monto</th>
                                        <th>Observación</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($abonos as $abono)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($abono->fecha)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">
                                                    {{ $abono->codigo }}
                                                </span>
                                            </td>
                                            <td>
                                                @switch($abono->metodo_pago)
                                                    @case('efectivo')
                                                        💵 Efectivo
                                                    @break

                                                    @case('tarjeta')
                                                        💳 Tarjeta
                                                    @break

                                                    @case('transferencia')
                                                        🏦 Transferencia
                                                    @break

                                                    @default
                                                        {{ ucfirst($abono->metodo_pago) }}

                                                @endswitch
                                            </td>
                                            <td class="text-success font-weight-bold">
                                                ${{ number_format($abono->monto,2) }}
                                            </td>
                                            <td>
                                                {{ $abono->observacion ?? 'Sin observaciones' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="ri-information-line"></i>No existen abonos registrados.
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
    </div>
    </div>
</div>

@include('components.preview-img-form')
@endsection