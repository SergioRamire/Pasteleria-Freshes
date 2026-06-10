@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <!-- ALERTA DE ÉXITO -->
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
                <h3 class="mb-3">Pedidos cancelados
                    <i class="fas fa-info-circle text-primary"
                    data-toggle="tooltip"
                    data-placement="right"
                    title="Visualiza los pedidos que fueron cancelados para llevar un control de las operaciones no concretadas y tomar decisiones correctivas si es necesario.">
                    </i>
                </h3>
            </div>
        </div>

        <!-- FILTROS -->
        <div class="col-lg-12">
            <form action="{{ route('order.cancelledOrders') }}" method="get" id="filterForm" class="row g-3 align-items-end mb-4">

                <div class="form-group col-md-3">
                    <label for="row" class="form-label">
                        <i class="ri-align-justify"></i> Fila
                    </label>
                    <select class="form-control" name="row" id="row">
                        <option value="20" @selected(request('row') == '20')>20</option>
                        <option value="25" @selected(request('row') == '25')>25</option>
                        <option value="50" @selected(request('row') == '50')>50</option>
                        <option value="100" @selected(request('row') == '100')>100</option>
                    </select>
                </div>

                @php
                    $hoy =now()->timezone('America/Mexico_City')->toDateString();
                @endphp
                <div class="form-group col-md-3">
                    <label for="order_date" class="form-label fw-semibold"><i class="ri-calendar-line me-2"></i> Fecha del pedido</label>
                    <input type="date" name="order_date" id="order_date" max="{{ $hoy }}" class="form-control"
                           value="{{ request('order_date') }}">
                </div>

                <div class="form-group col-md-6">
                    <label for="search" class="form-label fw-semibold">
                    <i class="ri-search-line me-1"></i> Buscar (N° Ticket o Total)
                    </label>
                    <div class="input-group">
                        <input type="text" id="search" class="form-control" name="search" placeholder="Buscar pedido" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="input-group-text bg-primary text-white" title="Buscar">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            <a href="{{ route('order.cancelledOrders') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- TABLA DE PEDIDOS -->
        <div class="col-lg-12">
            <div class="table-responsive rounded shadow-sm border mb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>N°</th>
                            <th>N° Ticket</th>
                            <th>@sortablelink('customer.name', 'Cliente')</th>
                            <th>@sortablelink('order_date', 'Fecha de pedido')</th>
                            <th>Estado del pago</th>
                            <th>@sortablelink('total', 'Total')</th>
                            <th>Estado de la orden</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ (($orders->currentPage() - 1) * $orders->perPage()) + $loop->iteration }}</td>
                                <td>{{ $order->invoice_no }}</td>
                                <td>{{ ucwords(strtolower($order->customer->name)) }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') }}</td>
                                <td class="text-center">
                                        <span class="badge bg-danger">Devolución</span>
                                </td>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ ucfirst($order->order_status) }}</span>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-info text-white" data-toggle="tooltip" href="{{ route('order.DetailsCancel', $order->id) }}" title="Ver detalles">
                                        <i class="fas fa-eye"></i> Detalles
                                    </a>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No se encontraron pedidos.</td>
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

<!-- AUTO-SUBMIT PARA SELECT Y DATE -->
<script>
    document.getElementById('row').addEventListener('change', function () {
        document.getElementById('filterForm').submit();
    });
    document.getElementById('order_date').addEventListener('change', function () {
        document.getElementById('filterForm').submit();
    });
</script>
@endsection
