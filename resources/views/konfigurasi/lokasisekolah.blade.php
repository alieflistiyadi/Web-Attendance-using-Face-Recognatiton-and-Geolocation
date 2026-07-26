@extends('layouts.admin.tabler')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Konfigurasi Lokasi
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row justify-content-center">
                 <div class="col-lg-11">
                    <div class="card">
                        <div class="card-body">
                            @if (Session::get('success'))
                                <div class="alert alert-success">
                                    {{ Session::get('success')}}
                                </div>
                            @endif

                            @if (Session::get('warning'))
                                <div class="alert alert-warning">
                                    {{ Session::get('warning')}}
                                </div>
                            @endif
                            <form action="/konfigurasi/updatelokasisekolah" method="POST">
                                @csrf

                                <div class="row">

                                    {{-- FORM KIRI --}}
                                    <div class="col-md-4">

                                        <label class="form-label">Latitude</label>
                                        <div class="input-icon mb-3">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M9 11a3 3 0 1 0 6 0"/>
                                                    <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0"/>
                                                </svg>
                                            </span>

                                            <input type="text"
                                                id="latitude"
                                                name="latitude"
                                                class="form-control"
                                                value="{{ $lok_sekolah->latitude }}"
                                                readonly>
                                        </div>

                                        <label class="form-label">Longitude</label>
                                        <div class="input-icon mb-3">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M9 11a3 3 0 1 0 6 0"/>
                                                    <path d="M17.657 16.657l-4.243 4.243a2 2 0 1 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0"/>
                                                </svg>
                                            </span>

                                            <input type="text"
                                                id="longitude"
                                                name="longitude"
                                                class="form-control"
                                                value="{{ $lok_sekolah->longitude }}"
                                                readonly>
                                        </div>

                                        <label class="form-label">Radius (Meter)</label>

                                        <div class="input-icon mb-3">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 12a1 1 0 1 0 2 0"/>
                                                    <path d="M15.51 15.56a5 5 0 1 0 -3.51 1.44"/>
                                                    <path d="M18.832 17.86a9 9 0 1 0 -6.832 3.14"/>
                                                </svg>
                                            </span>

                                            <input type="number"
                                                id="radius"
                                                class="form-control"
                                                name="radius"
                                                value="{{ $lok_sekolah->radius }}">
                                        </div>

                                        <button class="btn btn-primary mt-3 px-5">
                                            Update Lokasi
                                        </button>

                                    </div>

                                    {{-- MAP KANAN --}}
                                    <div class="col-md-8">

                                        <label class="form-label fw-bold">
                                            Pilih Lokasi Sekolah
                                        </label>

                                        <div id="map"
                                            style="height:500px;border-radius:10px;"></div>

                                        <small class="text-muted">
                                            Klik peta atau geser marker untuk menentukan lokasi sekolah.
                                        </small>

                                    </div>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@push('myscript')

<script>

const latitudeInput  = document.getElementById("latitude");
const longitudeInput = document.getElementById("longitude");
const radiusInput    = document.getElementById("radius");

// Ambil koordinat dari database
let lat = parseFloat(latitudeInput.value) || -6.3274;
let lng = parseFloat(longitudeInput.value) || 107.1675;

// Inisialisasi Map
const map = L.map('map').setView([lat, lng], 17);

//search control
L.Control.geocoder({
    defaultMarkGeocode: false,
    placeholder: "Cari lokasi sekolah..."
})
.on("markgeocode", function(e){

    const center = e.geocode.center;

    marker.setLatLng(center);

    map.setView(center,17);

    document.getElementById("latitude").value =
        center.lat.toFixed(7);

    document.getElementById("longitude").value =
        center.lng.toFixed(7);

    circle.setLatLng(center);

})
.addTo(map);

// Tile OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:'© OpenStreetMap'
}).addTo(map);

// Marker
const marker = L.marker([lat, lng], {
    draggable:true
}).addTo(map);

// Circle Radius
const circle = L.circle([lat, lng], {
    radius: parseInt(radiusInput.value) || 100,
    color:'#206bc4',
    fillColor:'#206bc4',
    fillOpacity:0.15
}).addTo(map);


// Klik Map
map.on('click', function(e){

    marker.setLatLng(e.latlng);
    circle.setLatLng(e.latlng);

    latitudeInput.value  = e.latlng.lat.toFixed(6);
    longitudeInput.value = e.latlng.lng.toFixed(6);

});


// Geser Marker
marker.on('dragend', function(e){

    const posisi = marker.getLatLng();

    circle.setLatLng(posisi);

    latitudeInput.value  = posisi.lat.toFixed(6);
    longitudeInput.value = posisi.lng.toFixed(6);

});


// Ubah Radius
radiusInput.addEventListener('input', function(){

    circle.setRadius(parseInt(this.value) || 0);

});

</script>

@endpush

@endsection