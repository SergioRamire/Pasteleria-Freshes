@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block">
                <div class="card-header d-flex justify-content-between bg-primary">
                    <div class="iq-header-title">
                        <h4 class="card-title mb-0">Reporte de Venta</h4>
                    </div>
                    @if (session()->has('error'))
                        <div  id="alert-error" class="alert text-white bg-danger" role="alert">
                            <div class="iq-alert-text">{{ session('error') }}</div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    @endif

                    <div class="invoice-btn d-flex">
                        {{-- <form action="{{ route('ventas.printInvoice') }}" method="post" target="_blank" class="m-0">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                            <button type="submit" class="btn btn-primary-dark mr-2">
                                <i class="las la-print"></i> Imprimir
                            </button>
                        </form> --}}

                        <a class="btn btn-danger mr-2" href="{{ route('ventas.regresar') }}"
                            onclick="return confirm('¿Confirmas la cancelación de esta venta? Los datos asociados serán eliminados permanentemente.')">
                            <i class="ri-close-line me-1"></i> Cancelar
                        </a>

                        <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target=".bd-example-modal-lg">
                            <i class="ri-money-dollar-circle-line me-1"></i> Pagar
                        </button>



                        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                            <label class="form-check-label" for="option_pending">Pendiente.</label>
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
                                                            oninput="this.value = this.value.replace(/\D/g, '')"
                                                        >
                                                    </div>

                                                    <div id="cardDigitsGroup" style="display: none;" class="form-group mt-2">
                                                        <label for="last_four_digits">Últimos 4 dígitos de la tarjeta:</label>
                                                        <input type="text" class="form-control" id="last_four_digits" name="last_four_digits" maxlength="4" pattern="\d{4}" placeholder="1234" oninput="this.value = this.value.replace(/\D/g, '')">
                                                    </div>

                                                    <div class="form-group mt-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="envio" id="envio" value="1">
                                                            <label class="form-check-label" for="envio">
                                                                ¿Deseas enviar el pedido a domicilio?
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div id="payInfo" class="alert d-flex align-items-center mt-3 fw-semibold fs-5" style="display: none;">
                                                        <i id="payInfoIcon" class="ri-money-dollar-circle-line me-2" style="font-size: 1.4rem;"></i>
                                                        <span id="payInfoText"></span>
                                                    </div>

                                                    <div id="errorPay" class="alert alert-danger d-flex align-items-center mt-2 fw-semibold fs-5" style="display: none;">
                                                        <i class="ri-error-warning-line me-2" style="font-size: 1.4rem;"></i>
                                                        <span>La venta se guardará como pendiente.</span>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary d-flex align-items-center" data-dismiss="modal">
                                                    <i class="ri-close-line me-1"></i> Cerrar
                                                </button>
                                                <button type="submit" class="btn btn-success d-flex align-items-center">
                                                    <i class="ri-money-dollar-circle-line me-1"></i> Cobrar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <img src="{{ asset('assets/images/logo/logo-min.png') }}" class="logo-invoice img-fluid mb-3">
                            <h5 class="mb-3">Hola, {{ ucwords(strtolower($customer->name)) }}</h5>
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
                                            <th scope="col">Dirección de Envio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ ucfirst(\Carbon\Carbon::now()->timezone('America/Mexico_City')->locale('es')->isoFormat('ddd DD \d\e MMMM \d\e YYYY')) }}</td>
                                            <td><span class="badge badge-danger">No pagado</span></td>
                                            <td>
                                                <p class="mb-0">{{ $customer->address }}<br>
                                                    Nombre de tienda: {{ $customer->shopname ? $customer->shopname : '-' }}<br>
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
                                            <th scope="col">Artículo</th>
                                            <th class="text-center" scope="col">Cantidad</th>
                                            <!-- <th class="text-center" scope="col">Unidad</th> -->
                                            <th class="text-center" scope="col">Precio U.</th>
                                            <th class="text-center" scope="col">Totales</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($content as $item)
                                        <tr>
                                            <th class="text-center" scope="row">{{ $loop->iteration }}</th>
                                            <td>
                                                <h6 class="mb-0">{{ $item->name }}</h6>
                                            </td>
                                            <td class="text-center">{{ $item->qty }}</td>
                                            <!-- <td class="text-center">{{ $item->options->equivalencia ?? 'N/A' }}</td> -->
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
                        <div class="offset-lg-6 col-lg-6"> {{-- Cambié offset-lg-8 a offset-lg-6 para centrar mejor el bloque --}}
                            <div class="or-detail border rounded shadow-sm">
                                <div class="p-4"> {{-- Padding interno más generoso --}}
                                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                        <h5 class="mb-0 text-dark">Detalles del pedido</h5>
                                        <i class="ri-shopping-bag-3-line text-muted"></i> {{-- Ícono opcional decorativo --}}
                                    </div>

                                    @php
                                        $cart = Cart::instance('venta');
                                        $total = $cart->subtotal();
                                        $subtotal = $total / 1.16;
                                        $iva = $subtotal * 0.16;
                                    @endphp

                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Sub Total</span>
                                        <span>${{ number_format($subtotal, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>IVA (16%)</span>
                                        <span>${{ number_format($iva, 2) }}</span>
                                    </div>
                                </div>

                                <div class="ttl-amt py-3 px-4 border-top d-flex justify-content-between align-items-center">
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
    const digitsGroup = document.getElementById('cardDigitsGroup');
    const digitsInput = document.getElementById('last_four_digits');
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
        digitsInput.value = '';

        groupCash.style.display = (selected === 'cash' || selected === 'mixed') ? 'block' : 'none';
        groupCard.style.display = (selected === 'card' || selected === 'mixed') ? 'block' : 'none';
        ticketGroup.style.display = (selected === 'card' || selected === 'mixed') ? 'block' : 'none';
        digitsGroup.style.display = (selected === 'card' || selected === 'mixed') ? 'block' : 'none';

        if (selected === 'card') {
            payCard.value = total.toFixed(2);
            payCard.readOnly = true; // Desactiva edición
        } else {
            payCard.readOnly = false; // Permite edición en mixto
        }
    }

    function calculateAndShowInfo() {
        const total = parseFloat("{{ str_replace(',', '', Cart::subtotal()) }}");
        const payCash = parseFloat(document.getElementById('pay_cash').value) || 0;
        const payCard = parseFloat(document.getElementById('pay_card').value) || 0;
        const totalPaid = payCash + payCard;

        const payInfo = document.getElementById('payInfo');
        const payInfoText = document.getElementById('payInfoText');
        const payInfoIcon = document.getElementById('payInfoIcon');
        const errorPay = document.getElementById('errorPay');

        if (totalPaid >= total) {
            const cambio = (totalPaid - total).toFixed(2);
            payInfo.style.display = 'flex';
            payInfo.classList.remove('alert-success');
            payInfo.classList.add('alert-warning'); // naranja
            payInfoText.textContent = "Cambio: $" + cambio;
            payInfoIcon.className = "ri-exchange-dollar-line me-2"; // puedes usar otro icono

            errorPay.style.display = 'none';
        } else {
            const faltan = (total - totalPaid).toFixed(2);
            payInfo.style.display = 'flex';
            payInfo.classList.remove('alert-warning');
            payInfo.classList.add('alert-success'); // verde
            payInfoText.textContent = "Faltan: $" + faltan;
            payInfoIcon.className = "ri-error-warning-line me-2";

            errorPay.style.display = 'flex';
            errorPay.querySelector('span').textContent = "Si el pago es incompleto. La venta debe ser registrada como pendiente.";
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
        const lastFour = digitsInput.value.trim();

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
            if (!lastFour || !/^\d{4}$/.test(lastFour)) {
                e.preventDefault();
                alert('Ingresa los últimos 4 dígitos de la tarjeta (exactamente 4 números).');
                digitsInput.focus();
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
            if (!lastFour || !/^\d{4}$/.test(lastFour)) {
                e.preventDefault();
                alert('Ingresa los últimos 4 dígitos de la tarjeta (exactamente 4 números).');
                digitsInput.focus();
                return;
            }
        }

        if (!confirm('¿Estás seguro de guardar este metodo de pago?')) {
            e.preventDefault();
        }
    });
});
</script>
