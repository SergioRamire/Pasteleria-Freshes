@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block">
                <div class="card-header d-flex justify-content-between bg-primary">
                    <div class="iq-header-title">
                        <h4 class="card-title mb-0">Cotización</h4>
                    </div>
                    @if (session()->has('error'))
                        <div  id="alert-error" class="alert text-white bg-danger" role="alert">
                            <div class="iq-alert-text">{{ session('error') }}</div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    @endif

                    <div class="invoice-btn d-flex gap-2">
                        <a href="{{ route('cotizaciones.regresar') }}"
                        onclick="return confirm('¿Confirmas la cancelación de esta cotización? Los datos asociados serán eliminados permanentemente.')"
                        class="btn btn-danger d-flex align-items-center mr-2">
                            <i class="ri-close-circle-line me-2" style="font-size: 1.2rem;"></i> Continuar
                        </a>

                        <form action="{{ route('cotizaciones.printInvoice') }}" method="post" target="_blank" class="m-0 mr-2">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                            <button type="submit" class="btn btn-success d-flex align-items-center">
                                <i class="ri-printer-line me-2" style="font-size: 1.2rem;"></i> Imprimir
                            </button>
                        </form>
                    </div>
                </div>

                        {{-- <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target=".bd-example-modal-lg">
                            Pagar
                        </button> --}}
                        {{-- <button type="submit" class="btn btn-primary" id="btnGuardarPago">Guardar</button> --}}

                        {{-- <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    @php
                                        $total = Cart::subtotal(); // o el total que tengas
                                    @endphp
                                        <div class="modal-header bg-white">
                                            <h3 class="modal-title text-center mx-auto">
                                                Detalles de la Venta<br>
                                                <small class="text-muted">Cliente: {{ $customer->name }}</small><br>
                                                <span class="fw-bold">Total a pagar: $<span id="total">{{ $total }}</span></span>
                                            </h3>

                                        </div>
                                        <form id="paymentForm" action="{{ route('ventas.storeOrder') }}" method="post">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Método de Pago</label>

                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="payment_option" id="option_pending" value="pending" checked>
                                                            <label class="form-check-label" for="option_pending">Pendiente (Cotizaciones)</label>
                                                        </div>

                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="payment_option" id="option_cash" value="cash">
                                                            <label class="form-check-label" for="option_cash">Efectivo</label>
                                                        </div>

                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="payment_option" id="option_card" value="card">
                                                            <label class="form-check-label" for="option_card">Tarjeta</label>
                                                        </div>

                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="payment_option" id="option_mixed" value="mixed">
                                                            <label class="form-check-label" for="option_mixed">Mixto (Efectivo + Tarjeta)</label>
                                                        </div>
                                                    </div>

                                                    <div id="cashAmountGroup" class="form-group" style="display:none;">
                                                        <label for="pay_cash">Monto en efectivo</label>
                                                        <input
                                                            type="text"
                                                            name="pay_cash"
                                                            id="pay_cash"
                                                            class="form-control"
                                                            placeholder="Ej. 100.00"
                                                            maxlength="10"
                                                            pattern="^\d+(\.\d{1,2})?$"
                                                            title="Solo números y hasta dos decimales."
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                                        >
                                                    </div>

                                                    <div id="cardAmountGroup" class="form-group" style="display:none;">
                                                        <label for="pay_card">Monto en tarjeta</label>
                                                        <input
                                                            type="text"
                                                            name="pay_card"
                                                            id="pay_card"
                                                            class="form-control"
                                                            placeholder="Ej. 200.00"
                                                            maxlength="10"
                                                            pattern="^\d+(\.\d{1,2})?$"
                                                            title="Solo números y hasta dos decimales."
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                                        >
                                                    </div>

                                                    <!-- Campo para número de ticket (solo visible para tarjeta o mixto) -->
                                                    <div id="ticketNumberGroup" class="form-group" style="display:none;">
                                                        <label for="num_ticket">Número de ticket de la terminal <span class="text-danger">*</span></label>
                                                        <input
                                                            type="text"
                                                            name="num_ticket"
                                                            id="num_ticket"
                                                            class="form-control"
                                                            placeholder="Ej. 123456789"
                                                            maxlength="20"
                                                            pattern="^\d+$"
                                                            title="Solo números."
                                                        >
                                                    </div>

                                                    <div class="form-group mt-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="envio" id="envio" value="1">
                                                            <label class="form-check-label" for="envio">
                                                                ¿Deseas enviar el pedido a domicilio?
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <small id="payInfo" class="form-text text-info mt-2" style="display:none;"></small>
                                                    <small id="errorPay" class="form-text text-danger" style="display:none;"></small>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                            </div>
                                        </form>

                                    </div>

                            </div>
                        </div> --}}

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <img src="{{ asset('assets/images/logo/logo-min.png') }}" class="logo-invoice img-fluid mb-3">
                            <h5 class="mb-3">Hola, {{ $customer->name }}</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive-sm">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Fecha de Orden</th>
                                            <th scope="col">Estado del Pedido</th>
                                            <th scope="col">Datos del Cliente</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ ucfirst(\Carbon\Carbon::now()->timezone('America/Mexico_City')->locale('es')->isoFormat('ddd DD \d\e MMMM \d\e YYYY')) }}</td>
                                            <td><span class="badge badge-danger">No pagado</span></td>
                                            <td>
                                                <p class="mb-0">{{ $customer->address }}<br>
                                                    Nombre: {{ $customer->name ?? '-' }}<br>
                                                    Teléfono: {{ $customer->phone }}<br>
                                                    Email: {{ $customer->email }}<br>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <h5 class="mb-3">Resumen del pedido</h5>
                            <div class="table-responsive-lg">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="text-center" scope="col">#</th>
                                            <th class="text-center" scope="col">Código</th>
                                            <th scope="col">Artículo</th>
                                            <th class="text-center" scope="col">Cantidad</th>
                                            <th class="text-center"scope="col">Unidad</th>
                                            <th class="text-center" scope="col">Precio</th>
                                            <th class="text-center" scope="col">Totales</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($content as $item)
                                        <tr>
                                            <th class="text-center" scope="row">{{ $loop->iteration }}</th>
                                            <th class="text-center" scope="row">{{ $item->options->product_code ?? 'N/A' }}</td>
                                            <td><h6 class="mb-0">{{ $item->name }}</h6></td>
                                            <td class="text-center">{{ $item->qty }}</td>
                                            <td class="text-center">{{ $item->options->unidad ?? 'N/A' }}</td>
                                            <td class="text-center">${{ number_format($item->price, 2) }}</td>
                                            <td class="text-center">${{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <b class="text-danger">Notas:</b>
                            <p class="mb-0">
                                Su compra está respaldada por nuestra política de calidad. Algunos productos cuentan con garantía, revise los términos en su empaque o solicítelos con su asesor.
                                Para cualquier duda o seguimiento, comuníquese al área de atención al cliente.
                            </p>
                        </div>
                    </div>

                    <div class="row mt-4 mb-3">
                        <div class="offset-lg-8 col-lg-4">
                            <div class="or-detail rounded border shadow-sm">
                                <div class="p-3">
                                    <h5 class="mb-3">Detalles del pedido</h5>
                                     <div class="mb-2 d-flex justify-content-between">
                                        {{-- @php
                                            $iva = floatval(str_replace(',', '', Cart::subtotal())) * 0.16; // Ejemplo 8% IVA
                                            $total = floatval(str_replace(',', '', Cart::subtotal())); // restar el total - IVA
                                            $subtotal = $total - $iva;
                                        @endphp --}}
                                        @php
                                            $cart = Cart::instance('cotizacion');
                                            $total = $cart->subtotal();
                                            $subtotal = $total / 1.16; // Asumiendo que el total ya incluye IVA
                                            $iva = $subtotal * 0.16;
                                            // $total = $subtotal; // ya es con IVA incluido
                                            // $subtotall = $subtotal - $iva; // si quieres mostrar el neto sin IVA
                                        @endphp
                                        <h6 class="mb-0">Sub Total</h6>
                                        <p class="mb-0">${{ number_format($subtotal, 2) }}</p>
                                    </div>
                                    <div class="mb-2 d-flex justify-content-between">
                                        <h6 class="mb-0">IVA (16%)</h6>
                                        <p class="mb-0">${{ number_format($iva, 2) }}</p>
                                    </div>
                                </div>
                                <div class="ttl-amt py-2 px-3 d-flex justify-content-between align-items-center border-top">
                                    <h6 class="mb-0">Total</h6>
                                    <h3 class="text-primary font-weight-bold mb-0">${{ number_format($total, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    const total = parseFloat("{{ str_replace(',', '', Cart::subtotal()) }}");
    const payCash = document.getElementById('pay_cash');
    const payCard = document.getElementById('pay_card');
    const groupCash = document.getElementById('cashAmountGroup');
    const groupCard = document.getElementById('cardAmountGroup');
    const ticketGroup = document.getElementById('ticketNumberGroup');
    const ticketInput = document.getElementById('num_ticket');
    const payInfo = document.getElementById('payInfo');
    const errorPay = document.getElementById('errorPay');
    const form = document.getElementById('paymentForm');
    const radios = document.querySelectorAll('input[name="payment_option"]');

    function updatePaymentFields() {
        const selected = document.querySelector('input[name="payment_option"]:checked').value;

        payInfo.style.display = 'none';
        errorPay.style.display = 'none';

        payCash.value = '';
        payCard.value = '';
        ticketInput.value = '';

        groupCash.style.display = (selected === 'cash' || selected === 'mixed') ? 'block' : 'none';
        groupCard.style.display = (selected === 'card' || selected === 'mixed') ? 'block' : 'none';

        // Mostrar campo ticket solo si es tarjeta o mixto
        ticketGroup.style.display = (selected === 'card' || selected === 'mixed') ? 'block' : 'none';
    }

    function calculateAndShowInfo() {
        const selected = document.querySelector('input[name="payment_option"]:checked').value;
        if (selected === 'pending') {
            payInfo.style.display = 'none';
            errorPay.style.display = 'none';
            return;
        }

        const cash = parseFloat(payCash.value) || 0;
        const card = parseFloat(payCard.value) || 0;
        const totalPaid = cash + card;
        const due = total - totalPaid;

        if (totalPaid >= total) {
            payInfo.textContent = "Cambio: $" + (totalPaid - total).toFixed(2);
            payInfo.style.display = 'block';
            errorPay.style.display = 'none';
        } else {
            payInfo.textContent = "Faltan: $" + due.toFixed(2);
            payInfo.style.display = 'block';
            errorPay.textContent = "Pago incompleto. La venta se registrará como pendiente.";
            errorPay.style.display = 'block';
        }
    }

    radios.forEach(radio => radio.addEventListener('change', () => {
        updatePaymentFields();
        calculateAndShowInfo();
    }));

    payCash.addEventListener('input', calculateAndShowInfo);
    payCard.addEventListener('input', calculateAndShowInfo);

    updatePaymentFields();

    form.addEventListener('submit', function (e) {
        const selected = document.querySelector('input[name="payment_option"]:checked').value;
        const cash = parseFloat(payCash.value) || 0;
        const card = parseFloat(payCard.value) || 0;
        const totalPaid = cash + card;
        const ticketNumber = ticketInput.value.trim();

        if (selected === 'cash') {
            if (!payCash.value || cash <= 0) {
                e.preventDefault();
                alert('Ingresa el monto en efectivo.');
                return;
            }
            if (cash < total) {
                e.preventDefault();
                alert('El monto en efectivo no cubre el total de la venta ($' + total.toFixed(2) + ').');
                return;
            }
        }

        if (selected === 'card') {
            if (!payCard.value || card <= 0) {
                e.preventDefault();
                alert('Ingresa el monto en tarjeta.');
                return;
            }
            if (card < total) {
                e.preventDefault();
                alert('El monto en tarjeta no cubre el total de la venta ($' + total.toFixed(2) + ').');
                return;
            }
            if (!ticketNumber) {
                e.preventDefault();
                alert('El número de ticket de la terminal es obligatorio para pagos con tarjeta.');
                ticketInput.focus();
                return;
            }
        }

        if (selected === 'mixed') {
            if (totalPaid <= 0) {
                e.preventDefault();
                alert('Ingresa montos válidos en efectivo y/o tarjeta.');
                return;
            }
            if (totalPaid < total) {
                e.preventDefault();
                alert('La suma de efectivo y tarjeta no cubre el total de la venta ($' + total.toFixed(2) + ').');
                return;
            }
            if (!ticketNumber) {
                e.preventDefault();
                alert('El número de ticket de la terminal es obligatorio para pagos con tarjeta.');
                ticketInput.focus();
                return;
            }
        }

        if (!confirm('¿Estás seguro de guardar este pago?')) {
            e.preventDefault();
        }
    });
});

</script>
