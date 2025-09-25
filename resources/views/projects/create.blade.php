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
                                    <option value="{{ $association->id }}" data-domain="{{ $association->domain }}">
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
    const descriptionTextarea = document.getElementById('description');
    const statusSelect = document.getElementById('status');
    const recommendationsDiv = document.getElementById('recommendations');

    async function fetchRecommendations() {
        // Get selected association's domain
        const selectedAssociation = associationSelect.options[associationSelect.selectedIndex];
        const associationDomain = selectedAssociation ? selectedAssociation.dataset.domain : '';

        // Get selected green space info
        const selectedGreenSpace = greenSpaceSelect.options[greenSpaceSelect.selectedIndex];
        const greenSpaceText = selectedGreenSpace ? selectedGreenSpace.text : '';

        // Get form values
        const description = descriptionTextarea.value.trim();
        const status = statusSelect.value;

        // Only fetch if both association and green space are selected
        if (!associationSelect.value || !greenSpaceSelect.value) {
            recommendationsDiv.innerHTML = '<p class="text-muted">Sélectionnez une association et un espace vert pour voir les recommandations.</p>';
            return;
        }

        // Extract green space name (before the " - " part)
        const greenSpaceName = greenSpaceText.includes(' - ') ?
            greenSpaceText.split(' - ')[0] : greenSpaceText;

        const payload = {
            description: description,
            status: status,
            green_space_type: greenSpaceName,
            association_domain: associationDomain || ''
        };

        console.log('Sending payload:', payload); // Debug log

        // Show loading state
        recommendationsDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Recherche de recommandations...</div>';

        try {
            const response = await fetch('http://127.0.0.1:5001/recommend', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Received data:', data); // Debug log

            // Clear previous recommendations
            recommendationsDiv.innerHTML = '';

            // Check for the correct response structure
            if (data.recommendations && data.recommendations.length > 0) {
                let html = '<div class="row">';

                data.recommendations.forEach((project, index) => {
                    const similarityPercentage = (project.similarity_score * 100).toFixed(1);

                    html += `
                        <div class="col-12 mb-3">
                            <div class="card border-left-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title mb-2">
                                                <i class="fas fa-project-diagram me-2 text-success"></i>
                                                ${project.title || 'Projet sans titre'}
                                            </h6>
                                            <p class="card-text text-muted small mb-2">
                                                ${project.description ? project.description.substring(0, 120) + (project.description.length > 120 ? '...' : '') : 'Pas de description disponible'}
                                            </p>
                                            <div class="row text-sm">
                                                <div class="col-md-4">
                                                    <strong>Budget:</strong> ${project.estimated_budget ? project.estimated_budget + ' €' : 'Non spécifié'}
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Statut:</strong>
                                                    <span class="badge badge-${getStatusBadgeClass(project.status)}">
                                                        ${project.status || 'Non spécifié'}
                                                    </span>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Association:</strong> ${project.association || 'Non spécifiée'}
                                                </div>
                                            </div>
                                            ${project.green_space ? `<div class="mt-1"><strong>Espace vert:</strong> ${project.green_space}</div>` : ''}
                                        </div>
                                        <div class="ms-3 text-center">
                                            <div class="badge badge-primary badge-lg">
                                                ${similarityPercentage}%
                                            </div>
                                            <div class="text-xs text-muted">Similarité</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                recommendationsDiv.innerHTML = html;
            } else if (data.error) {
                recommendationsDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>${data.error}</div>`;
            } else {
                recommendationsDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Aucune recommandation disponible pour ces critères.</div>';
            }
        } catch (error) {
            console.error('Error fetching recommendations:', error);
            recommendationsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Erreur lors de la récupération des recommandations: ${error.message}
                </div>`;
        }
    }

    // Helper function to get appropriate badge class for status
    function getStatusBadgeClass(status) {
        switch(status) {
            case 'proposé': return 'secondary';
            case 'en cours': return 'warning';
            case 'terminé': return 'success';
            default: return 'light';
        }
    }

    // Add debouncing to avoid too many API calls
    let debounceTimer;
    function debouncedFetchRecommendations() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchRecommendations, 300);
    }

    // Add event listeners
    associationSelect.addEventListener('change', fetchRecommendations);
    greenSpaceSelect.addEventListener('change', fetchRecommendations);
    statusSelect.addEventListener('change', fetchRecommendations);
    descriptionTextarea.addEventListener('input', debouncedFetchRecommendations);
});
</script>
@endsection

