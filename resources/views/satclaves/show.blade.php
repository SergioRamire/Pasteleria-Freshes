@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Ver Clave SAT</h4>
                    </div>
                    <a href="{{ route('satclaves.index') }}" class="btn btn-primary">
                        <i class="ri-arrow-go-back-line me-1"></i> Regresar
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="nombre"><i class="ri-numbers-line me-1"></i> Clave SAT</label>
                            <p class="form-control-plaintext">{{ $clave->c_ClaveProdServ }}</p>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="activo"><i class="ri-toggle-line me-1"></i> Estado:</label>
                            <p class="form-control-plaintext">
                                @if($clave->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </p>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="descripcion"><i class="ri-file-text-line me-1"></i> Descripción</label>
                            <p class="form-control-plaintext">{{ $clave->descripcion }}</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
