@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">

    {{-- Formularios DELETE ocultos --}}
    <form id="formDeleteLogo" action="{{ route('configuracion.negocio.deleteLogo') }}" method="POST">
        @csrf @method('DELETE')
    </form>
    <form id="formDeleteFavicon" action="{{ route('configuracion.negocio.deleteFavicon') }}" method="POST">
        @csrf @method('DELETE')
    </form>

   
    @if (session()->has('success'))
        <div id="alert-success" class="alert text-white bg-success d-flex align-items-center gap-2" role="alert">
            <i class="ri-checkbox-circle-line" style="font-size:1.3rem; flex-shrink:0;"></i>
            <div class="iq-alert-text ml-2">{{ session('success') }}</div>
            <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close">
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

    {{-- Encabezado --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1"><i class="ri-settings-3-line me-2 text-primary"></i>Configuración del Negocio</h3>
            <p class="text-muted mb-0">Administra la identidad visual y datos de tu negocio</p>
        </div>
    </div>

    <form action="{{ route('configuracion.negocio.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- Columna izquierda: datos --}}
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom d-flex align-items-center gap-2">
                        <i class="ri-store-2-line text-primary" style="font-size:1.2rem;"></i>
                        <h5 class="mb-0">Datos del Negocio</h5>
                    </div>
                    <div class="card-body">

                        {{-- Nombre --}}
                        <div class="form-group mb-4">
                            <label for="nombre_negocio" class="fw-semibold">
                                Nombre del Negocio <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ri-store-2-line"></i></span>
                                </div>
                                <input type="text"
                                       name="nombre_negocio"
                                       id="nombre_negocio"
                                       class="form-control @error('nombre_negocio') is-invalid @enderror"
                                       value="{{ old('nombre_negocio', $config->nombre_negocio) }}"
                                       placeholder="Ej. Pastelería San Juan"
                                       required>
                                @error('nombre_negocio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Teléfono --}}
                        <div class="form-group mb-2">
                            <label for="telefono" class="fw-semibold">Teléfono</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ri-phone-line"></i></span>
                                </div>
                                <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                    value="{{ old('telefono', $config->telefono) }}"
                                    placeholder="Ej. 9511234567"
                                    maxlength="10"
                                    oninput="this.value = this.value.replace(/\D/g, '').slice(0, 10)">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Columna derecha: imágenes --}}
            <div class="col-lg-5">

                {{-- Logo --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom d-flex align-items-center gap-2">
                        <i class="ri-image-line text-success" style="font-size:1.2rem;"></i>
                        <h5 class="mb-0">Logo</h5>
                    </div>
                    <div class="card-body text-center">

                        {{-- Preview logo --}}
                        <div class="mb-3 d-flex justify-content-center">
                            <div style="width:130px; height:130px; border-radius:12px; border:2px dashed #dee2e6;
                                        display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f8f9fa;">
                                @if($config->logo)
                                    <img src="{{ asset('storage/' . $config->logo) }}"
                                         alt="Logo"
                                         id="previewLogo"
                                         style="max-width:100%; max-height:100%; object-fit:contain;">
                                @else
                                    <div id="previewLogo" class="text-muted text-center">
                                        <i class="ri-image-add-line" style="font-size:2.5rem;"></i>
                                        <p class="small mb-0">Sin logo</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <input type="file"
                               name="logo"
                               id="inputLogo"
                               class="form-control mb-2 @error('logo') is-invalid @enderror"
                               accept="image/png,image/jpg,image/jpeg,image/webp"
                               onchange="previewImagen(event, 'previewLogo')">
                        <small class="text-muted d-block mb-2">PNG, JPG, WEBP. Máx 2MB.</small>
                        @error('logo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        @if($config->logo)
                            <button type="button" class="btn btn-sm btn-outline-danger mt-1"
                                    onclick="if(confirm('¿Eliminar el logo?')) document.getElementById('formDeleteLogo').submit()">
                                <i class="ri-delete-bin-line me-1"></i> Eliminar logo
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Favicon --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom d-flex align-items-center gap-2">
                        <i class="ri-bookmark-line text-warning" style="font-size:1.2rem;"></i>
                        <h5 class="mb-0">Favicon</h5>
                        <small class="text-muted">(ícono de pestaña)</small>
                    </div>
                    <div class="card-body text-center">

                        {{-- Preview favicon --}}
                        <div class="mb-3 d-flex justify-content-center">
                            <div style="width:80px; height:80px; border-radius:10px; border:2px dashed #dee2e6;
                                        display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f8f9fa;">
                                @if($config->favicon)
                                    <img src="{{ asset('storage/' . $config->favicon) }}"
                                         alt="Favicon"
                                         id="previewFavicon"
                                         style="max-width:100%; max-height:100%; object-fit:contain;">
                                @else
                                    <div id="previewFavicon" class="text-muted text-center">
                                        <i class="ri-bookmark-line" style="font-size:1.8rem;"></i>
                                        <p class="small mb-0">Sin favicon</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <input type="file"
                               name="favicon"
                               id="inputFavicon"
                               class="form-control mb-2 @error('favicon') is-invalid @enderror"
                               accept="image/png,image/x-icon,image/jpeg"
                               onchange="previewImagen(event, 'previewFavicon')">
                        <small class="text-muted d-block mb-2">PNG, ICO, JPG. Máx 512KB.</small>
                        @error('favicon')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        @if($config->favicon)
                            <button type="button" class="btn btn-sm btn-outline-danger mt-1"
                                    onclick="if(confirm('¿Eliminar el favicon?')) document.getElementById('formDeleteFavicon').submit()">
                                <i class="ri-delete-bin-line me-1"></i> Eliminar favicon
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- Botón guardar --}}
        <div class="d-flex justify-content-end mt-2 mb-4">
            <button type="submit" class="btn btn-primary px-4">
                <i class="ri-save-line me-1"></i> Guardar Cambios
            </button>
        </div>

    </form>
</div>

<script>
function previewImagen(event, targetId) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const target = document.getElementById(targetId);
        target.innerHTML = '';

        const img = document.createElement('img');
        img.src = e.target.result;
        img.style = 'max-width:100%; max-height:100%; object-fit:contain;';
        target.appendChild(img);
    };
    reader.readAsDataURL(file);
}
</script>
@endsection