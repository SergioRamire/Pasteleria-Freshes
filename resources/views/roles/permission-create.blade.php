@extends('dashboard.body.main')

@section('container')
@php
    $group_names = [
        [
            'slug' => 'pos',
            'name' => 'POS'
        ],
        [
            'slug' => 'employee',
            'name' => 'Employee'
        ],
        [
            'slug' => 'customer',
            'name' => 'Customer'
        ],
        [
            'slug' => 'supplier',
            'name' => 'Supplier'
        ],
        [
            'slug' => 'salary',
            'name' => 'Salary'
        ],
        [
            'slug' => 'attendence',
            'name' => 'Attendence'
        ],
        [
            'slug' => 'category',
            'name' => 'Category'
        ],
        [
            'slug' => 'product',
            'name' => 'Product'
        ],
        [
            'slug' => 'orders',
            'name' => 'Orders'
        ],
        [
            'slug' => 'stock',
            'name' => 'Stock'
        ],
        [
            'slug' => 'roles',
            'name' => 'Roles'
        ],
        [
            'slug' => 'user',
            'name' => 'User'
        ],
        [
            'slug' => 'database',
            'name' => 'Database'
        ],
    ]
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            
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
                    <div class="header-title">
                        <h4 class="card-title">Crear permiso</h4>
                    </div>
                </div>

            @if (session()->has('error'))
                <div class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-text">{{ session('error') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

                <div class="card-body">
                    <form action="{{ route('permission.store') }}" method="POST">
                    @csrf
                        <!-- begin: Input Data -->
                        <div class=" row align-items-center">
                            <div class="form-group col-md-6">
                                <label for="name"><i class="ri-key-line me-1"></i> Nombre del permiso <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autocomplete="off">
                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="group_name"><i class="ri-group-line me-1"></i> Nombre del grupo <span class="text-danger">*</span></label>
                                <select class="form-control @error('group_name') is-invalid @enderror" name="group_name" required>
                                    <option selected="" disabled>-- Selecciona grupo --</option>
                                    @foreach ($group_names as $item)
                                        <option value="{{ $item['slug'] }}">{{ $item['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('group_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <!-- end: Input Data -->
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <a class="btn bg-danger text-white" href="{{ route('permission.index') }}">
                                <i class="ri-close-line me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>
@endsection
