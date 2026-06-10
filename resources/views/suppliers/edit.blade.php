@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Editar Proveedor {{ old('name', $supplier->name) }}</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('put')

                        {{-- Imagen del proveedor --}}
                        <div class="form-group text-center">
                            <label>Foto del proveedor</label>
                            <div class="profile-img-edit mb-3">
                                <img id="image-preview" src="{{ $supplier->photo ? asset('storage/suppliers/'.$supplier->photo) : asset('assets/images/user/1.png') }}" class="rounded-circle avatar-100" alt="Foto del proveedor">
                            </div>
                            <div class="custom-file w-50 mx-auto">
                                <input type="file" class="custom-file-input @error('photo') is-invalid @enderror" id="image" name="photo" accept="image/*" onchange="previewImage();">
                                <label class="custom-file-label" for="photo">Elija el archivo</label>
                                @error('photo')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Información general --}}
                        <div class="row">
                            {{-- RFC --}}
                            <div class="form-group col-md-3">
                                <label for="rfc"><i class="ri-file-user-line me-1"></i> RFC con Homoclave <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('rfc') is-invalid @enderror"
                                    id="rfc"
                                    name="rfc"
                                    maxlength="13"
                                    title="Seleccione un tipo de persona para validar el RFC"
                                    value="{{ old('rfc', $supplier->rfc) }}"
                                    required
                                    oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9Ñ&]/g, '').slice(0,13)">
                                @error('rfc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-2">
                                <label for="tipo_persona"><i class="ri-user-settings-line me-1"></i> Tipo Persona <span class="text-danger">*</span></label>
                                <select class="form-control @error('tipo_persona') is-invalid @enderror" name="tipo_persona" id="tipo_persona" onchange="actualizarValidacionRFC()" required>
                                    <option value="" disabled selected hidden>Seleccione tipo...</option>
                                    <option value="Persona Fisica" {{ (old('tipo_persona') ?? $supplier->tipo_persona) == 'Persona Fisica' ? 'selected' : '' }}>Persona Física</option>
                                    <option value="Persona Moral" {{ (old('tipo_persona') ?? $supplier->tipo_persona) == 'Persona Moral' ? 'selected' : '' }}>Persona Moral</option>
                                </select>
                                @error('tipo_persona')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-7">
                                <label for="name"><i class="ri-user-3-line me-1"></i> Nombre Empresa <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="name"
                                    name="name"
                                    maxlength="50"
                                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}"
                                    title="Solo letras y espacios. Mínimo 2 y máximo 50 caracteres."
                                    value="{{ old('name', $supplier->name) }}"
                                    required
                                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '').slice(0, 50)">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group col-md-5">
                                <label for="shopname"><i class="ri-building-line me-1"></i> Nombre Completo del Responsable <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('shopname') is-invalid @enderror" id="shopname" name="shopname"
                                    value="{{ old('shopname', $supplier->shopname) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}"
                                    title="Solo letras y espacios, mínimo 2 y máximo 50 caracteres">
                                @error('shopname') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group col-md-4 position-relative">
                                <label for="email"><i class="ri-mail-line me-1"></i> Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                    value="{{ old('email', $supplier->email) }}"
                                    pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                    title="Ingresa un correo válido que contenga '@' y termine en '.com'"
                                    oninput="showEmailSuggestions(this)"
                                    onblur="hideSuggestions()"
                                >
                                <div id="email-suggestions" class="list-group position-absolute" style="z-index: 1000; display:none;"></div>

                                @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <script>
                                    const commonDomains = ['gmail.com', 'outlook.com', 'yahoo.com', 'hotmail.com', 'live.com','acuario.com'];

                                    function showEmailSuggestions(input) {
                                        const value = input.value;
                                        const suggestionBox = document.getElementById('email-suggestions');

                                        const atPosition = value.indexOf('@');
                                        if (atPosition === -1) {
                                            suggestionBox.style.display = 'none';
                                            return;
                                        }

                                        const typedDomain = value.slice(atPosition + 1).toLowerCase();
                                        if (typedDomain.length === 0) {
                                            suggestionBox.style.display = 'none';
                                            return;
                                        }

                                        const prefix = value.slice(0, atPosition + 1);

                                        const filtered = commonDomains.filter(domain => domain.startsWith(typedDomain));
                                        if (filtered.length === 0) {
                                            suggestionBox.style.display = 'none';
                                            return;
                                        }

                                        suggestionBox.innerHTML = '';
                                        filtered.forEach(domain => {
                                            const item = document.createElement('a');
                                            item.href = '#';
                                            item.className = 'list-group-item list-group-item-action';
                                            item.textContent = prefix + domain;
                                            item.onclick = function(e) {
                                                e.preventDefault();
                                                input.value = this.textContent;
                                                suggestionBox.style.display = 'none';
                                                input.dispatchEvent(new Event('input')); // <-- esto asegura que HTML5 revalide el campo
                                            };
                                            suggestionBox.appendChild(item);
                                        });

                                        suggestionBox.style.width = input.offsetWidth + 'px';
                                        suggestionBox.style.display = 'block';
                                    }

                                    function hideSuggestions() {
                                        setTimeout(() => {
                                            const suggestionBox = document.getElementById('email-suggestions');
                                            suggestionBox.style.display = 'none';
                                        }, 200);
                                    }
                            </script>

                            <div class="form-group col-md-3">
                                <label for="phone"><i class="ri-phone-line me-1"></i> Teléfono <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                    value="{{ old('phone', $supplier->phone) }}" required maxlength="10" pattern="^\d{10}$"
                                    title="El número de teléfono debe tener exactamente 10 dígitos numéricos"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="type"><i class="ri-user-star-line me-1"></i>Tipo Proveedor <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" name="type" required>
                                    <option value="" disabled selected hidden>Seleccione tipo...</option>
                                    <option value="Local" @if(old('type', $supplier->type) == 'Local') selected @endif>Local</option>
                                    <option value="Mayorista" @if(old('type', $supplier->type) == 'Mayorista') selected @endif>Mayorista</option>
                                    <option value="Distributor" @if(old('type', $supplier->type) == 'Distributor') selected @endif>Distributor</option>
                                    <option value="Internacional" @if(old('type', $supplier->type) == 'Internacional') selected @endif>Internacional</option>
                                    <option value="Vendedor Completo" @if(old('type', $supplier->type) == 'Vendedor Completo') selected @endif>Vendedor Completo</option>
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="city"><i class="ri-road-map-line me-1"></i> Ciudad <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city"
                                    value="{{ old('city', $supplier->city) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s,.]{2,50}"
                                    title="La ciudad debe tener solo letras y espacios, con un mínimo de 2 caracteres">
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Dirección --}}
                        <div class="form-group">
                            <label for="rul_maps">
                                <a href="https://maps.google.com.mx/" target="_blank" style="text-decoration: none; color: inherit;" class="me-1">
                                    <i class="fas fa-map-marker-alt text-success"></i>
                                </a>
                                <a href="https://maps.google.com.mx/" target="_blank" style="text-decoration: none; color: inherit;" class="me-1">
                                    <i class="fab fa-google text-success"></i>
                                </a>
                                <a href="https://maps.google.com.mx/" target="_blank" style="text-decoration: none; color: inherit;">
                                    Dirección Google Maps
                                </a>
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="3" required>{{ old('address', $supplier->address) }}</textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Botón para mostrar/ocultar los datos bancarios --}}
                        <div class="d-flex justify-content-end mb-2">
                            <button type="button" class="btn btn-success" id="toggle-banco">
                                <i class="ri-bank-line mr-1"></i>
                                <span id="toggle-banco-text">Mostrar datos de cuenta bancaria</span>
                            </button>
                        </div>

                        {{-- Sección de cuenta bancaria --}}
                        <div id="datosBancarios" style="display: none;">
                            <hr>
                            <h5 class="mb-3">Datos de cuenta bancaria</h5>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="account_holder"><i class="ri-user-3-line me-1"></i> Titular de la cuenta bancaria</label>
                                    <input type="text"
                                        class="form-control @error('account_holder') is-invalid @enderror"
                                        id="account_holder"
                                        name="account_holder"
                                        maxlength="50"
                                        pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}"
                                        title="Solo letras y espacios. Mínimo 2 y máximo 50 caracteres."
                                        value="{{ old('account_holder', $supplier->account_holder) }}"
                                        oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '').slice(0, 50)">
                                    @error('account_holder') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="bank_name"><i class="ri-bank-line me-1"></i> Nombre del Banco</label>
                                    <select class="form-control @error('bank_name') is-invalid @enderror" name="bank_name">
                                    <option value="" disabled selected hidden>Seleccione banco...</option>
                                        @php
                                            $bancos = [
                                                'BBVA México', 'Banorte', 'Santander México', 'Banamex (Citibanamex)',
                                                'HSBC México', 'Scotiabank Inverlat', 'Banco Azteca', 'Bancoppel'
                                            ];
                                        @endphp
                                        @foreach ($bancos as $banco)
                                            <option value="{{ $banco }}" {{ old('bank_name', $supplier->bank_name) == $banco ? 'selected' : '' }}>{{ $banco }}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="account_number"><i class="ri-bank-card-line me-1"></i> Número de cuenta</label>
                                    <input type="text"
                                        class="form-control @error('account_number') is-invalid @enderror"
                                        id="account_number"
                                        name="account_number"
                                        maxlength="18"
                                        pattern="^\d{16,18}$"
                                        title="El número de cuenta debe tener entre 16 y 18 dígitos numéricos"
                                        value="{{ old('account_number', $supplier->account_number) }}"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 18)">
                                    @error('account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <!-- Botón Cancelar a la derecha -->
                            <div>
                                <a href="{{ route('suppliers.index') }}" class="btn btn-danger text-white">
                                    <i class="ri-close-line me-1"></i> Cancelar
                                </a>
                            </div>
                            <!-- Botón Guardar a la izquierda -->
                            <div>
                                <button type="submit" class="btn btn-primary" title="Guardar cambios realizados al proveedor">
                                    <i class="ri-refresh-line me-1"></i> Actualizar
                                </button>
                            </div>

                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggle-banco').addEventListener('click', function () {
        const seccion = document.getElementById('datosBancarios');
        const texto = document.getElementById('toggle-banco-text');

        const estaVisible = seccion.style.display === 'block';

        seccion.style.display = estaVisible ? 'none' : 'block';
        texto.textContent = estaVisible ? 'Mostrar datos de cuenta bancaria' : 'Ocultar datos de cuenta bancaria';
    });

    // Validar RFC dinámicamente según tipo de persona
    function actualizarValidacionRFC() {
        const tipoPersona = document.getElementById('tipo_persona').value;
        const rfcInput = document.getElementById('rfc');

        if (tipoPersona === 'Persona Fisica') {
            rfcInput.pattern = "^[A-ZÑ&]{4}\\d{6}[A-Z0-9]{3}$";
            rfcInput.title = "RFC válido para Persona Física: 13 caracteres (4 letras + fecha + 3 alfanuméricos)";
        } else if (tipoPersona === 'Persona Moral') {
            rfcInput.pattern = "^[A-ZÑ&]{3}\\d{6}[A-Z0-9]{3}$";
            rfcInput.title = "RFC válido para Persona Moral: 12 caracteres (3 letras + fecha + 3 alfanuméricos)";
        } else {
            rfcInput.pattern = "";
            rfcInput.title = "Seleccione un tipo de persona para validar el RFC";
        }
    }

    window.addEventListener('DOMContentLoaded', actualizarValidacionRFC);
</script>

@include('components.preview-img-form')
@endsection
