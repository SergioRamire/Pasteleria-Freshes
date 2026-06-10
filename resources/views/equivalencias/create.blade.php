@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Agregar Equivalencia</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('equivalencias.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Información general --}}
                        <div class="row">
                            {{-- Nombre Equivalencia --}}
                            <div class="form-group col-md-6">
                                <label for="nombre">
                                    <i class="ri-exchange-line me-1"></i> Nombre Equivalencia
                                    <span class="text-danger">*</span>
                                    <!-- Icono de información con tooltip -->
                                    <i class="ri-information-line text-info ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Solo letras y espacios. Mínimo 2 y máximo 50 caracteres."></i>
                                </label>
                                <input type="text"
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    id="nombre"
                                    name="nombre"
                                    maxlength="10"
                                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ0-9.³²\s]{2,50}"
                                    value="{{ old('nombre') }}"
                                    required
                                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ0-9.³²\s]/g, '').slice(0, 50)">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Abreviatura --}}
                            <div class="form-group col-md-6">
                                <label for="nombre">
                                    <i class="ri-input-method-line me-1"></i> Abreviatura
                                    <span class="text-danger">*</span>
                                    <!-- Icono de información con tooltip -->
                                    <i class="ri-information-line text-info ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Solo letras mayúsculas sin espacios. Mínimo 2 y máximo 6 caracteres."></i>
                                </label>
                                <input type="text"
                                    class="form-control @error('abreviatura') is-invalid @enderror"
                                    id="abreviatura"
                                    name="abreviatura"
                                    maxlength="6"
                                    minlength="2"
                                    pattern="[A-Z0-9.\-]{2,6}"
                                    value="{{ old('abreviatura') }}"
                                    required
                                    oninput="this.value = this.value.replace(/[^A-Z0-9.\-]/g, '').slice(0, 6)"
                                    style="text-transform: uppercase;">
                                @error('abreviatura')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Descripción --}}
                            <div class="form-group col-md-12">
                                <label for="descripcion">
                                    <i class="ri-file-text-line me-1"></i>
                                    Descripción
                                </label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" name="descripcion" rows="2">{{ old('descripcion') }}</textarea>
                                @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Clave SAT --}}
                            <div class="form-group col-md-6">
                                <label for="clave_sat">
                                    <i class="ri-government-line me-1"></i> Clave SAT
                                    <!-- Icono de información con tooltip -->
                                    <i class="ri-information-line text-info ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Solo letras mayúsculas sin espacios. Mínimo 2 y máximo 6 caracteres."></i>
                                </label>
                                <input type="text"
                                    class="form-control @error('clave_sat') is-invalid @enderror"
                                    id="clave_sat"
                                    name="clave_sat"
                                    maxlength="10"
                                    pattern="[A-Z]{2,6}"
                                    title="Solo letras y números. Mínimo 2 y máximo 10 caracteres."
                                    value="{{ old('clave_sat') }}"
                                    oninput="this.value = this.value.replace(/[^A-Z]/g, '').slice(0, 6); toggleTipoRequired();">
                                @error('clave_sat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tipo de clave SAT --}}
                            <div class="form-group col-md-6">
                                <label for="tipo">
                                    <i class="ri-price-tag-line me-1"></i> Tipo
                                    <span class="text-danger" id="tipo-required" style="display: none;">*</span>
                                </label>
                                <select class="form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo">
                                    <option value="" disabled selected hidden>Seleccionar tipo</option>
                                    <option value="Mecánica" {{ old('tipo') == 'Mecánica' ? 'selected' : '' }}>Mecánica</option>
                                    <option value="Tiempo y Espacio" {{ old('tipo') == 'Tiempo y Espacio' ? 'selected' : '' }}>Tiempo y Espacio</option>
                                    <option value="Unidades de empaque" {{ old('tipo') == 'Unidades de empaque' ? 'selected' : '' }}>Unidades de empaque</option>
                                    <option value="Múltiplos/Fracciones/Decimales" {{ old('tipo') == 'Múltiplos/Fracciones/Decimales' ? 'selected' : '' }}>Múltiplos/Fracciones/Decimales</option>
                                    <option value="Números enteros/Números/Ratios" {{ old('tipo') == 'Números enteros/Números/Ratios' ? 'selected' : '' }}>Números enteros/Números/Ratios</option>
                                    <option value="Unidades específicas de la industria" {{ old('tipo') == 'Unidades específicas de la industria' ? 'selected' : '' }}>Unidades específicas de la industria</option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>


                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <!-- Botón Guardar a la izquierda -->
                            <div>
                                <a href="{{ route('equivalencias.index') }}" class="btn btn-danger text-white">
                                    <i class="ri-close-line me-1"></i> Cancelar
                                </a>
                            </div>

                            <!-- Botón Cancelar a la derecha -->
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i> Guardar
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

<!-- Script para activar tooltips y validación condicional -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Ejecutar la validación al cargar la página para old values
    toggleTipoRequired();
});

function toggleTipoRequired() {
    const claveSat = document.getElementById('clave_sat');
    const tipo = document.getElementById('tipo');
    const tipoRequired = document.getElementById('tipo-required');

    if (claveSat.value.trim() !== '') {
        // Si hay texto en Clave SAT, hacer tipo obligatorio
        tipo.setAttribute('required', 'required');
        tipoRequired.style.display = 'inline';

        // Agregar evento de validación personalizada
        tipo.addEventListener('invalid', function() {
            if (this.value === '') {
                this.setCustomValidity('El campo tipo es obligatorio cuando se ingresa una Clave SAT.');
            } else {
                this.setCustomValidity('');
            }
        });

        tipo.addEventListener('input', function() {
            if (this.value !== '') {
                this.setCustomValidity('');
            }
        });
    } else {
        // Si no hay texto en Clave SAT, tipo no es obligatorio
        tipo.removeAttribute('required');
        tipoRequired.style.display = 'none';
        tipo.setCustomValidity('');
    }
}
</script>
