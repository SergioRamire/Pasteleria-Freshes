@extends('dashboard.body.main')

@section('container')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Editar Cliente</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group row align-items-center">
                            <div class="col-md-12">
                                <div class="profile-img-edit">
                                    <div class="crm-profile-img-edit">
                                        <img class="crm-profile-pic rounded-circle avatar-100" id="image-preview"
                                            src="{{ $customer->photo ? asset('storage/customers/' . $customer->photo) : asset('assets/images/user/1.png') }}"
                                            alt="profile-pic">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group mb-4 col-lg-6">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('photo') is-invalid @enderror" id="image" name="photo" accept="image/*" onchange="previewImage();">
                                    <label class="custom-file-label" for="photo">Cambiar imagen</label>
                                </div>
                                @error('photo')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="name"><i class="ri-user-3-line me-1"></i> Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="name"
                                    name="name"
                                    maxlength="50"
                                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}"
                                    title="Solo letras y espacios. Mínimo 2 y máximo 50 caracteres."
                                    value="{{ old('name', $customer->name) }}"
                                    required
                                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '').slice(0, 50)">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="shopname"><i class="ri-building-line me-1"></i> Nombre Empresa</label>
                                <input type="text"
                                    class="form-control @error('shopname') is-invalid @enderror"
                                    id="shopname"
                                    name="shopname"
                                    maxlength="50"
                                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}"
                                    title="Solo letras y espacios. Mínimo 2 y máximo 50 caracteres."
                                    value="{{ old('shopname', $customer->shopname) }}"
                                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '').slice(0, 50)">
                                @error('shopname') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="type_customer"><i class="ri-user-star-line me-1"></i> Tipo de cliente <span class="text-danger">*</span></label>
                                <select class="form-control @error('type_customer') is-invalid @enderror" name="type_customer" required>
                                    <option value="" disabled selected hidden>Seleccione tipo...</option>
                                    <option value="normal" {{ old('type_customer', $customer->type_customer) == 'normal' ? 'selected' : '' }}>General</option>
                                    <option value="distribuidor" {{ old('type_customer', $customer->type_customer) == 'distribuidor' ? 'selected' : '' }}>Mayoreo</option>
                                </select>
                                @error('type_customer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group col-md-4 position-relative">
                                <label for="email"><i class="ri-mail-line me-1"></i> Correo Electrónico <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', $customer->email ?? '') }}"
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

                            <div class="form-group col-md-4">
                                <label for="phone"><i class="ri-phone-line me-1"></i> Teléfono <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                    value="{{ old('phone', $customer->phone) }}" required maxlength="10" pattern="\d{10}"
                                    title="El número de teléfono debe tener exactamente 10 dígitos"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="phone2"><i class="ri-phone-line me-1"></i> Teléfono 2 (Opcional)</label>
                                <input type="text" class="form-control @error('phone2') is-invalid @enderror" id="phone2" name="phone2"
                                    value="{{ old('phone2', $customer->phone2) }}" maxlength="10" pattern="\d{10}"
                                    title="El número de teléfono debe tener exactamente 10 dígitos"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                                @error('phone2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5><i class="fa fa-map-marker-alt text-primary"></i> Dirección Del Cliente</h5>
                                <hr>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="calle"><i class="ri-road-map-line me-1"></i> Calle <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('calle') is-invalid @enderror" id="calle" name="calle" value="{{ old('calle', $customer->calle) }}" required>
                                @error('calle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="num_exterior"><i class="ri-community-line me-1"></i> Número Exterior <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('num_exterior') is-invalid @enderror" id="num_exterior" name="num_exterior" value="{{ old('num_exterior', $customer->num_exterior) }}" required>
                                @error('num_exterior')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="num_interior"><i class="ri-home-8-line me-1"></i> Número Interior</label>
                                <input type="text" class="form-control @error('num_interior') is-invalid @enderror" id="num_interior" name="num_interior" value="{{ old('num_interior', $customer->num_interior) }}">
                                @error('num_interior')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="cp"><i class="ri-mail-send-line me-1"></i> C.P. <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('cp') is-invalid @enderror"
                                    id="cp"
                                    name="cp"
                                    maxlength="5"
                                    pattern="\d{5,5}"
                                    title="El código postal debe tener exactamente 5 dígitos"
                                    value="{{ old('cp', $customer->cp) }}"
                                    required
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5)">
                                @error('cp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="colonia"><i class="ri-map-pin-2-line me-1"></i> Colonia <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('colonia') is-invalid @enderror" id="colonia" name="colonia" value="{{ old('colonia', $customer->colonia) }}" required>
                                @error('colonia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="municipio"><i class="ri-map-pin-line me-1"></i> Municipio <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('municipio') is-invalid @enderror" id="municipio" name="municipio" value="{{ old('municipio', $customer->municipio) }}" required>
                                @error('municipio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="estado"><i class="ri-map-line me-1"></i> Estado <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('estado') is-invalid @enderror" id="estado" name="estado" value="{{ old('estado', $customer->estado) }}" required>
                                @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="pais"><i class="ri-global-line me-1"></i> País <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('pais') is-invalid @enderror" id="pais" name="pais" value="{{ old('pais', $customer->pais) }}" required readonly>
                                @error('pais')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="latitud"><i class="ri-map-pin-5-line me-1"></i> Latitud <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('latitud') is-invalid @enderror" id="latitud" name="latitud" value="{{ old('latitud', $customer->latitud) }}" required readonly>
                                @error('latitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="longitud"><i class="ri-map-pin-5-line me-1"></i> Longitud <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('longitud') is-invalid @enderror" id="longitud" name="longitud" value="{{ old('longitud', $customer->longitud) }}" required readonly>
                                @error('longitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group col-md-12">
                                <label for="rul_maps"><i class="fas fa-map-marker-alt text-success me-1"></i> Enlace Generado de Google Maps <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('rul_maps') is-invalid @enderror" id="rul_maps" name="rul_maps" value="{{ old('rul_maps', $customer->rul_maps) }}" required readonly>
                                @error('rul_maps')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group col-md-12 mt-2">
                                <label><i class="ri-map-2-line me-1"></i> Ubicación en Mapa (Arrastra el marcador o haz clic para precisar):</label>
                                <div id="map" style="width: 100%; height: 380px; border-radius: 8px; border: 1px solid #ccc; z-index: 1;"></div>
                            </div>

                            <div class="form-group col-md-12 mt-2">
                                <label for="referencia"><i class="fa fa-thumbtack text-success me-1"></i> Referencias</label>
                                <input type="text" class="form-control @error('referencia') is-invalid @enderror" id="referencia" name="referencia" value="{{ old('referencia', $customer->referencia) }}">
                                @error('referencia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mb-2 mt-3">
                            <button type="button" class="btn btn-success" id="toggle-banco">
                                <i class="ri-bank-line mr-1"></i>
                                <span id="toggle-banco-text">Editar datos Fiscales</span>
                            </button>
                        </div>

                        <div id="datosBancarios" style="display: none;">
                            <hr>
                            <h5>Datos Fiscales</h5>
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="rfc"><i class="ri-file-user-line me-1"></i> RFC con Homoclave <span class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('rfc') is-invalid @enderror"
                                        id="rfc"
                                        name="rfc"
                                        maxlength="13"
                                        pattern="[A-Z0-9ÁÉÍÓÚáéíóúÑñ\s]{2,50}"
                                        title="Solo letras y espacios. Exactamente 13 caracteres."
                                        value="{{ old('rfc', $customer->rfc) }}"
                                        oninput="this.value = this.value.replace(/[^A-Z0-9ÁÉÍÓÚáéíóúÑñ\s]/g, '').slice(0, 50)">
                                    @error('rfc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="tipo_persona"><i class="ri-user-settings-line me-1"></i> Tipo de Persona <span class="text-danger">*</span></label>
                                    <select class="form-control @error('tipo_persona') is-invalid @enderror" name="tipo_persona">
                                        <option value="" disabled selected hidden>Seleccione tipo...</option>
                                        <option value="Persona Física" {{ old('tipo_persona', $customer->tipo_persona) == 'Persona Física' ? 'selected' : '' }}>Persona Física</option>
                                        <option value="Persona Moral" {{ old('tipo_persona', $customer->tipo_persona) == 'Persona Moral' ? 'selected' : '' }}>Persona Moral</option>
                                    </select>
                                    @error('tipo_persona') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="regimen_fiscal"><i class="ri-user-settings-line me-1"></i> Régimen Fiscal <span class="text-danger">*</span></label>
                                    <select name="regimen_fiscal" class="form-control @error('regimen_fiscal') is-invalid @enderror" >
                                        <option value="" disabled hidden selected>Seleccione un régimen fiscal...</option>
                                        
                                        @foreach(\App\Models\RegimenFiscal::orderBy('code', 'asc')->get() as $regimen)
                                            @php
                                                // Concatenamos de forma directa para asegurar la renderización del string entero
                                                $regimenCompleto = $regimen->code . ' - ' . $regimen->description;
                                            @endphp
                                            <option value="{{ $regimenCompleto }}" {{ old('regimen_fiscal', $customer->regimen_fiscal) == $regimenCompleto ? 'selected' : '' }}>
                                                {{ $regimenCompleto }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('regimen_fiscal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="uso_cfdi"><i class="ri-file-text-line me-1"></i> Uso de CFDI <span class="text-danger">*</span></label>
                                    <select class="form-control @error('uso_cfdi') is-invalid @enderror" name="uso_cfdi" >
                                        <option value="" disabled hidden selected>Seleccione uso...</option>
                                        
                                        @foreach(\App\Models\UsoCfdi::orderBy('code', 'asc')->get() as $uso)
                                            @php
                                                // Armamos la cadena exacta aquí para asegurar que no pinte en blanco
                                                $textoCompleto = $uso->code . ' - ' . $uso->description;
                                            @endphp
                                            <option value="{{ $textoCompleto }}" {{ old('uso_cfdi', $customer->uso_cfdi) == $textoCompleto ? 'selected' : '' }}>
                                                {{ $textoCompleto }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('uso_cfdi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <a href="{{ route('customers.index') }}" class="btn btn-danger text-white" title="Cancelar edición y regresar al listado de clientes">
                                    <i class="ri-close-line me-1"></i> Cancelar
                                </a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary" title="Guardar cambios realizados al cliente">
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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    // ==============================================================
    // 1. TOGGLE DATOS FISCALES
    // ==============================================================
    document.getElementById('toggle-banco').addEventListener('click', function () {
        const seccion = document.getElementById('datosBancarios');
        const texto = document.getElementById('toggle-banco-text');
        const estaVisible = seccion.style.display === 'block';
        seccion.style.display = estaVisible ? 'none' : 'block';
        texto.textContent = estaVisible ? 'Editar datos Fiscales' : 'Ocultar datos Fiscales';
    });

    // ==============================================================
    // 2. SUGERENCIAS DE CORREO
    // ==============================================================
    const commonDomains = ['gmail.com', 'outlook.com', 'yahoo.com', 'hotmail.com', 'live.com', 'acuario.com'];

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
                input.dispatchEvent(new Event('input'));
            };
            suggestionBox.appendChild(item);
        });
        suggestionBox.style.width = input.offsetWidth + 'px';
        suggestionBox.style.display = 'block';
    }

    function hideSuggestions() {
        setTimeout(() => {
            document.getElementById('email-suggestions').style.display = 'none';
        }, 200);
    }

    // ==============================================================
    // 3. MAPA CON GEOCÓDIGO INVERSO (RELLENADO AUTOMÁTICO)
    // ==============================================================
    document.addEventListener('DOMContentLoaded', () => {
        // Coordenadas iniciales desde el cliente o por defecto
        let initialLat = parseFloat(document.getElementById('latitud').value) || 17.0732;
        let initialLng = parseFloat(document.getElementById('longitud').value) || -96.7266;

        // Inicializar mapa
        const map = L.map('map').setView([initialLat, initialLng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Marcador arrastrable
        const marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

        // Actualizar campos de coordenadas y enlace de Google Maps
        function updateCoordinates(lat, lng) {
            document.getElementById('latitud').value = lat.toFixed(6);
            document.getElementById('longitud').value = lng.toFixed(6);
            document.getElementById('rul_maps').value = `https://www.google.com/maps?q=${lat.toFixed(6)},${lng.toFixed(6)}`;
        }

        // Geocodificación inversa usando Nominatim (OpenStreetMap)
        let reverseGeocodeTimeout = null;

        async function reverseGeocode(lat, lng) {
            const addressFields = ['calle', 'num_exterior', 'colonia', 'municipio', 'estado', 'cp', 'pais'];
            // Cambiar cursor a "wait"
            addressFields.forEach(field => {
                const el = document.getElementById(field);
                if (el) el.style.cursor = 'wait';
            });

            try {
                const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
                const response = await fetch(url, {
                    headers: { 'User-Agent': 'LaravelCustomerApp/1.0' }
                });
                const data = await response.json();

                if (data && data.address) {
                    const addr = data.address;
                    document.getElementById('calle').value = addr.road || addr.pedestrian || addr.path || addr.footway || '';
                    document.getElementById('num_exterior').value = addr.house_number || '';
                    document.getElementById('colonia').value = addr.suburb || addr.neighbourhood || addr.quarter || addr.city_district || '';
                    document.getElementById('municipio').value = addr.city || addr.town || addr.municipality || addr.village || '';
                    document.getElementById('estado').value = addr.state || '';
                    document.getElementById('cp').value = addr.postcode || '';
                    document.getElementById('pais').value = addr.country || '';
                } else {
                    console.warn('No se obtuvo dirección para estas coordenadas');
                }
            } catch (error) {
                console.error('Error en geocodificación inversa:', error);
            } finally {
                addressFields.forEach(field => {
                    const el = document.getElementById(field);
                    if (el) el.style.cursor = '';
                });
            }
        }

        // Llamada con debounce para no saturar la API
        function onLocationChange(lat, lng) {
            updateCoordinates(lat, lng);
            if (reverseGeocodeTimeout) clearTimeout(reverseGeocodeTimeout);
            reverseGeocodeTimeout = setTimeout(() => {
                reverseGeocode(lat, lng);
            }, 500);
        }

        // Eventos del mapa
        marker.on('dragend', function (e) {
            const pos = marker.getLatLng();
            onLocationChange(pos.lat, pos.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            onLocationChange(e.latlng.lat, e.latlng.lng);
        });

        // Si ya existían coordenadas y los campos de dirección están vacíos, rellenar automáticamente
        const calleActual = document.getElementById('calle').value.trim();
        const estadoActual = document.getElementById('estado').value.trim();
        if (initialLat && initialLng && (calleActual === '' || estadoActual === '')) {
            reverseGeocode(initialLat, initialLng);
        }

        // Asegurar que los campos de coordenadas y enlace se muestren correctamente
        updateCoordinates(initialLat, initialLng);
    });

    // ==============================================================
    // 4. LISTA DE PAÍSES (RESTCOUNTRIES) Y ESTADOS DE MÉXICO
    // ==============================================================
    document.addEventListener('DOMContentLoaded', async () => {
        // Países
        try {
            const response = await fetch('https://restcountries.com/v3.1/all');
            const countries = await response.json();
            const paisInput = document.getElementById('pais');
            const datalist = document.createElement('datalist');
            datalist.id = 'paises-list';
            countries.sort((a, b) => a.name.common.localeCompare(b.name.common))
                .forEach(country => {
                    const option = document.createElement('option');
                    option.value = country.name.common;
                    datalist.appendChild(option);
                });
            document.body.appendChild(datalist);
            paisInput.setAttribute('list', 'paises-list');
        } catch (error) {
            console.error('Error cargando países:', error);
        }

        // Estados de México (para autocompletar)
        const estados = [
            "Aguascalientes", "Baja California", "Baja California Sur", "Campeche",
            "Chiapas", "Chihuahua", "Ciudad de México", "Coahuila", "Colima", "Durango",
            "Estado de México", "Guanajuato", "Guerrero", "Hidalgo", "Jalisco", "Michoacán",
            "Morelos", "Nayarit", "Nuevo León", "Oaxaca", "Puebla", "Querétaro", "Quintana Roo",
            "San Luis Potosí", "Sinaloa", "Sonora", "Tabasco", "Tamaulipas", "Tlaxcala",
            "Veracruz", "Yucatán", "Zacatecas"
        ];
        const estadoInput = document.getElementById('estado');
        const datalistEstados = document.createElement('datalist');
        datalistEstados.id = 'estados-list';
        estados.forEach(estado => {
            const option = document.createElement('option');
            option.value = estado;
            datalistEstados.appendChild(option);
        });
        document.body.appendChild(datalistEstados);
        estadoInput.setAttribute('list', 'estados-list');
    });
</script>

@include('components.preview-img-form')
@endsection