@extends('dashboard.body.main')

@section('container')
<div class="container-fluid mb-3">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-transparent border-0 shadow-none">
                <div class="profile-image position-relative">
                    <img src="{{ asset('assets/images/page-img/profile.png') }}" class="img-fluid rounded w-100" alt="profile-image" style="max-height: 400px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>

    <div class="row px-3">
        <!-- Left Panel: Perfil Cliente -->
        <div class="col-lg-4 card-profile mb-5 h-50">
            <div class="card card-block card-stretch card-height mb-5">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="profile-img position-relative">
                            <img
                            src="{{ $customer->photo ? asset('storage/customers/' . $customer->photo) : asset('assets/images/user/1.png') }}"
                            alt="profile-image"
                            class="rounded-circle mb-3"
                            style="width: 110px; height: 110px; object-fit: cover;"
                            >
                        </div>
                        <div class="ml-3">
                            <h4 class="mb-1">{{ ucwords(strtolower($customer->name)) }}</h4>
                            <p class="mb-2">{{ $customer->shopname }}</p>
                        </div>
                    </div>

                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary btn-block mb-2">
                        <i class="ri-pencil-line mr-1"></i> Editar
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-block">
                        <i class="ri-arrow-left-line mr-1"></i> Regresar
                    </a>

                    <ul class="list-unstyled mt-4 text-left">
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ri-mail-line mr-3" style="font-size: 1.2rem;"></i>
                            <span>{{ $customer->email }}</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="ri-phone-line mr-3" style="font-size: 1.2rem;"></i>
                            <span>{{ $customer->phone }}</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <a href="{{ $customer->rul_maps }}" target="_blank" class="d-flex align-items-center text-decoration-none text-success" title="Ver ubicación en Google Maps">
                                <i class="ri-map-pin-line mr-2" style="font-size: 1.2rem;"></i>
                                <span>
                                    {{ $customer->municipio || $customer->estado
                                        ? ucwords(strtolower(trim($customer->municipio . ', ' . $customer->estado)))
                                        : 'Desconocida' }}
                                </span>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Panel: Información Detallada -->
        <div class="col-lg-8 card-profile">
            <div class="card card-block card-stretch mb-0">
                <div class="card-header px-3">
                    <div class="header-title">
                        <h4 class="card-title">Información del Cliente</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Nombre -->
                        <div class="form-group col-md-6 mb-3">
                            <label>Nombre Completo</label>
                            <input type="text" class="form-control" value="{{ ucwords(strtolower($customer->name)) }}" readonly>

                        </div>

                        <!-- Nombre de Tienda -->
                        <div class="form-group col-md-6 mb-3">
                            <label>Nombre Empresa</label>
                            <input type="text" class="form-control" value="{{ $customer->shopname }}" readonly>
                        </div>

                        <!-- Teléfono -->
                        <div class="form-group col-md-6 mb-3">
                            <label>Teléfono</label>
                            <input type="text" class="form-control" value="{{ $customer->phone }}" readonly>
                        </div>

                        <!-- Teléfono 2 -->
                        <div class="form-group col-md-6 mb-3">
                            <label>Teléfono 2</label>
                            <input type="text" class="form-control" value="{{ $customer->phone2 }}" readonly>
                        </div>

                        <!-- Email -->
                        <div class="form-group col-md-6 mb-3">
                            <label>Correo Electrónico</label>
                            <input type="text" class="form-control" value="{{ $customer->email }}" readonly>
                        </div>

                        <!-- Tipo Cliente -->
                        <div class="form-group col-md-6 mb-3">
                            <label>Tipo de Cliente</label>
                            <input type="text" class="form-control" value="{{ ucfirst($customer->type_customer) }}" readonly>
                        </div>
                    </div>

                    <hr>

                    <!-- Dirección -->
                    <h5 class="mb-3"><i class="fas fa-map-marker-alt text-primary"></i> Dirección</h5>
                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label>Calle</label>
                            <input type="text" class="form-control" value="{{ $customer->calle }}" readonly>
                        </div>

                        <div class="form-group col-md-3 mb-3">
                            <label>Número Exterior</label>
                            <input type="text" class="form-control" value="{{ $customer->num_exterior }}" readonly>
                        </div>

                        <div class="form-group col-md-3 mb-3">
                            <label>Número Interior</label>
                            <input type="text" class="form-control" value="{{ $customer->num_interior ?? '-' }}" readonly>
                        </div>

                        <div class="form-group col-md-2 mb-3">
                            <label>C.P.</label>
                            <input type="text" class="form-control" value="{{ $customer->cp }}" readonly>
                        </div>

                        <div class="form-group col-md-4 mb-3">
                            <label>Colonia</label>
                            <input type="text" class="form-control" value="{{ $customer->colonia }}" readonly>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            <label>Municipio</label>
                            <input type="text" class="form-control" value="{{ $customer->municipio }}" readonly>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            <label>Estado</label>
                            <input type="text" class="form-control" value="{{ $customer->estado }}" readonly>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            <label>País</label>
                            <input type="text" class="form-control" value="{{ $customer->pais }}" readonly>
                        </div>
                    </div>

                    <hr>

                    <!-- Datos Bancarios -->
                    <h5 class="mb-3"><i class="ri-bank-line text-primary"></i> Datos Fiscales</h5>
                    <div class="row">
                        <!-- RFC -->
                        <div class="form-group col-md-6 mb-3">
                            <label>RFC con Homoclave</label>
                            <input type="text" class="form-control" value="{{ $customer->rfc }}" readonly>
                        </div>

                        <!-- Tipo de Persona -->
                        <div class="form-group col-md-6 mb-3">
                            <label>Tipo de Persona</label>
                            <input type="text" class="form-control" value="{{ $customer->tipo_persona }}" readonly>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            <label>Regimen Fiscal</label>
                            <input type="text" class="form-control" value="{{ $customer->regimen_fiscal ?? '-' }}" readonly>
                        </div>

                        <!-- RFC -->
                        <div class="form-group col-md-6 mb-3">
                            <label>Uso CFDI</label>
                            <input type="text" class="form-control" value="{{ $customer->uso_cfdi }}" readonly>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- End Right Panel -->
    </div>
</div>
@endsection
