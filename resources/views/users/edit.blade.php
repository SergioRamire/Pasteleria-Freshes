@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Editar Usuario "{{ old('username', $userData->username) }}"</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('users.update', $userData->username) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('put')

                        <!-- begin: Input Image -->
                        <div class="form-group row align-items-center">
                            <div class="col-md-12">
                                <div class="profile-img-edit">
                                    <div class="crm-profile-img-edit">
                                        <img class="crm-profile-pic rounded-circle avatar-100" id="image-preview" src="{{ asset('assets/images/user/1.png') }}" alt="profile-pic">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group mb-4 col-lg-6">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('photo') is-invalid @enderror" id="image" name="photo" accept="image/*" onchange="previewImage();">
                                    <label class="custom-file-label" for="photo">Elija archivo...</label>
                                </div>
                                @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- end: Input Image -->

                        <!-- begin: Input Data -->
                        <div class="row align-items-center">

                            <div class="form-group col-md-3">
                                <label for="name"><i class="ri-user-line me-1"></i> Nombre's <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', $userData->name) }}"
                                    required
                                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')"
                                >
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="apellido_p"><i class="ri-user-line me-1"></i> Apellido Paterno <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('apellido_p') is-invalid @enderror"
                                    id="apellido_p"
                                    name="apellido_p"
                                    value="{{ old('apellido_p', $userData->apellido_p) }}"
                                    required
                                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')"
                                >
                                @error('apellido_p')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="apellido_m"><i class="ri-user-line me-1"></i> Apellido Materno </span></label>
                                <input type="text"
                                    class="form-control @error('apellido_m') is-invalid @enderror"
                                    id="apellido_m"
                                    name="apellido_m"
                                    value="{{ old('apellido_m', $userData->apellido_m) }}"
                                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')"
                                >
                                @error('apellido_m')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="username"><i class="ri-user-3-line me-1"></i> Usuario <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('username') is-invalid @enderror"
                                    id="username"
                                    name="username"
                                    value="{{ old('username', $userData->username) }}"
                                    required
                                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')"
                                >
                                @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4 position-relative">
                                <label for="email"><i class="ri-mail-line me-1"></i> Correo Electrónico <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', $userData->email) }}"
                                    required
                                    autocomplete="off"
                                    pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$"
                                    title="Ingresa un correo válido que contenga '@' y termine en '.com'"
                                    oninput="showEmailSuggestions(this)"
                                    onblur="hideSuggestions()"
                                >
                                <div id="email-suggestions" class="list-group position-absolute" style="z-index: 1000; display:none;"></div>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="country_code"><i class="ri-global-line me-1"></i> País <span class="text-danger">*</span></label>
                                <select
                                    id="country_code"
                                    name="country_code"
                                    class="form-control @error('country_code') is-invalid @enderror"
                                    required
                                >
                                    <option value="+52" {{ old('country_code', $selectedCode) == '+52' ? 'selected' : '' }}>+52 México</option>
                                    <option value="+1" {{ old('country_code', $selectedCode) == '+1' ? 'selected' : '' }}>+1 USA / Canadá</option>
                                    <option value="+44" {{ old('country_code', $selectedCode) == '+44' ? 'selected' : '' }}>+44 Reino Unido</option>
                                    <option value="+34" {{ old('country_code', $selectedCode) == '+34' ? 'selected' : '' }}>+34 España</option>
                                    <!-- Agrega más códigos de país según necesites -->
                                </select>
                                @error('country_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="cellphone"><i class="ri-phone-line me-1"></i> Teléfono <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('cellphone') is-invalid @enderror"
                                    id="cellphone"
                                    name="cellphone"
                                    placeholder="Ej. 5512345678"
                                    required
                                    maxlength="15"
                                    pattern="^[0-9]{7,15}$"
                                    title="Solo números. De 7 a 15 dígitos."
                                    value="{{ old('cellphone', preg_replace('/^\+\d{1,3}/', '', $userData->cellphone)) }}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                >
                                @error('cellphone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="password"><i class="ri-lock-2-line me-1"></i> Contraseña <span class="text-danger">*</span></label>
                                <input
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                >
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="password_confirmation"><i class="ri-shield-keyhole-line me-1"></i> Confirmar Contraseña <span class="text-danger">*</span></label>
                                <input
                                    type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                >
                                @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="estado"><i class="ri-toggle-line me-1"></i> Estado <span class="text-danger">*</span></label>
                                <select class="form-control @error('estado') is-invalid @enderror" name="estado" id="estado" required">
                                    <option value="" disabled selected hidden>-- Seleccione --</option>
                                    <option value="1" {{ old('estado', $userData->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('estado', $userData->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="role"><i class="ri-shield-user-line me-1"></i> Rol <span class="text-danger">*</span></label>
                                <select class="form-control @error('role') is-invalid @enderror" name="role" id="role" required>
                                    <option value="" disabled selected hidden>-- Selecciona el Role --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ $userData->hasRole($role->name) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="branche_id"><i class="ri-store-2-line me-1"></i> Sucursal <span class="text-danger">*</span></label>                                <select class="form-control @error('branche_id') is-invalid @enderror" name="branche_id" id="branche_id" required>
                                    <option value="" disabled selected hidden>Selecciona Una Sucursal</option>
                                    @foreach (\App\Models\Branche::all() as $sucursal)
                                        <option value="{{ $sucursal->id }}" {{ old('branche_id', $userData->branche_id) == $sucursal->id ? 'selected' : '' }}>
                                            {{ $sucursal->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branche_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                        <!-- end: Input Data -->

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <!-- Botón Cancelar a la derecha -->
                            <div>
                                <a href="{{ route('users.index') }}" class="btn btn-danger text-white">
                                    <i class="ri-close-line me-1"></i> Cancelar
                                </a>
                            </div>

                            <!-- Botón Actualizar a la izquierda -->
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-refresh-line me-1"></i> Actualizar
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cellphoneInput = document.getElementById('cellphone');
        const countryCodeSelect = document.getElementById('country_code');

        // Solo permitir números en el teléfono
        cellphoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Cambiar placeholder, maxlength y pattern según país seleccionado
        countryCodeSelect.addEventListener('change', function () {
            switch (this.value) {
                case '+52':
                    cellphoneInput.placeholder = 'Ej. 5512345678';
                    cellphoneInput.maxLength = 10;
                    cellphoneInput.pattern = "^[0-9]{10}$";
                    cellphoneInput.title = "Solo números. Exactamente 10 dígitos para México.";
                    break;
                case '+1':
                    cellphoneInput.placeholder = 'Ej. 5551234567';
                    cellphoneInput.maxLength = 10;
                    cellphoneInput.pattern = "^[0-9]{10}$";
                    cellphoneInput.title = "Solo números. Exactamente 10 dígitos para USA/Canadá.";
                    break;
                case '+44':
                    cellphoneInput.placeholder = 'Ej. 7123456789';
                    cellphoneInput.maxLength = 10;
                    cellphoneInput.pattern = "^[0-9]{10}$";
                    cellphoneInput.title = "Solo números. Exactamente 10 dígitos para Reino Unido.";
                    break;
                case '+34':
                    cellphoneInput.placeholder = 'Ej. 612345678';
                    cellphoneInput.maxLength = 9;
                    cellphoneInput.pattern = "^[0-9]{9}$";
                    cellphoneInput.title = "Solo números. Exactamente 9 dígitos para España.";
                    break;
                default:
                    cellphoneInput.placeholder = 'Número telefónico';
                    cellphoneInput.maxLength = 15;
                    cellphoneInput.pattern = "^[0-9]{7,15}$";
                    cellphoneInput.title = "Solo números. De 7 a 15 dígitos.";
            }
        });

        // Disparar cambio para aplicar validaciones al cargar la página
        countryCodeSelect.dispatchEvent(new Event('change'));
    });

    // Funciones para las sugerencias de email
    const commonDomains = ['gmail.com', 'outlook.com', 'yahoo.com', 'hotmail.com', 'live.com'];

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
                input.dispatchEvent(new Event('input')); // Revalidar campo
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

     document.addEventListener('DOMContentLoaded', function () {
        const sucursalSelect = document.getElementById('branche_id');
        const roleSelect = document.getElementById('role');

        function cargarRoles(sucursalId, selectedRoleId = null) {
            if (sucursalId) {
                fetch(`/roles-disponibles/${sucursalId}`)
                    .then(response => response.json())
                    .then(data => {
                        roleSelect.innerHTML = '<option value="" disabled hidden>-- Selecciona el Role --</option>';
                        roleSelect.disabled = false;

                        data.forEach(role => {
                            const option = document.createElement('option');
                            option.value = role.id;
                            option.textContent = role.name;

                            if (selectedRoleId && selectedRoleId == role.id) {
                                option.selected = true;
                            }

                            roleSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al obtener los roles:', error);
                        roleSelect.innerHTML = '<option value="">Error al cargar roles</option>';
                        roleSelect.disabled = true;
                    });
            } else {
                roleSelect.innerHTML = '<option value="" disabled hidden>-- Primero selecciona una sucursal --</option>';
                roleSelect.disabled = true;
            }
        }

        // Obtener valores actuales del usuario (Laravel los inyecta)
        const selectedSucursal = sucursalSelect.value;
        const selectedRoleId = "{{ old('role', optional($userData->roles->first())->id) }}";

        // Cargar roles al iniciar la página
        cargarRoles(selectedSucursal, selectedRoleId);

        // Cargar roles al cambiar de sucursal
        sucursalSelect.addEventListener('change', function () {
            cargarRoles(this.value);
        });
    });
</script>
@endsection
