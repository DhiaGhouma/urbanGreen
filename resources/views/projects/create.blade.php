@extends('layouts.app')

@section('title', 'Nouveau Projet - UrbanGreen')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projets</a></li>
            <li class="breadcrumb-item active">Nouveau Projet</li>
        </ol>
    </nav>
    <h1><i class="fas fa-plus me-2"></i>Créer un Projet</h1>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('projects.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="title" class="form-label">Titre du projet *</label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') }}"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="association_id" class="form-label">Association *</label>
                            <select class="form-select @error('association_id') is-invalid @enderror"
                                    id="association_id"
                                    name="association_id"
                                    required>
                                <option value="">Sélectionnez une association</option>
                                @foreach($associations as $association)
                                    <option value="{{ $association->id }}"
                                            {{ old('association_id', request('association_id')) == $association->id ? 'selected' : '' }}>
                                        {{ $association->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('association_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="green_space_id" class="form-label">Espace Vert *</label>
                            <select class="form-select @error('green_space_id') is-invalid @enderror"
                                    id="green_space_id"
                                    name="green_space_id"
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

                        <div class="col-md-12">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="4"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="estimated_budget" class="form-label">Budget estimé (€) *</label>
                            <input type="number"
                                   class="form-control @error('estimated_budget') is-invalid @enderror"
                                   id="estimated_budget"
                                   name="estimated_budget"
                                   step="0.01"
                                   min="0"
                                   value="{{ old('estimated_budget') }}"
                                   required>
                            @error('estimated_budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label">Statut *</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status"
                                    name="status"
                                    required>
                                <option value="proposé" {{ old('status', 'proposé') == 'proposé' ? 'selected' : '' }}>Proposé</option>
                                <option value="en cours" {{ old('status') == 'en cours' ? 'selected' : '' }}>En cours</option>
                                <option value="terminé" {{ old('status') == 'terminé' ? 'selected' : '' }}>Terminé</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Créer le Projet
                        </button>
                    </div>
                </form>
                <div class="col-md-12 mt-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Projets Recommandés</h5>
        </div>
        <div class="card-body" id="recommendations">
            <p class="text-muted">Les projets similaires apparaîtront ici après avoir sélectionné une association et un espace vert.</p>
        </div>
    </div>
</div>

            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const associationSelect = document.getElementById('association_id');
    const greenSpaceSelect = document.getElementById('green_space_id');
    const recommendationsDiv = document.getElementById('recommendations');

    async function fetchRecommendations() {
        const associationText = associationSelect.options[associationSelect.selectedIndex].text;
        const greenSpaceText = greenSpaceSelect.options[greenSpaceSelect.selectedIndex].text;
        const description = document.getElementById('description').value;
        const status = document.getElementById('status').value;

        if (!associationSelect.value || !greenSpaceSelect.value) return;

        const payload = {
            description: description,
            status: status,
            green_space_type: greenSpaceText.split(' - ')[0],
            association_domain: associationText
        };

        try {
            const res = await fetch('http://127.0.0.1:5001/recommend', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            // Clear previous recommendations
            recommendationsDiv.innerHTML = '';

            if (data.indices && data.indices.length > 0) {
                data.indices[0].forEach(idx => {
                    // Example: You can fetch project details from Laravel API by index or store project info in JS
                    const projectCard = `<div class="mb-2 p-2 border rounded">
                        Projet #${idx + 1} - Voir détails
                    </div>`;
                    recommendationsDiv.innerHTML += projectCard;
                });
            } else {
                recommendationsDiv.innerHTML = '<p class="text-muted">Aucune recommandation disponible.</p>';
            }
        } catch (err) {
            recommendationsDiv.innerHTML = '<p class="text-danger">Erreur lors de la récupération des recommandations.</p>';
            console.error(err);
        }
    }

    associationSelect.addEventListener('change', fetchRecommendations);
    greenSpaceSelect.addEventListener('change', fetchRecommendations);
    document.getElementById('description').addEventListener('input', fetchRecommendations);
    document.getElementById('status').addEventListener('change', fetchRecommendations);
});
</script>
@endsection

