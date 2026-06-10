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
                        <h4 class="card-title">Detalles de compras de inventario</h4>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row align-items-center">

                        {{-- Datos del Traspaso --}}
                        <div class="form-group col-md-3">
                            <label><i class="ri-file-list-3-line me-1"></i> Código de la compra</label>
                            <input type="text" class="form-control bg-white" value="{{ $compra->codigo }}" readonly>
                        </div>

                        <div class="form-group col-md-3">
                            <label><i class="ri-building-line me-1"></i> Sucursal</label>
                            <input type="text" class="form-control bg-white" value="{{ $compra->sucursal_origen_nombre }}" readonly>
                        </div>

                        <div class="form-group col-md-2">
                            <label><i class="ri-user-settings-line me-1"></i> Empleado</label>
                            <input type="text" class="form-control bg-white" value="{{ ucfirst($compra->empleado) }}" readonly>
                        </div>

                        <div class="form-group col-md-2">
                            <label><i class="ri-calendar-event-line me-1"></i> Fecha</label>
                            <input type="date" class="form-control bg-white" value="{{ $compra->fecha }}" readonly>
                        </div>

                        <div class="form-group col-md-2">
                            <label><i class="ri-timer-line me-1"></i> Hora</label>
                            <input type="time" class="form-control bg-white" value="{{ $compra->hora }}" readonly>
                        </div>

                        <div class="form-group col-md-12">
                            <label><i class="ri-file-text-line me-1"></i> Observaciones</label>
                            <input type="text" class="form-control bg-white" value="{{ $compra->observaciones }}" readonly>
                        </div>

                        {{-- Separador de productos --}}
                        <div class="form-group col-md-12 mt-3">
                            <hr>
                            <h5 class="mb-3">Productos comprados</h5>
                        </div>

                        {{-- Tabla de productos --}}
                        <div class="form-group col-md-12">
                            <table class="table table table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Cantidad</th>
                                         <th class="text-center">Unidad</th>
                                        <th class="text-center">Producto</th>
                                        <th class="text-center">Código</th>
                                        <th class="text-center">Marca</th>
                                        <th class="text-center">Proveedor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($productos_compras as $productos_compra)
                                        <tr>
                                            <td class="text-center">
                                                {{ ($productos_compras->currentPage() - 1) * $productos_compras->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="text-center">{{ $productos_compra->cantidad }}</td>
                                            <td class="text-center">
                                                {{ $productos_compra->equivalencia ?? 'Sin unidad' }}
                                            </td>
                                            <td>{{ ucfirst($productos_compra->producto) }}</td>
                                            <td class="text-center">{{ ucfirst($productos_compra->codigo) }}</td>
                                            <td class="text-center">{{ ucfirst($productos_compra->marca) }}</td>
                                            <td class="text-center">{{ ucfirst($productos_compra->proveedor) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-danger">No se encontraron productos en esta compra.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{-- Paginación --}}
                            <div class="mt-3">
                                {{ $productos_compras->links() }}
                            </div>
                        </div>

                        {{-- Botón regresar --}}
                        <div class="form-group col-md-12 text-end">
                            <a href="{{ route('compras.index') }}" class="btn btn-danger font-size-14">
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
