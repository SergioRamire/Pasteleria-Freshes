@extends('dashboard.body.main')

@section('specificpagestyles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endsection

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Editar {{ old('nombre', $sucursal->nombre) }}</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('sucursales.update', $sucursal->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row align-items-center">
                            <div class="form-group col-md-6">
                                <label for="nombre">
                                    <i class="ri-store-line me-1"></i> Nombre de la sucursal
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('nombre') is-invalid @enderror"
                                       id="nombre" name="nombre"
                                       value="{{ old('nombre', $sucursal->nombre) }}"
                                       required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="telefono">
                                    <i class="ri-phone-line me-1"></i> Teléfono
                                </label>
                                <input type="text"
                                       class="form-control @error('telefono') is-invalid @enderror"
                                       id="telefono" name="telefono"
                                       value="{{ old('telefono', $sucursal->telefono) }}"
                                       maxlength="10"
                                       oninput="this.value = this.value.replace(/\D/g,'').slice(0,10)" required>
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Dirección --}}
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5><i class="fa fa-map-marker-alt text-primary me-1"></i> Dirección de la Sucursal</h5>
                                <hr>
                            </div>

                            <div class="form-group col-md-8">
                                <label for="direccion">
                                    <i class="ri-road-map-line me-1"></i> Dirección completa
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('direccion') is-invalid @enderror"
                                       id="direccion" name="direccion"
                                       value="{{ old('direccion', $sucursal->direccion) }}"
                                       placeholder="Se rellena automáticamente al mover el mapa..."
                                       required>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="buscar_direccion">
                                    <i class="ri-search-line me-1"></i> Buscar en mapa
                                </label>
                                <div class="input-group">
                                    <input type="text"
                                           class="form-control"
                                           id="buscar_direccion"
                                           placeholder="Escribe y presiona Enter...">
                                    <button type="button" class="btn btn-primary" onclick="buscarEnMapa()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Coordenadas --}}
                            <div class="form-group col-md-3">
                                <label for="latitud">
                                    <i class="ri-map-pin-5-line me-1"></i> Latitud
                                </label>
                                <input type="text"
                                       class="form-control @error('latitud') is-invalid @enderror"
                                       id="latitud" name="latitud"
                                       value="{{ old('latitud', $sucursal->latitud) }}"
                                       readonly>
                                @error('latitud')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="longitud">
                                    <i class="ri-map-pin-5-line me-1"></i> Longitud
                                </label>
                                <input type="text"
                                       class="form-control @error('longitud') is-invalid @enderror"
                                       id="longitud" name="longitud"
                                       value="{{ old('longitud', $sucursal->longitud) }}"
                                       readonly>
                                @error('longitud')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="rul_maps">
                                    <i class="fas fa-map-marker-alt text-success me-1"></i> Enlace Google Maps
                                </label>
                                <input type="text"
                                       class="form-control @error('rul_maps') is-invalid @enderror"
                                       id="rul_maps" name="rul_maps"
                                       value="{{ old('rul_maps', $sucursal->rul_maps) }}"
                                       readonly>
                                @error('rul_maps')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Mapa --}}
                            <div class="form-group col-md-12 mt-2">
                                <label>
                                    <i class="ri-map-2-line me-1"></i>
                                    Ubicación en Mapa
                                    <small class="text-muted">(Arrastra el marcador, haz clic o busca una dirección)</small>
                                </label>
                                <div id="map" style="width:100%; height:400px; border-radius:10px; border:1px solid #dbeafe; z-index:1;"></div>
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle text-primary"></i>
                                    La dirección se rellena automáticamente al seleccionar una ubicación en el mapa.
                                </small>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a class="btn btn-danger text-white" href="{{ route('sucursales.index') }}">
                                <i class="ri-close-line me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-refresh-line me-1"></i> Actualizar
                            </button>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('specificpagescripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Coordenadas iniciales desde BD o default (Oaxaca)
    const latInicial = parseFloat(document.getElementById('latitud').value) || 17.0732;
    const lngInicial = parseFloat(document.getElementById('longitud').value) || -96.7266;

    // Inicializar mapa
    const map = L.map('map').setView([latInicial, lngInicial], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© <a href="https://www.openstreetmap.org/">OpenStreetMap</a>'
    }).addTo(map);

    // Marcador arrastrable
    const marker = L.marker([latInicial, lngInicial], { draggable: true }).addTo(map);

    // ── Actualizar campos de coordenadas y enlace ──
    function actualizarCoordenadas(lat, lng) {
        document.getElementById('latitud').value  = lat.toFixed(6);
        document.getElementById('longitud').value = lng.toFixed(6);
        document.getElementById('rul_maps').value = `https://www.google.com/maps?q=${lat.toFixed(6)},${lng.toFixed(6)}`;
    }

    // ── Geocodificación inversa: coordenadas → dirección ──
    let geocodeTimeout = null;

    async function geocodificacionInversa(lat, lng) {
        try {
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
            const response = await fetch(url, {
                headers: { 'Accept-Language': 'es' }
            });
            const data = await response.json();

            if (data && data.display_name) {
                // Rellenar campo de dirección con la dirección completa
                document.getElementById('direccion').value = data.display_name;
            }
        } catch (error) {
            console.error('Error en geocodificación inversa:', error);
        }
    }

    function alCambiarUbicacion(lat, lng) {
        actualizarCoordenadas(lat, lng);
        if (geocodeTimeout) clearTimeout(geocodeTimeout);
        geocodeTimeout = setTimeout(() => geocodificacionInversa(lat, lng), 600);
    }

    // ── Eventos del mapa ──
    marker.on('dragend', function () {
        const pos = marker.getLatLng();
        alCambiarUbicacion(pos.lat, pos.lng);
    });

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        alCambiarUbicacion(e.latlng.lat, e.latlng.lng);
    });

    // ── Búsqueda por texto → coordenadas ──
    window.buscarEnMapa = function () {
        const query = document.getElementById('buscar_direccion').value.trim();
        if (!query) return;

        fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=1`, {
            headers: { 'Accept-Language': 'es' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                map.setView([lat, lng], 17);
                marker.setLatLng([lat, lng]);
                alCambiarUbicacion(lat, lng);
            } else {
                alert('No se encontró la dirección. Intenta ser más específico.');
            }
        })
        .catch(() => alert('Error al buscar la dirección.'));
    };

    // Buscar al presionar Enter en el input de búsqueda
    document.getElementById('buscar_direccion').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarEnMapa();
        }
    });

    // Si ya hay coordenadas guardadas, rellenar dirección si está vacía
    const direccionActual = document.getElementById('direccion').value.trim();
    if (latInicial && lngInicial && !direccionActual) {
        geocodificacionInversa(latInicial, lngInicial);
    }

    // Asegura que los campos de coord se muestren correctamente al cargar
    actualizarCoordenadas(latInicial, lngInicial);
});
</script>
@endsection