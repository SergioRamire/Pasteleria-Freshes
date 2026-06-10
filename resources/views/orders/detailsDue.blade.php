@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Información de pedidos pendientes de pago</h4>
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
                            <label><i class="ri-exchange-dollar-line me-1"></i> Total de la venta</label>
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

                        @if($order->enviar == 1)
                            <hr class="my-4">

                            <div class="form-group col-md-12">
                                <label>
                                    <i class="fas fa-truck text-primary me-2 fa-lg"></i>
                                    Envío a domicilio
                                    <a href="{{ $order->customer->rul_maps}}"  target="_blank" class="ms-2 text-danger" title="Ver en Google Maps">
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

                    <a href="{{ route('order.pendingDue') }}" class="btn btn-danger mr-2" data-toggle="tooltip" data-placement="top" title="Regresar">
                        <i class="fas fa-arrow-left me-1"></i> Regresar
                    </a>
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
                            <td class="text-center">{{ $loop->iteration  }}</td>
                            <td class="text-center">{{ $item->clave ?? 'Sin código' }}</td>
                            <td class="text-center">
                                <img src="{{ $item->product->product_image ? asset('storage/products/'.$item->product->product_image) : asset('assets/images/product/default.webp') }}"
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
