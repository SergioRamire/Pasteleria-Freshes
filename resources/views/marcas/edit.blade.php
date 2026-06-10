@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Editar Marca</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('marcas.update', $marca->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('put')


                        <!-- end: Input Image -->
                        <!-- begin: Input Data -->
                        <div class=" row align-items-center">
                            <div class="form-group col-md-6">
                                <label for="name"><i class="ri-price-tag-3-line me-1"></i> Nombre Marca <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $marca->nombre) }}" required>
                                @error('nombre')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="suppliers_id"><i class="ri-truck-line me-1"></i> Proveedor <span class="text-danger">*</span></label>
                                <select class="form-control @error('suppliers_id') is-invalid @enderror" name="suppliers_id" required>
                                    <option disabled selected hidden{{ old('suppliers_id', $marca->suppliers_id ?? '') ? '' : 'selected' }}> Seleccione Proveedor </option>
                                    @foreach (\App\Models\Supplier::all() as $proveedor)
                                        <option value="{{ $proveedor->id }}"
                                            {{ old('suppliers_id', $marca->suppliers_id ?? '') == $proveedor->id ? 'selected' : '' }}>
                                            {{ $proveedor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('suppliers_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                <a class="btn btn-danger text-white" href="{{ route('marcas.index') }}">
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
