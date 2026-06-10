@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Editar {{ old('nombre', $sucursal->nombre) }}</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('sucursales.update', $sucursal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('put')


                        <!-- end: Input Image -->
                        <!-- begin: Input Data -->
                        <div class=" row align-items-center">
                            <div class="form-group col-md-6">
                                <label for="nombre"><i class="ri-store-line me-1"></i> Nombre de la sucursal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $sucursal->nombre) }}" required>
                                @error('nombre')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="rul_maps">
                                    <a href="https://maps.google.com.mx/" target="_blank" style="text-decoration: none; color: inherit;" class="me-1">
                                    <i class="ri-map-pin-line me-1"></i>
                                    <a href="https://maps.google.com.mx/" target="_blank" style="text-decoration: none; color: inherit;">
                                    Direccion
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion', $sucursal->direccion) }}" required>
                                @error('direccion')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                        </div>
                        <!-- end: Input Data -->
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <!-- Botón Guardar -->
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-refresh-line me-1"></i> Actualizar
                                </button>
                            </div>

                            <!-- Botón Cancelar -->
                            <div>
                                <a class="btn btn-danger text-white" href="{{ route('sucursales.index') }}">
                                    <i class="ri-close-line me-1"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

@include('components.preview-img-form')
@endsection
