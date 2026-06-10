@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
                @if (session()->has('error'))
                <div id="alert-error" class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-text">{{ session('error') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>¡Ups! Hay algunos errores:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Abrir Caja</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('cajas_sucursales.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <div class=" row align-items-center">
                            <div class="form-group col-md-6">
                                <label for="branche_id"><i class="ri-building-line me-1"></i> Sucursal <span class="text-danger">*</span></label>
                                <select class="form-control" id="branche_id" name="branche_id" required>
                                    <option value="" disabled selected hidden>Selecciona Una Sucursal</option>
                                    @foreach ($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group col-md-6">
                                    <label for="user_id"><i class="ri-user-star-line me-1"></i> Empleado Responsable <span class="text-danger">*</span></label>
                                    <select class="form-control" id="user_id" name="user_id" required>
                                    <option value="" disabled selected hidden>Primero Selecciona Una Sucursal</option>
                                    <!-- Los empleados se cargarán aquí dinámicamente -->
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="form-group">
                                    <label for="numero_caja"><i class="ri-safe-2-line me-1"></i> Número de Caja <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('numero_caja') is-invalid @enderror"
                                            id="numero_caja" name="numero_caja" min="1"
                                            value="{{ old('numero_caja') }}" required>
                                    @error('numero_caja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- @php
                                use Carbon\Carbon;
                                 $today = now()->timezone('America/Mexico_City')->toDateString();
                                $maxBuyingDate =  Carbon::today()->addDays(2)->format('Y-m-d');
                            @endphp
                            <div class="form-group col-md-3">
                                <label for="fecha">Fecha de apertura <span class="text-danger">*</span></label>
                                <input type="date"
                                    id="fecha"
                                    name="fecha"
                                    class="form-control @error('fecha') is-invalid @enderror"
                                    value="{{ old('fecha') }}"
                                    min="{{ $today }}"
                                    max="{{ $maxBuyingDate}}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div> --}}

                            {{-- <div class="form-group col-md-3">
                                <label for="hora_apertura">Hora apertura <span class="text-danger">*</span></label>
                                 <input type="time" id="hora_apertura" min="06:00" max="22:00" name="hora_apertura" class="form-control" required>
                                @error('hora_apertura')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div> --}}

                            <div class="form-group col-md-6">
                                <div class="form-group">
                                    <label for="monto_inicial"><i class="ri-money-dollar-circle-line me-1"></i> Monto Inicial <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('monto_inicial') is-invalid @enderror"
                                            id="monto_inicial" name="monto_inicial" min="1"
                                            value="{{ old('monto_inicial') }}" required>
                                    @error('monto_inicial')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <!-- end: Input Data -->
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <!-- Botón Guardar a la izquierda -->
                            <div>
                                <a href="{{ route('cajas_sucursales.index') }}" class="btn btn-danger text-white">
                                    <i class="ri-close-line me-1"></i> Cancelar
                                </a>
                            </div>

                            <!-- Botón Cancelar a la derecha -->
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-lock-unlock-line me-1"></i> Abrir Caja
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

@include('components.preview-img-form')
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sucursalSelect = document.getElementById('branche_id');
        const empleadoSelect = document.getElementById('user_id');

        sucursalSelect.addEventListener('change', function () {
            const sucursalId = this.value;

            if (!sucursalId) return;

            // Limpia el select de empleados
            empleadoSelect.innerHTML = '<option disabled selected hidden>Cargando empleados...</option>';

            // Hace la solicitud
            fetch(`/sucursal/${sucursalId}/empleados`)
                .then(res => res.json())
                .then(empleados => {
                    empleadoSelect.innerHTML = '<option disabled selected hidden>Selecciona Una Empleado</option>';
                    empleados.forEach(empleado => {
                        const option = document.createElement('option');
                        option.value = empleado.id;
                        option.textContent = empleado.name;
                        empleadoSelect.appendChild(option);
                    });
                })
                .catch(() => {
                    empleadoSelect.innerHTML = '<option disabled selected hidden>Error al cargar empleados</option>';
                });
        });
    });
</script>
