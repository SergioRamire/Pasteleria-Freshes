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
                    <div id="alert-error" class="alert text-white bg-danger" role="alert">
                        <div class="iq-alert-text">{{ session('error') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif

            <div class="card">
               <div class="card-header d-flex justify-content-between">
                    <div class="header-title d-flex align-items-center">
                        {{-- <i class="ri-archive-drawer-line text-primary mr-2" style="font-size: 1.5rem;"></i> --}}
                        <h4 class="card-title mb-0">Datos de la Caja Actual</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('cajas_transacciones.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <div class=" row align-items-center">
                            <input type="hidden" name="caja_id" value="{{ $caja->id }}">

                            <div class="form-group col-md-4">
                                <label for="empleado">
                                    <i class="ri-user-3-line me-1"></i> Empleado Responsable
                                </label>
                                <input type="text" class="form-control @error('empleado') is-invalid @enderror"
                                    id="empleado" name="empleado"
                                    value="{{ old('empleado', $empleado->name ?? '') }}" disabled>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="caja">
                                    <i class="ri-bank-card-line me-1"></i> Caja
                                </label>
                                <input type="text" class="form-control @error('caja') is-invalid @enderror"
                                    id="caja" name="caja"
                                    value="{{ old('caja', $caja->numero_caja ?? '') }}" disabled>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="saldo">
                                    <i class="ri-money-dollar-circle-line me-1"></i> Saldo en caja
                                </label>
                                <input type="text" class="form-control @error('caja') is-invalid @enderror"
                                    id="saldo" name="caja"
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

                            <div class="form-group col-md-4">
                                <label for="tipo_transaccion">
                                    <i class="ri-repeat-line me-1"></i> Tipo de Transacción <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="tipo_transaccion" name="tipo_transaccion" required>
                                    <option value="" disabled selected hidden>Selecciona Una Transacción</option>
                                    <option value="devolucion" {{ old('tipo_transaccion') == 'devolucion' ? 'selected' : '' }}>Devolución</option>
                                    <option value="ingreso" {{ old('tipo_transaccion') == 'ingreso' ? 'selected' : '' }}>Ingreso Efectivo</option>
                                    <option value="retiro" {{ old('tipo_transaccion') == 'retiro' ? 'selected' : '' }}>Retiro en sucursal</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="metodo_pago">
                                    <i class="ri-wallet-line me-1"></i> Método de Retiro <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="metodo_pago" name="metodo_pago" required>
                                    <option value="efectivo" selected>Efectivo</option>
                                    <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                </select>
                            </div>

                            {{--
                            <div class="form-group col-md-4">
                                <label for="metodo_pago">Método de pago <span class="text-danger">*</span></label>
                                <select class="form-control" id="metodo_pago" name="metodo_pago" required>
                                    <option value="" disabled selected hidden>Selecciona Un Método</option>
                                    <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                    <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                </select>
                            </div>
                            --}}

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="monto">
                                        <i class="ri-money-dollar-circle-line me-1"></i> Monto a Disponer <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control @error('monto') is-invalid @enderror"
                                            id="monto" name="monto" min="1"
                                            value="{{ old('monto') }}" required>
                                    </div>
                                    @error('monto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="descripcion">
                                    <i class="ri-file-text-line me-1"></i> Descripción <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                    id="descripcion"
                                    name="descripcion"
                                    cols="25"
                                    rows="3"
                                    required>{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- end: Input Data -->

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <!-- Botón Guardar a la izquierda -->
                            <div>
                                <a class="btn bg-danger text-white" href="{{ route('mis_cajas.index') }}">
                                    <i class="ri-close-line"></i> Cancelar
                                </a>
                            </div>

                            <!-- Botón Cancelar a la derecha -->
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line"></i> Guardar
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
