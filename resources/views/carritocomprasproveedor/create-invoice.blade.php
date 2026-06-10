@extends('dashboard.body.main')

@section('container')
<div class="container-fluid my-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Formulario principal --}}
            <form id="enviarTraspasoForm" action="{{ route('nuevascomprasproveedor.guardar') }}" method="POST">

                @csrf
                {{-- <input type="hidden" name="sucursal_origen" value="{{ $sucursal->id }}"> --}}
                {{-- Encabezado --}}
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <img src="{{ asset('assets/images/logo/logo-min.png') }}" alt="Logo" style="max-height: 60px;">
                    <h3 class="text-primary font-weight-bold mb-0">Nueva lista de productos</h3>
                    <div></div>
                </div>

                {{-- Información --}}
                <div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-4">
            <!-- Columna izquierda: datos del responsable -->
            <div class="col-md-6">
                <p class="mb-2"><strong><i class="fa-solid fa-user-tie me-1"></i>Responsable:</strong> {{ $empleado->name }}</p>
                <p class="mb-2"><strong><i class="fa-solid fa-calendar-day me-1"></i>Fecha:</strong> {{ now()->timezone('America/Mexico_City')->format('d/m/Y') }}</p>
                <p class="mb-2"><strong><i class="fa-solid fa-clock me-1"></i>Hora:</strong> {{ now()->timezone('America/Mexico_City')->format('H:i:s') }}</p>
                {{-- <p class="mb-2"><strong><i class="fa-solid fa-store me-1"></i>Sucursal:</strong> {{ $sucursal->nombre }}</p> --}}
            </div>

            <!-- Columna derecha: selección de sucursal -->
            {{-- Unidad de Origen --}}
            <div class="form-group col-md-3 mb-2">
                <label for="sucursal_origen" class="form-label">
                    <i class="ri-map-pin-line me-1"></i> Sucursal de Origen <span class="text-danger">*</span></label>
                </label>
                <select class="form-control" id="sucursal_origen" name="sucursal_origen" required>
                    <option value="" disabled selected hidden>Selecciona una unidad...</option>
                    @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Observaciones -->
        <div class="form-group mt-4">
            <label for="observaciones" class="form-label fw-bold">
                <i class="fa-solid fa-pen me-1"></i> Observaciones
            </label>
            <textarea name="observaciones" id="observaciones" class="form-control" rows="3" placeholder="Escribe alguna observación..."></textarea>
        </div>
    </div>
</div>


                {{-- Productos --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3 font-weight-bold">Resumen del Pedido</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm" style="font-size: 13px; white-space: nowrap;">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Cantidad</th>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Código de barras</th>
                                        <th>Marca</th>
                                        <th>Proveedor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($content as $item)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $item->qty }}</td>
                                            <td class="text-center">{{ $item->options->product_code }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td class="text-center">{{ $item->options->codigo_barras }}</td>
                                            <td class="text-center">{{ $item->options->marca }}</td>
                                            <td class="text-center">{{ $item->options->proveedor }}</td>
                                            {{-- Input para precio compra --}}
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No hay productos en el pedido.</td>
                                        </tr>
                                        @endforelse

                                </tbody>
                            </table>
                        </div>

                        @php
                            $cart = Cart::instance('compraslitproveedor');
                        @endphp
                        {{-- Totales --}}
                        <div class="row justify-content-end mt-4">
                            <div class="col-md-5 col-lg-4">
                                <div class="border rounded p-3 bg-light">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total de productos:</span>
                                        <span>{{ $cart->count() }}</span>
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
                            <a href="{{ route('nuevascomprasproveedor.index') }}" class="btn btn-outline-danger">
                                 <i class="fa-solid fa-cancel"></i>  Cancelar
                            </a>

                           <!-- Botón que ABRE el modal -->
                            <button type="button" class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#confirmModal">
                                  <i class="fas fa-boxes"></i> Realizar Lista
                            </button>
                            {{-- <form action="{{ route('cotizaciones.printInvoice') }}" method="post" target="_blank" class="m-0">
                                @csrf
                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                <button type="submit" class="btn btn-success d-flex align-items-center">
                                    <i class="ri-printer-line me-2" style="font-size: 1.2rem;"></i> Imprimir
                                </button>
                            </form> --}}
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
        <h5 class="modal-title" id="confirmModalLabel">Confirmar Lista</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ¿Está seguro de que desea generar esta lista de inventario?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

        <!-- Este botón ENVÍA el formulario -->
        <button type="submit" class="btn btn-primary" form="enviarTraspasoForm">
            Sí, generar
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

