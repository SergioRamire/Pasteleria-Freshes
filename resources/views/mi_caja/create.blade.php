@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">

                @if (session()->has('success'))
                    <div  id="alert-success" class="alert text-white bg-success" role="alert">
                        <div class="iq-alert-text">{{ session('success') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div id="alert-error" class="alert text-white bg-danger" role="alert">
                        <div class="iq-alert-text">{{ session('error') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>¡Ups! Hay algunos errores:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            <div class="card">
               <div class="card-header d-flex justify-content-between">
                    <div class="header-title d-flex align-items-center">
                        <i class="ri-archive-drawer-line text-primary mr-2" style="font-size: 1.5rem;"></i>
                        <h4 class="card-title mb-0">Abrir caja en tu sucursal</h4>
                    </div>
                </div>


                <div class="card-body">
                    <form action="{{ route('mis_cajas.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <div class=" row align-items-center">
                            <div class="form-group col-md-4">
                                <label for="user_id"><i class="ri-user-star-line me-1"></i> Empleado Responsable <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="numero_caja"><i class="ri-safe-2-line me-1"></i> Numero de Caja <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('numero_caja') is-invalid @enderror"
                                            id="numero_caja" name="numero_caja" min="1"
                                            value="{{ old('numero_caja') }}" required>
                                    @error('numero_caja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="monto_inicial"><i class="ri-money-dollar-circle-line me-1"></i> Monto Inicial <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('monto_inicial') is-invalid @enderror"
                                            id="monto_inicial" name="monto_inicial" min="1"
                                            value="{{ old('monto_inicial') }}" required>
                                    @error('monto_inicial')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <!-- end: Input Data -->
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <!-- Botón Abrir a la izquierda -->
                            <div>
                                <a href="{{ route('mis_cajas.index') }}" class="btn btn-danger text-white">
                                    <i class="ri-close-line me-1"></i> Cancelar
                                </a>
                            </div>

                            <!-- Botón Cancelar a la derecha -->
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-lock-unlock-line me-1"></i> Abrir Caja
                                </button>
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
