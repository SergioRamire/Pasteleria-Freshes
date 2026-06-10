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
                        <h4 class="card-title">Información del Traspaso Emitido</h4>
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
                            <table class="table table table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Imagen</th>
                                        <th>Producto</th>
                                        <th class="text-center">Código</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Unidad</th>
                                        <th class="text-center">Precio U.</th>
                                        <th class="text-center">Imp. Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($productos_traspasos as $productos_traspaso)
                                        <tr>
                                            <td class="text-center" >
                                                {{ ($productos_traspasos->currentPage() - 1) * $productos_traspasos->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="text-center">
                                                <img class="avatar-60 rounded" src="{{ $productos_traspaso->product_image ? asset('storage/products/'.$productos_traspaso->product_image) : asset('assets/images/product/default.webp') }}">
                                            </td>
                                            <td>{{ ucfirst($productos_traspaso->producto) }}</td>
                                            <td class="text-center">{{ $productos_traspaso->codigo_producto }}</td>
                                            <td class="text-center">{{ $productos_traspaso->cantidad }}</td>
                                            <td class="text-center">
                                                {{ $productos_traspaso->unidad ? ucfirst(strtolower($productos_traspaso->unidad)) : 'Sin unidad' }}
                                            </td>
                                            <td class="text-center">${{ number_format($productos_traspaso->selling_price, 2) }}</td>
                                            <td class="text-center font-weight-bold">
                                                ${{ number_format($productos_traspaso->cantidad * $productos_traspaso->selling_price, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No se encontraron productos en este traspaso.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{-- Paginación --}}
                            <div class="mt-3">
                                {{ $productos_traspasos->links() }}
                            </div>
                        </div>

                        {{-- Botón regresar --}}
                        <div class="form-group col-md-12 text-end">
                            <a href="{{ route('listTraspasos.index') }}" class="btn btn-danger font-size-14">
                                 <i class="ri-arrow-left-line mr-0"></i> Regresar
                                </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('components.preview-img-form')
@endsection
