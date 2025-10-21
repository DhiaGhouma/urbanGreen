@extends('layouts.app')

@section('title', 'Localisation - ' . $greenSpace->name)

@section('content')
<div class="page-header mb-3">
    <h1><i class="fas fa-map-marker-alt me-2"></i> Localisation de {{ $greenSpace->name }}</h1>
    <p class="text-muted">Visualisez l’emplacement exact de cet espace vert sur la carte.</p>
</div>

<div class="card">
    <div class="card-body">
        {{-- On passe les coordonnées et le nom dans des attributs data --}}
        <div id="map"
             data-lat="{{ $greenSpace->latitude }}"
             data-lon="{{ $greenSpace->longitude }}"
             data-name="{{ $greenSpace->name }}"
             data-location="{{ $greenSpace->location }}"
             style="height: 500px; border-radius: 10px;">
        </div>
    </div>
</div>

<a href="{{ route('greenspaces.index') }}" class="btn btn-secondary mt-3">
    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
</a>

{{-- Scripts Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapDiv = document.getElementById('map');
    const lat = parseFloat(mapDiv.dataset.lat);
    const lon = parseFloat(mapDiv.dataset.lon);
    const name = mapDiv.dataset.name;
    const location = mapDiv.dataset.location;

    if (isNaN(lat) || isNaN(lon)) {
        alert("Coordonnées non disponibles pour cet espace vert !");
        return;
    }

    // Initialisation de la carte
    const map = L.map('map').setView([lat, lon], 15);

    // Ajout du fond de carte
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Ajout du marqueur
    L.marker([lat, lon]).addTo(map)
        .bindPopup(`<b>${name}</b><br>${location}<br><small>(${lat.toFixed(6)}, ${lon.toFixed(6)})</small>`)
        .openPopup();
});
</script>
@endsection
