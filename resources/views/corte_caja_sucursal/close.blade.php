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
                                Cerrar Caja de {{ $caja->first()?->sucursal->nombre ?? 'Sucursal desconocida' }}
                                <i class="fas fa-info-circle text-primary"
                                data-toggle="tooltip"
                                data-placement="right"
                                title="Cierra la caja actual registrando el monto final y el estado de la sucursal al término de la jornada.">
                                </i>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="cerrarCajaForm" action="{{ route('caja_sucursal.update', $caja->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                        <!-- end: Input Image -->
                        <!-- begin: Input Data -->
                        <div class=" row align-items-center">
                            <div class="form-group col-md-3">
                                <label for="nombre_usuario"><i class="ri-user-star-line me-1"></i> Nombre de Empleado</label>
                                <input type="text" class="form-control @error('nombre_usuario') is-invalid @enderror" id="nombre_usuario" name="nombre_usuario" value="{{ old('nombre_usuario', $caja->nombre_usuario) }}" disabled>
                                @error('nombre_usuario')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-3">
                                <label for="numero_caja"><i class="ri-safe-2-line me-1"></i> Número de Caja</label>
                                <input type="number" class="form-control @error('numero_caja') is-invalid @enderror" id="numero_caja" name="numero_caja" min="1" value="{{ old('numero_caja', $caja->numero_caja) }}" disabled>
                                @error('numero_caja')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                           <div class="form-group col-md-3">
                                <label for="monto_inicial"><i class="ri-money-dollar-circle-line me-1"></i> Monto Inicial</label>
                                <input type="text"
                                    class="form-control @error('monto_inicial') is-invalid @enderror"
                                    id="monto_inicial"
                                    name="monto_inicial"
                                    value="{{ '$' . number_format(old('monto_inicial', $caja->monto_inicial), 2) }}"
                                    disabled>
                                @error('monto_inicial')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="monto_final"><i class="ri-money-dollar-box-line me-1"></i> Monto final</label>
                                <input type="text" class="form-control @error('monto_final') is-invalid @enderror" id="monto_final" name="monto_final" value="{{ '$' . number_format(old('monto_final', $caja->monto_final), 2) }}" disabled>
                                @error('monto_final')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="hora_apertura"><i class="ri-alarm-line"></i> Hora Apertura</label>
                                <input type="time" class="form-control @error('hora_apertura') is-invalid @enderror" id="hora_apertura" name="hora_apertura" value="{{ old('hora_apertura', $caja->hora_apertura) }}" disabled>
                                @error('hora_apertura')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="fecha"><i class="ri-calendar-2-line me-1"></i> Fecha</label>
                                <input type="date" id="fecha" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', $caja->fecha) }}" disabled>
                                @error('fecha')
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
                                        <th>Tipo de transacción</th>
                                        {{-- <th>Monto</th> --}}
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Total</th>
                                        <th>Monto Cobrado</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transacciones as $transaccione)
                                        <tr>
                                            <td class="text-center">{{ (($transacciones->currentPage() - 1) * $transacciones->perPage()) + $loop->iteration }}</td>
                                            <td class="text-center">{{ucfirst($transaccione->tipo_transaccion)}}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($transaccione->fecha)->format('d/m/Y') }}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($transaccione->hora)->format('h:i A') }}</td>
                                            <td class="text-center">${{ $transaccione->monto }} </td>
                                            <td class="text-center">${{ $transaccione->total }} </td>
                                            <td>{{ $transaccione->descripcion}}</td>
                                                {{-- <td>
                                                @if($caja->estado == 'abierta')
                                                    <span class="badge badge-success">Abiera</span>
                                                @elseif($caja->estado == 'cerrada')
                                                    <span class="badge badge-danger">Cerrada</span>
                                                @endif
                                            </td> --}}

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-danger">No se encontraron transacciones en el día.</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>

                        </div>
                         {{ $transacciones->links() }}
                        <!-- end: Input Data -->
                    </form>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <!-- Botón Cancelar a la izquierda -->
                        <div>
                            <a class="btn bg-danger text-white" href="{{ route('caja_sucursal.index') }}">
                                <i class="ri-close-circle-line me-1"></i> Cancelar
                            </a>
                        </div>

                        <!-- Botón Cerrar caja a la derecha -->
                        <div>
                            <button type="button" class="btn btn-primary me-2" data-toggle="modal" data-target="#confirmModal">
                                <i class="ri-lock-2-line me-1"></i> Cerrar Caja
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>
<!-- Modal de confirmacion-->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirmar cierre de caja</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ¿Estás seguro que deseas cerrar la caja? Esta acción registrará el estado final del día.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" form="cerrarCajaForm">Sí, cerrar caja</button>
      </div>
    </div>
  </div>
</div>

@include('components.preview-img-form')
@endsection

