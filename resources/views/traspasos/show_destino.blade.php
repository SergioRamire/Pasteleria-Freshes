@extends('dashboard.body.main')

@section('specificpagestyles')
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('container')
<div class="container-fluid">
    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Información del Traspaso Solicitado</h4>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row align-items-center">

                        {{-- Datos del Traspaso --}}
                        <div class="form-group col-md-3">
                            <label><i class="ri-barcode-box-line me-1"></i> Código del Traspaso</label>
                            <input type="text" class="form-control bg-white" value="{{ $traspaso->codigo }}" readonly>
                        </div>

                        <div class="form-group col-md-3">
                            <label><i class="ri-calendar-line me-1"></i> Fecha</label>
                            <input type="date" class="form-control bg-white" value="{{ $traspaso->fecha }}" readonly>
                        </div>

                        <div class="form-group col-md-3">
                            <label><i class="ri-time-line me-1"></i> Hora</label>
                            <input type="time" class="form-control bg-white" value="{{ $traspaso->hora }}" readonly>
                        </div>

                        <div class="form-group col-md-3">
                            <label><i class="ri-repeat-line me-1"></i> Estado</label>
                            <input type="text" class="form-control bg-white" value="{{ ucfirst($traspaso->estado) }}" readonly>
                        </div>

                        <div class="form-group col-md-6">
                            <label><i class="ri-map-pin-line me-1"></i> Sucursal Origen</label>
                            <input type="text" class="form-control bg-white" value="{{ $traspaso->sucursal_origen_nombre }}" readonly>
                        </div>

                        <div class="form-group col-md-6">
                            <label><i class="ri-map-pin-add-line me-1"></i> Sucursal Destino</label>
                            <input type="text" class="form-control bg-white" value="{{ $traspaso->sucursal_destino_nombre }}" readonly>
                        </div>

                        {{-- Separador de productos --}}
                        <div class="form-group col-md-12 mt-3">
                            <hr>
                            <h5 class="mb-3">Productos del Traspaso</h5>
                        </div>

                        {{-- Tabla de productos --}}
                        <div class="form-group col-md-12">
                            <table class="table table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Imagen</th>
                                        <th class="text-center">Producto</th>
                                        <th class="text-center">Código</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Unidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($productos_traspasos as $productos_traspaso)
                                        <tr class="text-center">
                                            <td>
                                                {{ ($productos_traspasos->currentPage() - 1) * $productos_traspasos->perPage() + $loop->iteration }}
                                            </td>
                                            <td>
                                                <img class="avatar-60 rounded" src="{{ $productos_traspaso->product_image ? asset('storage/products/'.$productos_traspaso->product_image) : asset('assets/images/product/default.webp') }}">
                                            </td>
                                            <td>{{ ucfirst($productos_traspaso->producto) }}</td>
                                            <td>{{ ucfirst($productos_traspaso->product_code) }}</td>
                                            <td>{{ $productos_traspaso->cantidad }}</td>
                                            <td>{{ $productos_traspaso->unidad ?? 'Sin unidad' }}</td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-danger">No se encontraron productos en este traspaso.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{-- Paginación --}}
                            <div class="mt-3">
                                {{ $productos_traspasos->links() }}
                            </div>
                        </div>

                        {{-- Botones a la misma altura, separados izquierda y derecha --}}
                        <div class="col-md-12 d-flex justify-content-between align-items-center mt-3">
                            {{-- Botón izquierdo --}}
                            <a href="{{ route('listTraspasosRecibidos.index') }}" class="btn btn-danger d-flex align-items-center">
                                <i class="ri-arrow-left-line mr-1"></i> Regresar
                            </a>

                            {{-- Botón derecho --}}
                            @can('traspasos_recibidos.despachar')
                                @if($traspaso->estado == 'solicitado')
                                    <form action="{{ route('traspasos.markAsDespachado', $id) }}" method="POST" class="mb-0" id="formDespachar">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">
                                            <i class="ri-lock-2-line mr-0"></i> Despachar
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Modal de confirmacion-->

<!-- Modal de confirmacion-->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirmar despachar el traspaso</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ¿Estás seguro que desea marcar como despachado? Esta acción registrará el estado final del día.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <!-- Este botón envía el formulario -->
        <button type="submit" class="btn btn-primary" form="formDespachar">Sí, despachar</button>
      </div>
    </div>
  </div>
</div>

@include('components.preview-img-form')
@endsection
