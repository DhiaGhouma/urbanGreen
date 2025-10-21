@extends('layouts.app')

@section('title', 'Localisation - ' . $greenSpace->name)

@section('content')
<div class="page-header mb-3">
    <h1><i class="fas fa-map-marker-alt me-2"></i> Localisation de {{ $greenSpace->name }}</h1>
    <p class="text-muted">Visualisez l’emplacement exact de cet espace vert sur la carte.</p>
</div>

<div class="card">
    <div class="card-body">
        {{-- On passe la localisation dans un attribut data --}}
        <div id="map" 
             data-location="{{ $greenSpace->location }}" 
             data-name="{{ $greenSpace->name }}"
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
    const location = mapDiv.dataset.location;
    const name = mapDiv.dataset.name;

    // Appel à Nominatim (service OpenStreetMap)
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lon = parseFloat(data[0].lon);

                const map = L.map('map').setView([lat, lon], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                L.marker([lat, lon]).addTo(map)
                    .bindPopup(`<b>${name}</b><br>${location}`)
                    .openPopup();
            } else {
                alert("Localisation introuvable !");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Erreur lors du chargement de la carte !");
        });
});
</script>
@endsection
