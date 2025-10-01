@extends('layouts.app')

@section('title', 'Modifier l\'événement')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Événements</a></li>
            <li class="breadcrumb-item"><a href="{{ route('events.show', $event) }}">{{ $event->titre }}</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol>
    </nav>
    <h1><i class="fas fa-edit me-2"></i>Modifier l'événement</h1>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="row g-4">
                        <!-- Titre -->
                        <div class="col-md-8">
                            <label for="titre" class="form-label">Titre de l'événement <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('titre') is-invalid @enderror" 
                                   id="titre" 
                                   name="titre" 
                                   value="{{ old('titre', $event->titre) }}"
                                   required>
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="col-md-4">
                            <label for="type" class="form-label">Type d'événement <span class="text-danger">*</span></label>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="plantation" {{ old('type', $event->type) == 'plantation' ? 'selected' : '' }}>
                                    🌱 Plantation
                                </option>
                                <option value="conference" {{ old('type', $event->type) == 'conference' ? 'selected' : '' }}>
                                    🎤 Conférence
                                </option>
                                <option value="atelier" {{ old('type', $event->type) == 'atelier' ? 'selected' : '' }}>
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
                                      required>{{ old('description', $event->description) }}</textarea>
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
                                   value="{{ old('date_debut', $event->date_debut->format('Y-m-d\TH:i')) }}"
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
                                   value="{{ old('date_fin', $event->date_fin ? $event->date_fin->format('Y-m-d\TH:i') : '') }}">
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
                                   value="{{ old('lieu', $event->lieu) }}"
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
                                   value="{{ old('adresse', $event->adresse) }}">
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Capacité max -->
                        <div class="col-md-4">
                            <label for="capacite_max" class="form-label">Capacité maximale</label>
                            <input type="number" 
                                   class="form-control @error('capacite_max') is-invalid @enderror" 
                                   id="capacite_max" 
                                   name="capacite_max" 
                                   value="{{ old('capacite_max', $event->capacite_max) }}"
                                   min="1">
                            @error('capacite_max')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div class="col-md-4">
                            <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select class="form-control @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                <option value="planifie" {{ old('statut', $event->statut) == 'planifie' ? 'selected' : '' }}>Planifié</option>
                                <option value="en_cours" {{ old('statut', $event->statut) == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="termine" {{ old('statut', $event->statut) == 'termine' ? 'selected' : '' }}>Terminé</option>
                                <option value="annule" {{ old('statut', $event->statut) == 'annule' ? 'selected' : '' }}>Annulé</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Association -->
                        <div class="col-md-4">
                            <label for="association_id" class="form-label">Association <span class="text-danger">*</span></label>
                            <select class="form-control @error('association_id') is-invalid @enderror" 
                                    id="association_id" 
                                    name="association_id" 
                                    required>
                                @foreach($associations as $association)
                                    <option value="{{ $association->id }}" {{ old('association_id', $event->association_id) == $association->id ? 'selected' : '' }}>
                                        {{ $association->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('association_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image actuelle -->
                        @if($event->image)
                        <div class="col-12">
                            <label class="form-label">Image actuelle</label>
                            <div>
                                <img src="{{ asset('storage/' . $event->image) }}" 
                                     alt="{{ $event->titre }}" 
                                     style="max-width: 300px; border-radius: 15px;">
                            </div>
                        </div>
                        @endif

                        <!-- Nouvelle image -->
                        <div class="col-12">
                            <label for="image" class="form-label">
                                {{ $event->image ? 'Changer l\'image' : 'Ajouter une image' }}
                            </label>
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image"
                                   accept="image/*"
                                   onchange="previewImage(event)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Image preview -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="preview" src="" alt="Aperçu" style="max-width: 300px; border-radius: 15px;">
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        </button>
                        <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary">
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
</script>
@endsection