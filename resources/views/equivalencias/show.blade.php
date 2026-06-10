@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Ver Equivalencia</h4>
                    </div>
                    <a href="{{ route('equivalencias.index') }}" class="btn btn-primary">
                        <i class="ri-arrow-go-back-line me-1"></i> Regresar
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="nombre"><i class="ri-exchange-line me-1"></i> Nombre Equivalencia</label>
                            <p class="form-control-plaintext">{{ $equivalencia->nombre }}</p>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="abreviatura"><i class="ri-input-method-line me-1"></i> Abreviatura</label>
                            <p class="form-control-plaintext">{{ $equivalencia->abreviatura }}</p>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="activo"><i class="ri-toggle-line me-1"></i> Estado</label>
                            <p class="form-control-plaintext">
                                @if($equivalencia->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </p>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="descripcion"><i class="ri-file-text-line me-1"></i> Descripción</label>
                            <p class="form-control-plaintext">{{ $equivalencia->descripcion }}</p>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="clave_sat"><i class="ri-government-line me-1"></i> Clave SAT</label>
                            <p class="form-control-plaintext">
                                <span class="badge badge-primary fs-6">{{ $equivalencia->clave_sat }}</span>
                            </p>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="tipo"><i class="ri-price-tag-line me-1"></i> Tipo</label>
                            <p class="form-control-plaintext">
                                <span class="badge badge-secondary">{{ $equivalencia->tipo }}</span>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
