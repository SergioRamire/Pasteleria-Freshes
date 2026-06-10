@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
                <div id="alert-success" class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-text">{{ session('success') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif
            @if (session()->has('error'))
                <div  id="alert-error" class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-text">{{ session('error') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            <!-- ENCABEZADO -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <h3 class="mb-3">Pedidos completos
                    <i class="fas fa-info-circle text-primary"
                    data-toggle="tooltip"
                    data-placement="right"
                    title="Visualiza los pedidos que ya han sido entregados en sucursal y finalizados, facilitando el control y la trazabilidad de las ventas realizadas.">
                    </i>
                </h3>
            </div>
        </div>

        <!-- FILTROS -->
        <div class="col-lg-12 mb-4">
            <form action="{{ route('order.completeOrders') }}" method="get">
                <div class="form-row align-items-end">

                    <!-- FILAS POR PÁGINA -->
                    <div class="form-group col-md-2">
                        <label for="row" class="form-label">
                            <i class="ri-align-justify"></i> Fila
                        </label>
                        <select class="form-control" name="row" id="row" onchange="this.form.submit()">
                            <option value="20" @selected(request('row') == '20')>20</option>
                            <option value="25" @selected(request('row') == '25')>25</option>
                            <option value="50" @selected(request('row') == '50')>50</option>
                            <option value="100" @selected(request('row') == '100')>100</option>
                        </select>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="enviar_id" class="form-label fw-semibold">
                        <i class="ri-truck-line me-1"></i> Entrega
                        </label>
                        <select class="form-control" name="enviar_id" id="enviar_id" onchange="this.form.submit()">
                            <option value="" @selected(request('enviar_id') == '')>Todos</option>
                            <option value="0" @selected(request('enviar_id') == '0')>Entrega en Sucursal</option>
                            <option value="1" @selected(request('enviar_id') == '1')>Envio a Domicilio</option>
                        </select>
                    </div>

                    @php
                        $hoy =now()->timezone('America/Mexico_City')->toDateString();
                    @endphp
                    <!-- FECHA -->
                    <div class="form-group col-md-3">
                        <label for="order_date"><i class="ri-calendar-line me-2"></i> Fecha del pedido</label>
                        <input type="date" name="order_date" id="order_date" max="{{ $hoy }}" class="form-control"
                               value="{{ request('order_date') }}" onchange="this.form.submit()">
                    </div>

                    <!-- BUSCADOR -->
                    <div class="form-group col-md-5">
                        <label for="search" class="form-label fw-semibold">
                        <i class="ri-search-line me-1"></i> Buscar (N° Ticket o Total)
                        </label>
                        <div class="input-group">
                            <input type="text" id="search" class="form-control" name="search" placeholder="Buscar pedido" value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                <a href="{{ route('order.completeOrders') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- TABLA DE RESULTADOS -->
        <div class="col-lg-12">
            <div class="table-responsive rounded shadow-sm border mb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th class="text-center">N°.</th>
                            <th>N° Ticket</th>
                            <th>@sortablelink('customer.name', 'Cliente')</th>
                            <th class="text-center">@sortablelink('order_date', 'Fecha Pedido')</th>
                            <th>@sortablelink('total', 'Total')</th>
                            <th class="text-center">Estado Pago</th>
                            <th class="text-center">Estado Orden</th>
                            <th class="text-center">Tipo Entrega</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="text-center">{{ (($orders->currentPage() - 1) * $orders->perPage()) + $loop->iteration }}</td>
                                <td>{{ $order->invoice_no }}</td>
                                <td>{{ ucwords(strtolower($order->customer->name)) }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') }}</td>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td class="text-center">
                                    @if (strtolower($order->payment_status) === 'due')
                                        <span class="badge bg-danger">Pendiente</span>
                                    @elseif (strtolower($order->payment_status) === 'pagado')
                                        <span class="badge bg-success">Pagado</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($order->payment_status) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success">{{ $order->order_status }}</span>
                                </td class="text-center">
                                <td class="text-center">
                                    @if($order->enviar)
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($order->customer->rul_maps) }}"
                                        target="_blank"
                                        class="text-danger"
                                        title="Ver dirección de envío en Google Maps">
                                            <i class="ri-map-pin-line" style="font-size: 1.3rem;"></i>
                                        </a>
                                    @else
                                        <i class="ri-store-line text-muted" style="font-size: 1.3rem;" title="Entrega en sucursal"></i>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-center">
                                        <a class="btn btn-info btn-sm d-inline-flex align-items-center mb-2 text-white" data-toggle="tooltip" title="Ver Detalles de la Venta" href="{{ route('order.DetailsComplete', $order->id) }}">
                                            <i class="fas fa-info-circle me-1"></i><span>Detalles</span>
                                        </a>
                                        <a class="btn btn-success btn-sm d-inline-flex align-items-center text-white" data-toggle="tooltip" title="Imprimir Venta" target="_blank" href="{{ route('order.invoiceDownload', $order->id) }}">
                                            <i class="fas fa-print me-1"></i><span>Imprimir</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No se encontraron pedidos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- PAGINACIÓN -->
                <div class="d-flex justify-content-end">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
