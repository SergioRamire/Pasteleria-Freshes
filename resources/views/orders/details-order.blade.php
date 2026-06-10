@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Información de pedidos pendientes de entrega</h4>
                    </div>
                </div>

                <div class="card-body">
                    <!-- begin: Show Data -->
                    <div class="form-group row align-items-center">
                        <div class="col-md-12">
                            <div class="profile-img-edit">
                                <div class="crm-profile-img-edit">
                                    <img class="crm-profile-pic rounded-circle avatar-100" id="image-preview" src="{{ $order->customer->photo ? asset('storage/customers/'.$order->customer->photo) : asset('assets/images/user/1.png') }}" alt="profile-pic">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="form-group col-md-4">
                            <label><i class="ri-user-line me-1"></i> Nombre del cliente</label>
                            <input type="text" class="form-control bg-white" value="{{ucfirst(strtolower( $order->customer->name)) }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label><i class="ri-mail-line me-1"></i> Correo del cliente</label>
                            <input type="text" class="form-control bg-white" value="{{ $order->customer->email }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label><i class="ri-phone-line me-1"></i> Teléfono del cliente</label>
                            <input type="text" class="form-control bg-white" value="{{ $order->customer->phone }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label><i class="ri-calendar-line me-1"></i> Fecha de la venta</label>
                            <input type="text" class="form-control bg-white" value="{{ $order->order_date }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label><i class="ri-barcode-line me-1"></i> Código de venta</label>
                            <input class="form-control bg-white" id="buying_date" value="{{ $order->invoice_no }}" readonly/>
                        </div>
                        <div class="form-group col-md-4">
                            <label><i class="ri-checkbox-circle-line me-1"></i> Estado de la venta</label>
                            <input class="form-control bg-white" id="expire_date" value="{{ ucfirst(strtolower($order->payment_status)) }}" readonly />
                        </div>
                        <div class="form-group col-md-4">
                            <label><i class="ri-exchange-dollar-line me-1""></i> Total de la venta</label>
                            <input type="text" class="form-control bg-white" value="{{ '$' . number_format(old('total', $order->total ?? 0), 2) }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label><i class="ri-money-dollar-box-line me-1"></i> Monto del pago en efectivo</label>
                            <input type="text" class="form-control bg-white" value="{{ '$' . number_format(old('pay', $order->pay ?? 0), 2) }}" readonly>
                        </div>
                        @if($order->due <= 0)
                            <div class="form-group col-md-4">
                                <label><i class="ri-coins-line me-1"></i> Cambio</label>
                                <input type="text" class="form-control bg-white" value="{{ '$' . number_format(abs(old('due', $order->due ?? 0)), 2) }}" readonly>
                            </div>
                        @else
                            <div class="form-group col-md-4">
                                <label><i class="ri-error-warning-line me-1"></i> Debe</label>
                                <input type="text" class="form-control bg-white" value="{{ '$' . number_format(old('due', $order->due ?? 0), 2) }}" readonly>
                            </div>
                        @endif
                        <div class="card-body row">
                            <div class="col-md-4 mb-3">
                                <label class="mb-1 text-muted"><i class="ri-wallet-line me-1"></i> Método de pago</label>
                                <div class="d-flex align-items-center">
                                    @if($order->metodo_pago == 'Efectivo')
                                        <i class="fas fa-money-bill-wave text-success mr-2 fa-lg"></i>
                                    @elseif($order->metodo_pago == 'Tarjeta')
                                        <i class="fas fa-credit-card text-primary mr-2 fa-lg"></i>
                                    @elseif($order->metodo_pago == 'Efectivo/Tarjeta')
                                        <i class="fas fa-exchange-alt text-warning mr-2 fa-lg"></i>
                                    @else
                                        <i class="fas fa-question-circle text-secondary mr-2 fa-lg"></i>
                                    @endif
                                    <input class="form-control bg-white" value="{{ $order->metodo_pago }}" readonly />
                                </div>
                            </div>

                            @if($order->metodo_pago == 'Tarjeta' || $order->metodo_pago == 'Efectivo/Tarjeta')
                                <div class="col-md-4 mb-3">
                                    <label class="mb-1 text-muted"><i class="ri-ticket-line me-1"></i> Número de ticket</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-receipt text-dark mr-2 fa-lg"></i>
                                        <input class="form-control bg-white" value="{{ $order->num_ticket }}" readonly />
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="mb-1 text-muted"><i class="ri-vip-crown-2-line me-1"></i> Últimos 4 dígitos de la tarjeta</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-credit-card text-dark mr-2 fa-lg"></i>
                                        <input class="form-control bg-white" value="{{ $order->num_tarjeta }}" readonly />
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($order->enviar == 1)
                            <hr class="my-4">

                            <div class="form-group col-md-12">
                                <label>
                                    <i class="fas fa-truck text-primary me-2 fa-lg"></i>
                                    Envío a domicilio
                                    <a href="{{$order->customer->rul_maps }}"  target="_blank" class="ms-2 text-danger" title="Ver en Google Maps">
                                    <i class="ri-map-pin-line" style="font-size: 1.2rem;"></i>
                                    </a>
                                </label>

                                <textarea class="form-control bg-white" rows="2" readonly> El pedido será enviado a: {{ $order->customer->calle }} {{ $order->customer->num_exterior }}{{ $order->customer->num_interior ? ' Int. ' . $order->customer->num_interior . ',' : '' }} Col. {{ $order->customer->colonia }}, {{ $order->customer->municipio }}, {{ $order->customer->estado }}.
                                </textarea>
                            </div>

                        @else
                            <hr class="my-4">

                            <div class="form-group col-md-12">
                                <label><i class="fas fa-store me-2 fa-lg text-success"></i> Entrega en sucursal</label>
                                <input type="text" class="form-control bg-white" value="El pedido entregado directamente en la sucursal." readonly>
                            </div>
                        @endif

                    </div>
                    <!-- end: Show Data -->

                    @if ($order->order_status == 'pendiente')
                        <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex justify-content-between align-items-center list-action">
                                <!-- Botones del lado izquierdo -->
                                <div class="d-flex">
                                    <form action="{{ route('order.updateStatus') }}" method="POST" style="margin-bottom: 5px">
                                        @method('put')
                                        @csrf
                                        @if($order->due <= 0)
                                            <input type="hidden" name="id" value="{{ $order->id }}">
                                            <button type="submit" class="btn btn-success mr-2 border-none" data-toggle="tooltip" data-placement="top" title="Orden completada">
                                                <i class="fas fa-check-circle me-1"></i> Orden completa
                                            </button>
                                        @endif
                                    </form>

                                    @if($order->enviar == 1)
                                        <a href="{{ route('order.invoiceDownload', $order->id) }}"
                                        class="btn btn-primary mr-2 border-none"
                                        style="margin-bottom: 5px"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Generar documento de entrega"
                                        target="_blank">
                                            <i class="fas fa-truck me-1"></i> Documento de Entrega
                                        </a>
                                    @endif
                                </div>

                                <!-- Botón del lado derecho -->
                                <div>
                                    <a href="{{ route('order.pendingOrders') }}" class="btn btn-danger" style="min-width: 120px;" data-toggle="tooltip" data-placement="top" title="Regresar">
                                        <i class="fas fa-arrow-left me-1"></i> Regresar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>


        <!-- end: Show Data -->
        <div class="col-lg-12">
            <div class="table-responsive rounded mb-3">
                <table class="table mb-0">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th class="text-center">No.</th>
                            <th class="text-center">Código SAT</th>
                            <th class="text-center">Foto</th>
                            <th>Nombre del Producto</th>
                            <th class="text-center">Código</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Unidad</th>
                            <th class="text-center">Precio U.</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @foreach ($orderDetails as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $item->clave ?? 'Sin código' }}</td>
                            <td class="text-center">
                                <img
                                src="{{ $item->product->product_image ? asset('storage/products/'.$item->product->product_image) : asset('assets/images/product/default.webp') }}"
                                    style="max-width: 70px; height: auto;">
                            </td>
                            <td>{{ $item->product->product_name }}</td>
                            <td class="text-center">{{ $item->product->product_code }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-center">{{ $item->equivalencia ? $item->equivalencia : 'Sin unidad' }}</td>
                            <td class="text-center">${{ number_format($item->unitcost, 2) }}</td>
                            <td class="text-center">${{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

@include('components.preview-img-form')
@endsection
