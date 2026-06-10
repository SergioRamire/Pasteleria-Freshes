@extends('dashboard.body.main')

@section('container')
<div class="container-fluid my-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Formulario principal --}}
            <form id="enviarTraspasoForm" action="{{ route('nuevascompras.storeOrder') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="sucursal_id" value="{{ $sucursal->id }}">
                {{-- Encabezado --}}
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <img src="{{ asset('assets/images/logo/logo-min.png') }}" alt="Logo" style="max-height: 60px;">
                    <h3 class="text-primary font-weight-bold mb-0">Nueva compra de inventario</h3>
                    <div></div>
                </div>

                {{-- Información --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Responsable:</strong> {{ $empleado->name }}</p>
                                <p><strong>Fecha:</strong> {{ now()->timezone('America/Mexico_City')->format('d/m/Y') }}</p>
                                <p><strong>Hora:</strong> {{ now()->timezone('America/Mexico_City')->format('H:i:s') }}</p>
                                <p><strong>Sucursal:</strong> {{ $sucursal->nombre }}</p>
                            </div>
                        </div>

                        {{-- Observaciones --}}
                        <div class="form-group mt-3">
                            <label for="observaciones"><strong>Observaciones</strong> (obligatorio)</label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="3" required></textarea>
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
                                        <th>Producto</th>
                                        <th>Código</th>
                                        <th>Cantidad</th>
                                        <th>Unid.</th>
                                        <th>P. Actual</th>
                                        <th>P. Compra</th>
                                        <th>P. Venta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($content as $item)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td class="text-center">{{ $item->options->codigo_product }}</td>
                                            <td class="text-center">{{ $item->qty }}</td>
                                            <td class="text-center">
                                                {{ $item->options->unidad ?? 'N/A' }}
                                            </td>
                                            <td class="text-right font-weight-bold">${{ number_format($item->subtotal, 2) }}</td>

                                            {{-- Input para precio compra --}}
                                            <td class="text-center">
                                                <input type="number" step="1.0" min="0" name="purchase_price[{{ $item->rowId }}]" value="{{ old('purchase_price.' . $item->rowId, $item->options->purchase_price ?? '') }}" class="form-control form-control-sm">

                                                {{-- Enviar código de barras oculto --}}
                                                <input type="hidden" name="codigo_barras[{{ $item->rowId }}]" value="{{ $item->options->codigo_barras }}">

                                                {{-- Enviar precio compra oculto (por si quieres tener el original también) --}}
                                                <input type="hidden" name="original_purchase_price[{{ $item->rowId }}]" value="{{ $item->options->purchase_price ?? '' }}">

                                                {{-- Enviar precio venta oculto (por si quieres tener el original también) --}}
                                                <input type="hidden" name="original_selling_price[{{ $item->rowId }}]" value="{{ $item->options->selling_price ?? '' }}">
                                            </td>

                                            {{-- Input para precio venta --}}
                                            <td class="text-center">
                                                <input type="number" step="1.0" min="0" name="selling_price[{{ $item->rowId }}]" value="{{ old('selling_price.' . $item->rowId, $item->options->selling_price ?? '') }}"
                                                    class="form-control form-control-sm">
                                            </td>
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
                            $cart = Cart::instance('compras');
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
                            <a href="{{ route('nuevascompras.index') }}" class="btn btn-outline-danger">
                                 <i class="fa-solid fa-cancel"></i>  Cancelar
                            </a>

                           <!-- Botón que ABRE el modal -->
                            <button type="button" class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#confirmModal">
                                  <i class="fas fa-boxes"></i> Agregar al Inventario
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
        <h5 class="modal-title" id="confirmModalLabel">Confirmar Inventario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ¿Estás seguro que deseas agreagar estos productos a tu inventario?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

        <!-- Este botón ENVÍA el formulario -->
        <button type="submit" class="btn btn-primary" form="enviarTraspasoForm">
            Sí, agregar
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

