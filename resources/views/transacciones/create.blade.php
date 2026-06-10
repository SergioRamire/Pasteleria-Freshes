@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
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

            @if (session()->has('error'))
                <div class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-text">{{ session('error') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            {{-- @if (session()->has('success'))
                <div class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-text">{{ session('success') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif --}}

            <div class="card">
               <div class="card-header d-flex justify-content-between">
                    <div class="header-title d-flex align-items-center">
                        {{-- <i class="ri-archive-drawer-line text-primary mr-2" style="font-size: 1.5rem;"></i> --}}
                        <h4 class="card-title mb-0">Datos de la caja actual</h4>
                    </div>
                </div>


                <div class="card-body">
                    <form action="{{ route('transacciones.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <div class=" row align-items-center">
                            <div class="form-group col-md-3">
                                <label for="user_id">Empleado Responsable </label>
                                <input type="text" class="form-control @error('empleado') is-invalid @enderror"
                                            id="empleado" name="empleado"  value="{{ old('empleado', $empleado->name ?? '') }}" disabled>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="user_id">Caja </label>
                                <input type="text" class="form-control @error('caja') is-invalid @enderror"
                                            id="caja" name="caja"  value="{{ old('caja', $caja->numero_caja ?? '') }}" disabled>
                            </div>

                           <div class="form-group col-md-3">
                                <label for="caja">Saldo en caja</label>
                                <input type="text" class="form-control @error('caja') is-invalid @enderror" id="caja" name="caja"
                                    value="{{ '$' . number_format(old('caja', $caja->monto_final ?? 0), 2) }}" disabled>
                                @error('caja')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-12">
                                <hr>
                                <div class="d-flex align-items-center">
                                    <i class="ri-archive-drawer-line text-primary mr-2" style="font-size: 1.5rem;"> </i><h4 class="card-title mb-0">Crear nueva transacción</h4>
                                </div>

                            </div>

                            <div class="form-group col-md-3">
                                <label for="tipo_transaccion">Tipo de Transacción <span class="text-danger">*</span></label>
                                <select class="form-control" id="tipo_transaccion" name="tipo_transaccion" required>
                                    <option value="" selected disabled>-- Selecciona transacción --</option>
                                    <option value="devolucion" {{ old('tipo_transaccion') == 'devolucion' ? 'selected' : '' }}>Devolución</option>
                                    <option value="retiro" {{ old('tipo_transaccion') == 'retiro' ? 'selected' : '' }}>Retiro en sucursal</option>
                                    <option value="ingreso" {{ old('tipo_transaccion') == 'ingreso' ? 'selected' : '' }}>Ingreso Efectivo</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="metodo_pago">Métod de pago <span class="text-danger">*</span></label>
                                <select class="form-control" id="metodo_pago" name="metodo_pago" required>
                                    <option value="" selected disabled>-- Selecciona método de pago --</option>
                                    <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                    <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="monto">Monto Total <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('monto') is-invalid @enderror"
                                            id="monto" name="monto" min="1"
                                            value="{{ old('monto') }}" required>
                                    @error('monto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group col-md-8">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                            id="descripcion"
                                            name="descripcion"
                                            cols="25"
                                            rows="3"
                                            required>{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>
                        <!-- end: Input Data -->
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary mr-2"> <i class="ri-save-line"></i> Guardar</button>
                            <a class="btn bg-danger" href="{{ route('transacciones.index') }}"> <i class="ri-close-line"></i> Cancelar</a>
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
