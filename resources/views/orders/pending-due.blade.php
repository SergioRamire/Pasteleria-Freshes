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
                <h3 class="mb-3">Pedidos no pagados
                    <i class="fas fa-info-circle text-primary"
                    data-toggle="tooltip"
                    data-placement="right"
                    title="Consulta los pedidos que aún no han sido liquidados, permitiendo dar seguimiento y gestionar los cobros pendientes de los clientes.">
                    </i>
                </h3>
            </div>
        </div>

        <div class="col-lg-12">
            <form action="{{ route('order.pendingDue') }}" method="get" id="filterForm" class="row g-3 align-items-end mb-4">

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
                            <a href="{{ route('order.pendingDue') }}" class="input-group-text bg-danger text-white" title="Limpiar búsqueda">
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
                                <th>N°.</th>
                                <th>N° Ticket</th>
                                <th>@sortablelink('customer.name', 'Cliente')</th>
                                <th>Fecha Pedido</th>
                                <th>Estado Pago</th>
                                <th>@sortablelink('pay', 'Pagado')</th>
                                <th>@sortablelink('due', 'Debe')</th>
                                <th>Entrega</th>
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
                                        <span class="badge {{ $order->payment_status == 'pagado' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($order->pay, 2) }}</td>
                                    <td>${{ number_format($order->due, 2) }}</td>
                                    <td>
                                        @if($order->enviar)
                                            <a href="{{$order->customer->rul_maps}}"
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
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            <a class="btn btn-info d-flex align-items-center gap-1"
                                            data-toggle="tooltip" title="Ver detalles del pedido"
                                            href="{{ route('order.DetailsDue', $order->id) }}">
                                                <i class="ri-file-text-line"></i> Detalles
                                            </a>

                                            <a class="btn btn-success d-flex align-items-center gap-1"
                                            data-toggle="tooltip" title="Imprimir" target="_blank"
                                            href="{{ route('order.invoiceDownload', $order->id) }}">
                                                <i class="ri-printer-line"></i> Imprimir
                                            </a>

                                            <button type="button"
                                                    class="btn btn-primary-dark d-flex align-items-center gap-1"
                                                    data-toggle="modal" data-target=".bd-example-modal-lg"
                                                    data-placement="top" title="Pagar Ticket"
                                                    id="{{ $order->id }}"
                                                    onclick="payDue(this.id)">
                                                <i class="ri-money-dollar-circle-line"></i> Pagar
                                            </button>
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
                </div>
            </div>
            {{ $orders->links() }}
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('order.updateDue') }}" method="post" id="formPago">
                @csrf
                <input type="hidden" name="order_id" id="order_id">

                <div class="modal-body">
                    <!-- @php
                        $deuda = isset($order) ? $order->due : 0;
                    @endphp -->
                   <h4 class="modal-title text-center mx-auto mb-4">
                        Pagar deuda: $<span id="deuda_titulo">0.00</span>
                    </h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pago_efectivo"><i class="ri-money-dollar-box-line me-1"></i> Pago en efectivo</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('pago_efectivo') is-invalid @enderror"
                                    id="pago_efectivo" name="pago_efectivo" value="{{ old('pago_efectivo', 0) }}">
                                @error('pago_efectivo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pago_tarjeta"><i class="ri-bank-card-line me-1"></i> Pago con tarjeta</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('pago_tarjeta') is-invalid @enderror"
                                    id="pago_tarjeta" name="pago_tarjeta" value="{{ old('pago_tarjeta', 0) }}">
                                @error('pago_tarjeta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12 mt-3" id="div_num_ticket" style="display:none;">
                            <label for="num_ticket"><i class="ri-ticket-2-line me-1"></i> Número de operacion del ticket <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="num_ticket" name="num_ticket" maxlength="15" placeholder="Ejemplo: 123456789" oninput="this.value = this.value.replace(/\D/g, '')">
                        </div>

                        <div class="col-md-12 mt-3" id="div_num_tarjeta" style="display:none;">
                            <label for="num_tarjeta"><i class="ri-bank-card-line me-1"></i> Últimos 4 dígitos de la tarjeta <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="num_tarjeta" name="num_tarjeta" maxlength="4" placeholder="Ejemplo: 1234" oninput="this.value = this.value.replace(/\D/g, '')">
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="alert alert-info d-flex align-items-center gap-2">
                                <i class="ri-wallet-3-line" style="font-size: 1.5rem;"></i>
                                <div>
                                    <strong>Deuda pendiente:</strong> $<span id="deuda_text">0.00</span>
                                    <input type="hidden" id="deuda_value" name="deuda_value" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="alert alert-success d-flex align-items-center gap-2">
                                <i class="ri-exchange-dollar-line" style="font-size: 1.5rem;"></i>
                                <div>
                                    <strong>Cambio:</strong> $<span id="cambio_text">0.00</span>
                                    <input type="hidden" id="cambio_value" name="cambio_value" value="0">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary d-flex align-items-center gap-1" data-dismiss="modal">
                        <i class="ri-close-line"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                        <i class="ri-check-double-line"></i> Pagar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function payDue(id) {
    $.ajax({
        type: 'GET',
        url: '/order/due/' + id,
        dataType: 'json',
        success: function(data) {
            $('#order_id').val(data.id);
            $('#deuda_text').text(parseFloat(data.due).toFixed(2));
            $('#deuda_value').val(parseFloat(data.due));
            $('#deuda_titulo').text(parseFloat(data.due).toFixed(2)); // ✅ Agrega esta línea

            $('#pago_efectivo').val(0);
            $('#pago_tarjeta').val(0);
            $('#num_ticket').val('');
            $('#num_tarjeta').val('');

            // Ocultar campos tarjeta
            document.getElementById('div_num_ticket').style.display = 'none';
            document.getElementById('num_ticket').required = false;

            document.getElementById('div_num_tarjeta').style.display = 'none';
            document.getElementById('num_tarjeta').required = false;

            // Reset cambio
            document.getElementById('cambio_text').textContent = '0.00';
            document.getElementById('cambio_value').value = '0.00';
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formPago');
    const pagoEfectivoInput = document.getElementById('pago_efectivo');
    const pagoTarjetaInput = document.getElementById('pago_tarjeta');
    const divNumTicket = document.getElementById('div_num_ticket');
    const numTicketInput = document.getElementById('num_ticket');
    const divNumTarjeta = document.getElementById('div_num_tarjeta');
    const numTarjetaInput = document.getElementById('num_tarjeta');
    const deudaValueInput = document.getElementById('deuda_value');
    const deudaText = document.getElementById('deuda_text');
    const cambioText = document.getElementById('cambio_text');
    const cambioValueInput = document.getElementById('cambio_value');

    function toggleTarjetaFields() {
        const valorTarjeta = parseFloat(pagoTarjetaInput.value) || 0;

        if (valorTarjeta > 1) {
            divNumTicket.style.display = 'block';
            numTicketInput.required = true;

            divNumTarjeta.style.display = 'block';
            numTarjetaInput.required = true;
        } else {
            divNumTicket.style.display = 'none';
            numTicketInput.required = false;
            numTicketInput.value = '';

            divNumTarjeta.style.display = 'none';
            numTarjetaInput.required = false;
            numTarjetaInput.value = '';
        }
    }

    function calcularCambio() {
        const efectivo = parseFloat(pagoEfectivoInput.value) || 0;
        const tarjeta = parseFloat(pagoTarjetaInput.value) || 0;
        const deuda = parseFloat(deudaValueInput.value) || 0;

        let cambio = (efectivo + tarjeta) - deuda;
        if (cambio < 0) cambio = 0;

        cambioText.textContent = cambio.toFixed(2);
        cambioValueInput.value = cambio.toFixed(2);
    }

    function actualizarDeudaRestante() {
        const efectivo = parseFloat(pagoEfectivoInput.value) || 0;
        const tarjeta = parseFloat(pagoTarjetaInput.value) || 0;
        const deuda = parseFloat(deudaValueInput.value) || 0;

        let restante = deuda - (efectivo + tarjeta);
        if (restante < 0) restante = 0;

        deudaText.textContent = restante.toFixed(2);
    }

    pagoTarjetaInput.addEventListener('input', () => {
        toggleTarjetaFields();
        calcularCambio();
        actualizarDeudaRestante();
    });

    pagoEfectivoInput.addEventListener('input', () => {
        calcularCambio();
        actualizarDeudaRestante();
    });

    // Inicialización
    toggleTarjetaFields();
    calcularCambio();
    actualizarDeudaRestante();

    form.addEventListener('submit', function (e) {
        const efectivo = parseFloat(pagoEfectivoInput.value) || 0;
        const tarjeta = parseFloat(pagoTarjetaInput.value) || 0;
        const deuda = parseFloat(deudaValueInput.value) || 0;
        const totalPagado = efectivo + tarjeta;

        if (tarjeta > 1 && numTicketInput.value.trim() === '') {
            alert('Por favor, ingrese el número de ticket de la terminal para el pago con tarjeta.');
            numTicketInput.focus();
            e.preventDefault();
            return;
        }

        if (tarjeta > 1 && (numTarjetaInput.value.trim() === '' || !/^\d{4}$/.test(numTarjetaInput.value))) {
            alert('Por favor, ingrese los últimos 4 dígitos de la tarjeta (exactamente 4 números).');
            numTarjetaInput.focus();
            e.preventDefault();
            return;
        }

        if (totalPagado < deuda) {
            alert(`El total pagado ($${totalPagado.toFixed(2)}) no cubre la deuda de $${deuda.toFixed(2)}.`);
            e.preventDefault();
            return;
        }

        if (!confirm(`¿Deseas confirmar el pago total de $${totalPagado.toFixed(2)}?`)) {
            e.preventDefault();
        }
    });
});
</script>

@endsection
