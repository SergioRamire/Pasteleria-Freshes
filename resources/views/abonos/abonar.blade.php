@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-lg-10">

            <div class="card shadow border-0">

                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="ri-money-dollar-circle-line"></i>
                        Registrar Abono
                    </h4>
                </div>

                <div class="card-body">

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


                    @if(isset($venta))

                    @php
                        $pagado = $venta->pay ?? 0;
                        $total = $venta->total ?? 0;

                        $porcentaje = 0;

                        if($total>0){
                            $porcentaje = ($pagado/$total)*100;
                        }
                    @endphp

                    <div class="row mb-2">

                        <div class="col-md-12">

                            <div class="alert alert-light border">

                                <div class="row">
                                    <div class="col-md-6">

                                        <h5 class="mb-6">
                                            <i class="ri-user-line text-primary"></i>
                                            Información del Cliente
                                        </h5>

                                        <p class="mb-1">
                                            <strong>Nombre:</strong>
                                            {{ $venta->customer_name }}
                                        </p>

                                        <p class="mb-0">
                                            <strong>Teléfono:</strong>
                                            {{ $venta->customer_phone }}
                                        </p>

                                    </div>

                                    <div class="col-md-6">

                                        <h5 class="mb-6">
                                            <i class="ri-shopping-bag-line text-primary"></i>
                                            Información de la Venta
                                        </h5>

                                        <p class="mb-1">
                                            <strong>Folio:</strong>
                                            {{ $venta->invoice_no }}
                                        </p>

                                        <p class="mb-0">
                                            <strong>Estado:</strong>

                                            @if($venta->due>0)

                                                <span class="badge badge-warning">
                                                    Pendiente
                                                </span>

                                            @else

                                                <span class="badge badge-success">
                                                    Liquidada
                                                </span>

                                            @endif

                                        </p>
                                        <div class="mb-6">
                                            <small class="text-muted">
                                                Progreso del pago
                                            </small>

                                            <div class="progress " style="height:25px;">
                                            <div
                                                class="progress-bar bg-success"
                                                role="progressbar"
                                                style="width: {{ $porcentaje }}%;">

                                                {{ number_format($porcentaje,1) }}%

                                            </div>

                                        </div>

                                        

                                    </div>

                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row text-center mb-4">

                        <div class="col-md-4">

                            <div class="card border-success shadow-sm">

                                <div class="card-body">

                                    <h6>Total Venta</h6>

                                    <h3 class="text-success">

                                        ${{ number_format($venta->total,2) }}

                                    </h3>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="card border-info shadow-sm">

                                <div class="card-body">

                                    <h6>Total Pagado</h6>

                                    <h3 class="text-info">

                                        ${{ number_format($venta->pay,2) }}

                                    </h3>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="card border-danger shadow-sm">

                                <div class="card-body">

                                    <h6>Saldo Pendiente</h6>

                                    <h3 class="text-danger">

                                        ${{ number_format($venta->due,2) }}

                                    </h3>

                                </div>

                            </div>

                        </div>

                    </div>

                    @endif


                    <form action="{{ route('abonos.store') }}" method="POST">

                        @csrf

                        <input type="hidden" name="venta_id" value="{{ $venta->id }}">
                        <div class="form-group">
                            <label>
                                <strong> <i class="ri-money-dollar-circle-line"></i>
                                    Monto a Abonar <span class="text-danger">*</span>
                                </strong>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"> $ </span>
                                </div>
                                <input type="number" step="0.10" min="1.00" class="form-control form-control-lg" name="monto" value="{{ old('monto') }}" required>
                            </div>

                        </div>

                        <div class="form-group mt-4">
                            <label>
                                <strong>
                                    Método de Pago <span class="text-danger">*</span>
                                </strong>
                            </label>

                            <select name="metodo" id="metodo" class="form-control form-control-lg" required>
                                <option value="">-- Seleccione un método de pago --</option>
                                <option value="efectivo" {{ old('metodo')=='efectivo' ? 'selected' : '' }}>
                                    💵 Efectivo
                                </option>

                                <option value="tarjeta" {{ old('metodo')=='tarjeta' ? 'selected' : '' }}>
                                    💳 Tarjeta
                                </option>

                                <option value="transferencia" {{ old('metodo')=='transferencia' ? 'selected' : '' }}>
                                    🏦 Transferencia
                                </option>
                            </select>
                        </div>

                        <div id="datosTarjeta" class="card border-primary shadow-sm mt-3" style="display:none;">

                            <div class="card-header bg-primary text-white">
                                <i class="ri-bank-card-line"></i>
                                Información de la Tarjeta
                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label>
                                                Número de Ticket <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                id="num_ticket"
                                                name="num_ticket"
                                                value="{{ old('num_ticket') }}"
                                                placeholder="Ej. 845231">

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">

                                            <label>
                                                Últimos 4 dígitos <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                id="num_tarjeta"
                                                name="num_tarjeta"
                                                maxlength="4"
                                                value="{{ old('num_tarjeta') }}"
                                                placeholder="1234"
                                                pattern="[0-9]{4}">

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="form-group mt-4">

                            <label>

                                <strong>

                                    Observaciones

                                </strong>

                            </label>

                            <textarea
                                class="form-control"
                                rows="4"
                                name="observacion"
                                placeholder="Escribe una observación si es necesario...">{{ old('observacion') }}</textarea>

                        </div>
                        <hr>
                        <div class="text-right">
                            <a href="{{ route('order.pendingDue') }}" class="btn btn-secondary btn-lg">
                                 <i class="ri-arrow-left-line"></i> Cancelar
                            </a>

                            <button class="btn btn-success btn-lg">
                                <i class="ri-save-line"></i>
                                Registrar Abono
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {

    const metodo = document.getElementById('metodo');
    const datosTarjeta = document.getElementById('datosTarjeta');

    const num_ticket = document.getElementById('num_ticket');
    const num_tarjeta = document.getElementById('num_tarjeta');

    function cambiarMetodo() {

        if (metodo.value === 'tarjeta') {

            datosTarjeta.style.display = 'block';

            num_ticket.required = true;
            num_tarjeta.required = true;

        } else {

            datosTarjeta.style.display = 'none';

            num_ticket.required = false;
            num_tarjeta.required = false;

            num_ticket.value = '';
            num_tarjeta.value = '';
        }
    }

    metodo.addEventListener('change', cambiarMetodo);

    cambiarMetodo();

    // Solo números para los últimos 4 dígitos
    num_tarjeta.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').substring(0,4);
    });

});
window.addEventListener('pageshow', function (event) {

    // Si la página fue restaurada desde el historial (botón atrás)
    if (event.persisted) {
        location.reload();
        return;
    }

    // Compatibilidad con Chrome, Edge y otros navegadores
    if (performance.navigation.type === 2) {
        location.reload();
    }

});
</script>
