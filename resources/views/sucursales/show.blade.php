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
                        <h4 class="card-title">Información de la Sucursal</h4>
                    </div>
                </div>

                <div class="card-body">


                    <div class=" row align-items-center">
                        <div class="form-group col-md-6">
                                <label for="nombre"><i class="ri-store-line me-1"></i> Nombre de la sucursal</label>
                            <input type="text" class="form-control" value="{{  $sucursal->nombre }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label><i class="ri-map-pin-line me-1"></i> Direccion</label>
                            <input type="text" class="form-control" value="{{  $sucursal->direccion }}" readonly>
                        </div>
                    </div>
                    <!-- end: Show Data -->
                    <div class="mt-3">
                        <a href="{{ route('sucursales.index') }}" class="btn btn-danger"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>


@include('components.preview-img-form')
@endsection
