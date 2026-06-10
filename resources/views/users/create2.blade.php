@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Crear Usuario</h4>
                    </div>
                </div>


                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
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
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <!-- end: Input Image -->
                        <!-- begin: Input Data -->
                        <div class=" row align-items-center">
                            <div class="form-group col-md-6">
                                <label for="name">Nombre <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')">
                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="apellido_p">Apellido Paterno <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('apellido_p') is-invalid @enderror"
                                    id="apellido_p"
                                    name="apellido_p"
                                    value="{{ old('apellido_p') }}"
                                    required
                                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')"
                                >
                                @error('apellido_p')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="apellido_m">Apellido Materno </span></label>
                                <input type="text"
                                    class="form-control @error('apellido_m') is-invalid @enderror"
                                    id="apellido_m"
                                    name="apellido_m"
                                    value="{{ old('apellido_m') }}"
                                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')"
                                >
                                @error('apellido_m')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="username">Usuario <span class="text-danger">*</span></label>

                                <input type="text"
                                    class="form-control @error('username') is-invalid @enderror"
                                    id="username"
                                    name="username"
                                    value="{{ old('username') }}"
                                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')"
                                >
                                @error('username')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>


                            <div class="form-group col-md-6">
                                <label for="country_code">País <span class="text-danger">*</span></label>
                                <select id="country_code" name="country_code" class="form-control" required>
                                    <option value="">Cargando países...</option>
                                </select>
                            </div>

                           <div class="form-group col-md-6">
                                <label for="cellphone">Teléfono <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control @error('cellphone') is-invalid @enderror"
                                        id="cellphone"
                                        name="cellphone"
                                        placeholder="Ej. 5512345678"
                                        value="{{ old('cellphone') }}"
                                        required
                                        maxlength="10"
                                        pattern="^[0-9]{7,10}$"
                                        title="Solo números. De 7 a 15 dígitos."
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    >
                                    @error('cellphone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const countrySelect = document.getElementById('country_code');

                                fetch('https://restcountries.com/v3.1/all?fields=name,idd')
                                    .then(res => res.json())
                                    .then(countries => {
                                        countries.sort((a, b) => a.name.common.localeCompare(b.name.common));
                                        countrySelect.innerHTML = '<option value="">Selecciona país</option>';

                                        countries.forEach(country => {
                                            if (country.idd && country.idd.root) {
                                                const root = country.idd.root;
                                                const suffix = (country.idd.suffixes && country.idd.suffixes.length > 0)
                                                    ? country.idd.suffixes[0]
                                                    : '';
                                                const phoneCode = root + suffix;
                                                const option = document.createElement('option');
                                                option.value = phoneCode;
                                                option.text = `${country.name.common} (${phoneCode})`;
                                                countrySelect.appendChild(option);
                                            }
                                        });
                                    })
                                    .catch(error => {
                                        countrySelect.innerHTML = '<option value="">Error al cargar países</option>';
                                        console.error('Error al obtener países:', error);
                                    });
                            });
                            </script>


                            <div class="form-group col-md-6 position-relative">
                                    <label for="email">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        class="form-control @error('email') is-invalid @enderror"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        autocomplete="off"
                                        pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$"
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


                            <div class="form-group col-md-6">
                                <label for="password">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required  autocomplete="off">
                                @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="password_confirmation">Confirmar contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                                @error('password_confirmation')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="branche_id">Sucursal</label>
                                <select class="form-control @error('branche_id') is-invalid @enderror" name="branche_id" id="branche_id" required>
                                    <option value="" selected disabled hidden>-- Selecciona una sucursal --</option>
                                    @foreach (\App\Models\Branche::all() as $sucursal)
                                        <option value="{{ $sucursal->id }}" {{ old('branche_id') == $sucursal->id ? 'selected' : '' }}>
                                            {{ $sucursal->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branche_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            @php
                                use Spatie\Permission\Models\Role;
                                $roles = Role::all();
                            @endphp

                            <div class="form-group col-md-6">
                                <label for="role">Role</label>
                                <select class="form-control @error('role') is-invalid @enderror" name="role" id="role" required>
                                    <option value="" selected disabled hidden>-- Selecciona el Role --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                        </div>
                        <!-- end: Input Data -->
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary mr-2">Guardar</button>
                            <a class="btn bg-danger" href="{{ route('users.index') }}">Cancelar</a>
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
