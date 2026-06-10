@extends('dashboard.body.main')

@section('container')
<div class="container-fluid my-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            @if (session('imprimir_url'))
                <script>
                    window.onload = function() {
                    window.open("{{ session('imprimir_url') }}", '_blank');
                    };
                </script>
            @endif
            {{-- Formulario principal --}}
            <form id="enviarTraspasoForm" action="{{ route('traspasos.storeOrder') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="sucursal_destino_id" value="{{ $sucursal_destino->id }}">
                {{-- Encabezado --}}
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-height: 60px;">
                    <h3 class="text-primary font-weight-bold mb-0">Solicitud de Traspaso de Material</h3>
                    <div></div>
                </div>

                {{-- Información --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Solicitante:</strong> {{ $empleado->name }}</p>
                                <p><strong>Fecha:</strong> {{ now()->timezone('America/Mexico_City')->format('d/m/Y') }}</p>
                                <p><strong>Hora:</strong> {{ now()->timezone('America/Mexico_City')->format('H:i:s') }}</p>
                                <p><strong>Sucursal origen:</strong> {{ $sucursal_emisora->nombre }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Sucursal receptora:</strong></p>
                                <p class="border rounded p-2 bg-light">{{ $sucursal_destino->nombre }}</p>
                            </div>
                        </div>

                        {{-- Observaciones --}}
                        <div class="form-group mt-3">
                            <label for="observaciones"><strong>Observaciones</strong> (opcional)</label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Productos --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3 font-weight-bold">Resumen del Pedido</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Unidad</th>
                                        <th>Imp. Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($content as $item)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $item->options->codigo_producto }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td class="text-center">{{ $item->qty }}</td>
                                        <td class="text-center">{{ $item->options->unidad ?? 'Sin unidad' }}</td>
                                        <td class="text-center font-weight-bold">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No hay productos en el pedido.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @php
                            $cart = Cart::instance('traspaso');
                        @endphp
                        {{-- Totales --}}
                        <div class="row justify-content-end mt-4">
                            <div class="col-md-5 col-lg-4">
                                <div class="border rounded p-3 bg-light">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total de productos:</span>
                                        <span>{{ $cart->count() }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="font-weight-bold">Total general:</span>
                                        <span class="font-weight-bold">
                                            ${{ number_format((float)str_replace(',', '', $cart->subtotal()), 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Notas --}}
                        <div class="mt-4">
                            <p class="text-danger font-weight-bold">Notas:</p>
                            <p class="text-muted small">
                                Por favor verifica que los productos y cantidades sean correctos antes de enviar esta solicitud.
                            </p>
                        </div>

                        {{-- Botones --}}
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('traspasos.index') }}" class="btn btn-outline-danger">
                                 <i class="fa-solid fa-cancel"></i>  Cancelar
                            </a>

                           <!-- Botón que ABRE el modal -->
                            <button type="button" class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#confirmModal">
                                <i class="fa-solid fa-paper-plane me-1"></i>  Enviar Solicitud
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- Modal de confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirmar Traspaso</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ¿Estás seguro que deseas enviar esta solicitud de traspaso?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

        <!-- Este botón ENVÍA el formulario -->
        <button type="submit" class="btn btn-primary" form="enviarTraspasoForm">
            Sí, enviar traspaso
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

