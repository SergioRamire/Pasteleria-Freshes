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
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Historial de Actualización de Precio</h4>
                    <a href="{{ route('historiales.index') }}" class="btn btn-secondary ms-3">
                        <i class="ri-arrow-go-back-line me-1"></i> Volver
                    </a>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label><i class="ri-archive-line me-1"></i> Producto Afectado</label>
                            <input type="text" class="form-control bg-white" value="{{ ucfirst(strtolower($historial->producto)) }}" readonly>
                        </div>

                        <div class="form-group col-md-5">
                            <label><i class="ri-briefcase-line me-1"></i> Tipo de Actividad</label>
                            <input type="text" class="form-control bg-white" value="{{ ucfirst(strtolower($historial->accion)) }}" readonly>
                        </div>

                        @php
                            $fecha = \Carbon\Carbon::parse($historial->fecha);
                        @endphp

                        <div class="form-group col-md-3">
                            <label><i class="ri-calendar-event-line me-1"></i> Fecha de Modificación</label>
                            <input type="text" class="form-control bg-white" value="{{ $fecha->format('d-m-Y') }}" readonly>
                        </div>

                        <div class="form-group col-md-2">
                            <label><i class="ri-time-line me-1"></i> Hora</label>
                            <input type="text" class="form-control bg-white" value="{{ $fecha->format('H:i:s') }}" readonly>
                        </div>

                        <div class="form-group col-md-2">
                            <label><i class="ri-user-star-line me-1"></i> Responsable</label>
                            <input type="text" class="form-control bg-white" value="{{ ucfirst(strtolower($historial->usuario)) }}" readonly>
                        </div>

                        <div class="form-group col-md-12">
                            <label><i class="ri-file-text-line me-1"></i> Descripción Detallada</label>
                            <textarea class="form-control bg-white" rows="3" readonly>{{ $historial->descripcion }}</textarea>
                        </div>
                    </div>

                    {{-- Si en un futuro quieres mostrar precios antiguos, puedes descomentar esta sección --}}
                    {{--
                    <hr>
                    <h5>Precios anteriores</h5>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Precio de compra</label>
                            <input type="text" class="form-control bg-white" value="{{ $product->buying_price }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Precio público</label>
                            <input type="text" class="form-control bg-white" value="{{ $product->selling_price }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Precio distribuidor</label>
                            <input type="text" class="form-control bg-white" value="{{ $product->dealer_price }}" readonly>
                        </div>
                    </div>
                    --}}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
