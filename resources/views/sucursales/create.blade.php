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
                    <h4 class="card-title">Agregar Sucursal</h4>
                </div>

                <div class="card-body">

                    <form action="{{ route('sucursales.store') }}" method="POST">
                        @csrf
                        <div class="row align-items-center">
                             {{-- Nombre --}}
                            <div class="form-group col-md-6">
                                <label>Nombre de la sucursal <span class="text-danger">*</span></label>
                                <input type="text" name="nombre"
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    value="{{ old('nombre') }}" required>
                            </div>

                            {{-- Telefono --}}
                            <div class="form-group col-md-6">
                                <label for="telefono">
                                        <i class="ri-phone-line me-1"></i> Teléfono
                                    </label>
                                <input type="text" name="telefono"
                                    class="form-control @error('telefono') is-invalid @enderror"
                                    value="{{ old('telefono') }}"  maxlength="10"
                                       oninput="this.value = this.value.replace(/\D/g,'').slice(0,10)" required>
                            </div>

                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5><i class="fa fa-map-marker-alt text-primary me-1"></i> Dirección de la Sucursal</h5>
                                <hr>
                            </div>

                            {{-- Dirección --}}
                            <div class="form-group col-md-8 mt-2">
                                <label>Dirección <span class="text-danger">*</span></label>
                                <input type="text" id="direccion" name="direccion"
                                    class="form-control @error('direccion') is-invalid @enderror"
                                    value="{{ old('direccion') }}" required
                                    placeholder="Se llena automáticamente desde el mapa">
                            </div>

                            {{-- Búsqueda --}}
                            <div class="form-group col-md-4 mt-2">
                                <label>Buscar en mapa</label>
                                <div class="input-group">
                                    <input type="text" id="buscar_direccion" class="form-control"
                                        placeholder="Escribe una dirección...">
                                    <button type="button" class="btn btn-primary" onclick="buscarEnMapa()">
                                        🔍
                                    </button>
                                </div>
                            </div>                            

                            {{-- Coordenadas --}}
                            <div class="form-group col-md-3">
                                    <label>Latitud</label>
                                    <input type="text" id="latitud" name="latitud" class="form-control" readonly>
                                </div>

                               <div class="form-group col-md-3">
                                    <label>Longitud</label>
                                    <input type="text" id="longitud" name="longitud" class="form-control" readonly>
                                </div>

                            {{-- Google Maps --}}
                            <div class="form-group col-md-6 mt-2">
                                <label>Link Google Maps</label>
                                <input type="text" id="rul_maps" name="rul_maps" class="form-control" readonly>
                            </div>

                            {{-- MAPA --}}
                            <div class="form-group col-md-12 mt-3">
                                <label>Ubicación en el mapa</label>
                                <div id="map" style="height:400px;border-radius:10px;"></div>
                            </div>

                        </div>    
                      

                        {{-- BOTONES --}}
                        <div class="d-flex justify-content-between mt-4">
                            
                            <a href="{{ route('sucursales.index') }}" class="btn btn-danger">
                                ❌ Cancelar
                            </a>

                            <button class="btn btn-primary">
                                💾 Guardar
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

    // 📍 ubicación inicial (Tabasco por defecto)
    let lat = 17.0732;
    let lng = -96.7266;

    const map = L.map('map').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    function actualizar(lat, lng) {
        document.getElementById('latitud').value = lat.toFixed(6);
        document.getElementById('longitud').value = lng.toFixed(6);

        document.getElementById('rul_maps').value =
            `https://www.google.com/maps?q=${lat},${lng}`;
    }

    async function reverseGeocode(lat, lng) {
        const res = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
        );
        const data = await res.json();

        if (data?.display_name) {
            document.getElementById('direccion').value = data.display_name;
        }
    }

    function setLocation(lat, lng) {
        actualizar(lat, lng);
        reverseGeocode(lat, lng);
    }

    marker.on('dragend', function () {
        const pos = marker.getLatLng();
        setLocation(pos.lat, pos.lng);
    });

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        setLocation(e.latlng.lat, e.latlng.lng);
    });

    // 🔎 búsqueda
    window.buscarEnMapa = function () {
        const q = document.getElementById('buscar_direccion').value;

        fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=1`)
        .then(res => res.json())
        .then(data => {
            if (data.length) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);

                map.setView([lat, lng], 17);
                marker.setLatLng([lat, lng]);
                setLocation(lat, lng);
            } else {
                alert('No encontrado');
            }
        });
    };

    // init
    actualizar(lat, lng);

});
</script>
@endsection