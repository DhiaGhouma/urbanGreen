@extends('layouts.app')

@section('title', 'Nouveau Signalement')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><i class="fas fa-flag me-2"></i>Nouveau Signalement</h1>
            <p class="mb-0 text-muted">Signalez un problème dans un espace vert</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>
</div>

<div class="row justify-content-center mt-4">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Espace Vert --}}
                    <div class="mb-3">
                        <label for="green_space_id" class="form-label">
                            <i class="fas fa-tree me-1"></i>Espace Vert <span class="text-danger">*</span>
                        </label>
                        <select name="green_space_id" 
                                id="green_space_id" 
                                class="form-select @error('green_space_id') is-invalid @enderror" 
                                required>
                            <option value="">Sélectionnez un espace vert</option>
                            @foreach($greenSpaces as $space)
                                <option value="{{ $space->id }}" {{ old('green_space_id') == $space->id ? 'selected' : '' }}>
                                    {{ $space->name }} - {{ $space->location }}
                                </option>
                            @endforeach
                        </select>
                        @error('green_space_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Titre --}}
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading me-1"></i>Titre du signalement <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title') }}"
                               placeholder="Ex: Déchets accumulés près de l'entrée"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{-- Catégorie --}}
                            <div class="mb-3">
                                <label for="category" class="form-label">
                                    <i class="fas fa-tags me-1"></i>Catégorie <span class="text-danger">*</span>
                                </label>
                                <select name="category" 
                                        id="category" 
                                        class="form-select @error('category') is-invalid @enderror" 
                                        required>
                                    <option value="">Sélectionnez une catégorie</option>
                                    <option value="dechets" {{ old('category') == 'dechets' ? 'selected' : '' }}>🗑️ Déchets</option>
                                    <option value="plantes_mortes" {{ old('category') == 'plantes_mortes' ? 'selected' : '' }}>🌱 Plantes mortes</option>
                                    <option value="vandalisme" {{ old('category') == 'vandalisme' ? 'selected' : '' }}>⚠️ Vandalisme</option>
                                    <option value="equipement" {{ old('category') == 'equipement' ? 'selected' : '' }}>🔧 Équipement endommagé</option>
                                    <option value="autre" {{ old('category') == 'autre' ? 'selected' : '' }}>📋 Autre</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            {{-- Priorité --}}
                            <div class="mb-3">
                                <label for="priority" class="form-label">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Priorité <span class="text-danger">*</span>
                                </label>
                                <select name="priority" 
                                        id="priority" 
                                        class="form-select @error('priority') is-invalid @enderror" 
                                        required>
                                    <option value="basse" {{ old('priority') == 'basse' ? 'selected' : '' }}>🟢 Basse</option>
                                    <option value="normale" {{ old('priority', 'normale') == 'normale' ? 'selected' : '' }}>🔵 Normale</option>
                                    <option value="haute" {{ old('priority') == 'haute' ? 'selected' : '' }}>🟠 Haute</option>
                                    <option value="urgente" {{ old('priority') == 'urgente' ? 'selected' : '' }}>🔴 Urgente</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description détaillée <span class="text-danger">*</span>
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  placeholder="Décrivez le problème en détail..."
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Photo --}}
                    <div class="mb-3">
                        <label for="photo" class="form-label">
                            <i class="fas fa-camera me-1"></i>Photo
                        </label>
                        <input type="file" 
                               name="photo" 
                               id="photo" 
                               class="form-control @error('photo') is-invalid @enderror"
                               accept="image/jpeg,image/png,image/jpg">
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Formats acceptés: JPG, PNG (Max: 5MB)</small>
                        <div id="photoPreview" class="mt-2" style="display: none;">
                            <img src="" alt="Aperçu" class="img-fluid rounded" style="max-height: 250px;">
                        </div>
                    </div>

                    {{-- Géolocalisation --}}
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i>Géolocalisation (optionnel)
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="number" 
                                           name="latitude" 
                                           id="latitude" 
                                           step="0.0000001"
                                           class="form-control @error('latitude') is-invalid @enderror" 
                                           value="{{ old('latitude') }}"
                                           placeholder="Ex: 36.8065">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" 
                                           name="longitude" 
                                           id="longitude" 
                                           step="0.0000001"
                                           class="form-control @error('longitude') is-invalid @enderror" 
                                           value="{{ old('longitude') }}"
                                           placeholder="Ex: 10.1815">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="getCurrentLocation">
                                <i class="fas fa-crosshairs me-1"></i>Utiliser ma position actuelle
                            </button>
                        </div>
                    </div>

                    {{-- Boutons --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>Envoyer le signalement
                        </button>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Aperçu de la photo
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photoPreview');
            preview.querySelector('img').src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});

// Géolocalisation
document.getElementById('getCurrentLocation').addEventListener('click', function() {
    if (navigator.geolocation) {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Récupération...';
        this.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(7);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(7);
                this.innerHTML = '<i class="fas fa-check me-1"></i>Position obtenue !';
                this.classList.replace('btn-outline-primary', 'btn-success');
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-crosshairs me-1"></i>Utiliser ma position actuelle';
                    this.classList.replace('btn-success', 'btn-outline-primary');
                    this.disabled = false;
                }, 2000);
            },
            (error) => {
                alert('Impossible d\'obtenir votre position. Veuillez vérifier les permissions de localisation.');
                this.innerHTML = '<i class="fas fa-crosshairs me-1"></i>Utiliser ma position actuelle';
                this.disabled = false;
            }
        );
    } else {
        alert('La géolocalisation n\'est pas supportée par votre navigateur.');
    }
});
</script>
@endsection