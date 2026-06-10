@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center">
                        <i class="ri-lock-line text-primary mr-2" style="font-size: 1.5rem;"></i> {{-- Icono de cerrar/candado --}}
                        <div class="header-title">
                            <h4 class="card-title mb-0">
                                Caja del Día
                                <i class="fas fa-info-circle text-primary"
                                data-toggle="tooltip"
                                data-placement="right"
                                title="Inspecciona la caja del día actual, revisa las transacciones realizadas hasta el momento y cierra la caja si es necesario.">
                                </i>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                        <!-- end: Input Image -->
                        <!-- begin: Input Data -->
                        <div class=" row align-items-center">
                            <div class="form-group col-md-3">
                                <label for="nombre_usuario"><i class="ri-user-star-line me-1"></i> Nombre de empleado</label>
                                <input type="text" class="form-control @error('nombre_usuario') is-invalid @enderror" id="nombre_usuario" name="nombre_usuario" value="{{ old('nombre_usuario', $caja->nombre_usuario) }}" disabled>
                                @error('nombre_usuario')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-3">
                                <label for="numero_caja"><i class="ri-safe-2-line me-1"></i> Número de caja</label>
                                <input type="number" class="form-control @error('numero_caja') is-invalid @enderror" id="numero_caja" name="numero_caja" min="1" value="{{ old('numero_caja', $caja->numero_caja) }}" disabled>
                                @error('numero_caja')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                           <div class="form-group col-md-3">
                                <label for="monto_inicial"><i class="ri-money-dollar-circle-line me-1"></i> Monto inicial</label>
                                <input type="text" class="form-control @error('monto_inicial') is-invalid @enderror" id="monto_inicial" name="monto_inicial"
                                    value="{{ '$' . number_format(old('monto_inicial', $caja->monto_inicial), 2) }}" disabled>
                                @error('monto_inicial')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-3">
                                <label for="monto_final"><i class="ri-money-dollar-box-line me-1"></i> Monto final</label>
                                <input type="text" class="form-control @error('monto_final') is-invalid @enderror" id="monto_final" name="monto_final"
                                    value="{{ '$' . number_format(old('monto_final', $caja->monto_final), 2) }}" disabled>
                                @error('monto_final')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="hora_apertura"><i class="ri-alarm-line"></i> Hora Apertura</label>
                                <input type="time" class="form-control @error('hora_apertura') is-invalid @enderror" id="hora_apertura" name="hora_apertura" value="{{ old('hora_apertura', $caja->hora_apertura) }}" disabled>
                                @error('hora_apertura')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="hora_cierre"><i class="ri-timer-line me-1"></i> Hora cierre</label>
                                <input type="time" class="form-control @error('hora_cierre') is-invalid @enderror" id="hora_cierre" name="hora_cierre" value="{{ old('hora_cierre', $caja->hora_cierre) }}" disabled>
                                @error('hora_cierre')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="fecha"><i class="ri-calendar-2-line me-1"></i> Fecha</label>
                                <input type="date" id="fecha" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', $caja->fecha) }}" disabled>
                                @error('fecha')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                             <div class="form-group col-md-3">
                                <label for="fecha"><i class="ri-repeat-line me-1"></i> Estado</label>
                                <input type="text" id="estado" name="estado" class="form-control @error('estado') is-invalid @enderror" value="{{ old('estado', ucfirst($caja->estado)) }}" disabled>
                                @error('estado')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-12">
                                <hr>
                                <h5 class="mb-3">Transacciones del día.</h5>
                            </div>

                             <table class="table mb-0">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data text-center">
                                        <th>#</th>
                                        <th>Tipo transacción</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Total</th>
                                        {{-- <th>Monto Cobrado</th> --}}
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transacciones as $transaccione)
                                        <tr>
                                            <td class="text-center">{{ (($transacciones->currentPage() - 1) * $transacciones->perPage()) + $loop->iteration }}</td>
                                            <td>{{ ucfirst( $transaccione->tipo_transaccion)}}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($transaccione->fecha)->format('d/m/Y') }}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($transaccione->hora)->format('h:i A') }}</td>
                                            <td class="text-center">${{ $transaccione->total }} </td>
                                            {{-- <td>${{ $transaccione->monto }} </td> --}}
                                            <td>{{ $transaccione->descripcion}}</td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No se encontraron transacciones en el día.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                         {{ $transacciones->links() }}

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <!-- Botón Izquierdo -->
                        <div>
                            <a href="{{ route('mis_cajas.index') }}" class="btn btn-danger text-white">
                                <i class="ri-arrow-left-line mr-1"></i> Regresar
                            </a>
                        </div>

                        <!-- Botón Derecho -->
                        <div>
                            <form action="{{ route('mis_cajas.imprimir_reporte') }}" method="POST" target="_blank">
                                @csrf
                                <input type="hidden" name="caja_id" value="{{ $caja->id }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-printer-line mr-1"></i> Imprimir Reporte de Caja
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

@include('components.preview-img-form')
@endsection
