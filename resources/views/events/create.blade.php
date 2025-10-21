@extends('layouts.app')

@section('title', 'Créer un événement')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Événements</a></li>
            <li class="breadcrumb-item active">Créer un événement</li>
        </ol>
    </nav>
    <h1><i class="fas fa-calendar-plus me-2"></i>Créer un nouvel événement</h1>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        <!-- Titre -->
                        <div class="col-md-8">
                            <label for="titre" class="form-label">Titre de l'événement <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('titre') is-invalid @enderror" 
                                   id="titre" 
                                   name="titre" 
                                   value="{{ old('titre') }}"
                                   required>
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="col-md-4">
                            <label for="type" class="form-label">Type d'événement <span class="text-danger">*</span></label>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Sélectionner un type</option>
                                <option value="plantation" {{ old('type') == 'plantation' ? 'selected' : '' }}>
                                    🌱 Plantation
                                </option>
                                <option value="conference" {{ old('type') == 'conference' ? 'selected' : '' }}>
                                    🎤 Conférence
                                </option>
                                <option value="atelier" {{ old('type') == 'atelier' ? 'selected' : '' }}>
                                    🛠️ Atelier
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="5" 
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date debut -->
                        <div class="col-md-6">
                            <label for="date_debut" class="form-label">Date et heure de début <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control @error('date_debut') is-invalid @enderror" 
                                   id="date_debut" 
                                   name="date_debut" 
                                   value="{{ old('date_debut') }}"
                                   required>
                            @error('date_debut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date fin -->
                        <div class="col-md-6">
                            <label for="date_fin" class="form-label">Date et heure de fin</label>
                            <input type="datetime-local" 
                                   class="form-control @error('date_fin') is-invalid @enderror" 
                                   id="date_fin" 
                                   name="date_fin" 
                                   value="{{ old('date_fin') }}">
                            @error('date_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Lieu -->
                        <div class="col-md-6">
                            <label for="lieu" class="form-label">Lieu <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('lieu') is-invalid @enderror" 
                                   id="lieu" 
                                   name="lieu" 
                                   value="{{ old('lieu') }}"
                                   placeholder="Ex: Parc Municipal"
                                   required>
                            @error('lieu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Adresse -->
                        <div class="col-md-6">
                            <label for="adresse" class="form-label">Adresse complète</label>
                            <input type="text" 
                                   class="form-control @error('adresse') is-invalid @enderror" 
                                   id="adresse" 
                                   name="adresse" 
                                   value="{{ old('adresse') }}"
                                   placeholder="Ex: 123 Rue de la Nature, Ville">
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Capacité max -->
                        <div class="col-md-6">
                            <label for="capacite_max" class="form-label">Capacité maximale</label>
                            <input type="number" 
                                   class="form-control @error('capacite_max') is-invalid @enderror" 
                                   id="capacite_max" 
                                   name="capacite_max" 
                                   value="{{ old('capacite_max') }}"
                                   min="1"
                                   placeholder="Laisser vide pour illimité">
                            @error('capacite_max')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Nombre maximum de participants (laisser vide si illimité)</small>
                        </div>

                        <!-- Association -->
                        <div class="col-md-6">
                            <label for="association_id" class="form-label">Association organisatrice <span class="text-danger">*</span></label>
                            <select class="form-control @error('association_id') is-invalid @enderror" 
                                    id="association_id" 
                                    name="association_id" 
                                    required>
                                <option value="">Sélectionner une association</option>
                                @foreach($associations as $association)
                                    <option value="{{ $association->id }}" {{ old('association_id') == $association->id ? 'selected' : '' }}>
                                        {{ $association->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('association_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image -->
                        <div class="col-12">
                            <label for="image" class="form-label">Image de l'événement</label>
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image"
                                   accept="image/*"
                                   onchange="previewImage(event)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Formats acceptés : JPG, PNG, GIF (max 2MB)</small>
                            
                            <!-- Image preview -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="preview" src="" alt="Aperçu" style="max-width: 300px; border-radius: 15px;">
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Créer l'événement
                        </button>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Annuler
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
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}

// Set minimum date to today
document.getElementById('date_debut').min = new Date().toISOString().slice(0, 16);
</script>
@endsection