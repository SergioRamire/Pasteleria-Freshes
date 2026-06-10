@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block">
                <div class="card-header d-flex justify-content-between bg-primary">
                    <div class="iq-header-title">
                        <h4 class="card-title mb-0">VENTA</h4>
                    </div>

                    <div class="invoice-btn d-flex">
                        <form action="{{ route('ventas.printInvoice') }}" method="post" target="_blank">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                            <button type="submit" class="btn btn-primary-dark mr-2">
                                <i class="las la-print"></i> Imprimir
                            </button>
                        </form>


                        <button type="button" class="btn btn-primary-dark mr-2" data-toggle="modal" data-target=".bd-example-modal-lg">Pagar</button>
                        {{-- <button type="submit" class="btn btn-primary" id="btnGuardarPago">Guardar</button> --}}

                        <a class="btn bg-danger" href="{{ route('ventas.regresar') }}">Cancelar</a>

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
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <img src="{{ asset('assets/images/logo.png') }}" class="logo-invoice img-fluid mb-3">
                            <h5 class="mb-3">Hola, {{ $customer->name }}</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive-sm">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Fecha de orden</th>
                                            <th scope="col">Estado del pedido</th>
                                            <th scope="col">Dirección de Envio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                           <td>{{ \Carbon\Carbon::now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</td>
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
                                            <th class="text-center" scope="col">Precio</th>
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
                                            <td class="text-center">{{ $item->price }}</td>
                                            <td class="text-center"><b>{{ $item->subtotal }}</b></td>
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
                            <p class="mb-0">Es un hecho establecido desde hace mucho tiempo que el lector se distraerá con el contenido legible de una página.
                                al mirar en su trazado. El objetivo de utilizar Lorem Ipsum es que tiene una distribución de letras más o menos normal,
                                en lugar de usar "Contenido aquí, contenido aquí", lo que hace que parezca un inglés legible.</p>
                        </div>
                    </div>

                    <div class="row mt-4 mb-3">
                        <div class="offset-lg-8 col-lg-4">
                            <div class="or-detail rounded">
                                <div class="p-3">
                                    <h5 class="mb-3">Detalles del pedido</h5>
                                    <div class="mb-2">
                                        @php
                                            $iva = floatval(str_replace(',', '', Cart::subtotal())) * 0.16; // Ejemplo 8% IVA
                                            $total = floatval(str_replace(',', '', Cart::subtotal())); // restar el total - IVA
                                            $subtotal = $total - $iva;
                                        @endphp
                                        <h6>Sub Total</h6>
                                        <p>${{ $subtotal}}</p>
                                    </div>
                                    <div>
                                        <h6>IVA (16%)</h6>
                                        <p>${{ $iva }}</p>
                                    </div>
                                </div>
                                <div class="ttl-amt py-2 px-3 d-flex justify-content-between align-items-center">
                                    <h6>Total</h6>
                                    <h3 class="text-primary font-weight-700">${{ Cart::subtotal() }}</h3>
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
    const payInfo = document.getElementById('payInfo');
    const errorPay = document.getElementById('errorPay');
    const form = document.getElementById('paymentForm');

    const radios = document.querySelectorAll('input[name="payment_option"]');

    function updatePaymentFields() {
        const selected = document.querySelector('input[name="payment_option"]:checked').value;

        // Ocultar campos e info
        payInfo.style.display = 'none';
        errorPay.style.display = 'none';

        // Limpiar valores SIEMPRE
        payCash.value = '';
        payCard.value = '';

        // Mostrar según la selección
        groupCash.style.display = (selected === 'cash' || selected === 'mixed') ? 'block' : 'none';
        groupCard.style.display = (selected === 'card' || selected === 'mixed') ? 'block' : 'none';
    }

    function calculateAndShowInfo() {
        const selected = document.querySelector('input[name="payment_option"]:checked').value;
        if (selected === 'pending') return;

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

    radios.forEach(r => r.addEventListener('change', updatePaymentFields));
    payCash.addEventListener('input', calculateAndShowInfo);
    payCard.addEventListener('input', calculateAndShowInfo);

    updatePaymentFields(); // inicializar

    form.addEventListener('submit', function (e) {
        const selected = document.querySelector('input[name="payment_option"]:checked').value;
        const cash = parseFloat(payCash.value) || 0;
        const card = parseFloat(payCard.value) || 0;
        const totalPaid = cash + card;

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
        }

        if (selected === 'mixed') {
            if ((!payCash.value && !payCard.value) || totalPaid <= 0) {
                e.preventDefault();
                alert('Ingresa montos válidos en efectivo y/o tarjeta.');
                return;
            }
            if (totalPaid < total) {
                e.preventDefault();
                alert('La suma de efectivo y tarjeta no cubre el total de la venta ($' + total.toFixed(2) + ').');
                return;
            }
        }

        // Mostrar mensaje de confirmación
        const confirmar = confirm('¿Estás seguro de guardar este pago?');
        if (!confirmar) {
            e.preventDefault();
        }
    });


});
</script>
