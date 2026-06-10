@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Editar Clave SAT</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('satclaves.update', $clave->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="c_ClaveProdServ"><i class="ri-numbers-line me-1"></i> Clave SAT <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('c_ClaveProdServ') is-invalid @enderror"
                                    id="c_ClaveProdServ"
                                    name="c_ClaveProdServ"
                                    maxlength="50"
                                    pattern="[0-9\s]{3,20}"
                                    title="Solo números. Mínimo 3 y máximo 20 caracteres."
                                    value="{{ old('c_ClaveProdServ', $clave->c_ClaveProdServ) }}"
                                    required
                                    oninput="this.value = this.value.replace(/[^0-9\s]/g, '').slice(0, 50)">
                                @error('c_ClaveProdServ')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="form-group col-md-2">
                                <label for="activo"><i class="ri-toggle-line me-1"></i> Estado</label>
                                <select name="activo" id="activo" class="form-control @error('activo') is-invalid @enderror" required>
                                    <option value="1" {{ old('activo', $clave->activo) == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('activo', $clave->activo) == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('activo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-7">
                                <label for="descripcion">
                                    <i class="ri-file-text-line me-1"></i>
                                    Descripción
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('descripcion') is-invalid @enderror"
                                    name="descripcion"
                                    id="descripcion"
                                    value="{{ old('descripcion', $clave->descripcion) }}"
                                    required
                                >
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                <a href="{{ route('satclaves.index') }}" class="btn btn-danger text-white">
                                    <i class="ri-close-line me-1"></i> Cancelar
                                </a>
                            </div>

                            <div>
                                <button type="submit" class="btn btn-success">
                                    <i class="ri-save-line me-1"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
