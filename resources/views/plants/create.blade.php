@extends('layouts.app')

@section('title', 'Nouvelle Plante - {{ $greenspace->name }} - UrbanGreen')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('greenspaces.index') }}">Espaces Verts</a></li>
            <li class="breadcrumb-item"><a href="{{ route('greenspaces.show', $greenspace) }}">{{ $greenspace->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('greenspaces.plants.index', $greenspace) }}">Plantes</a></li>
            <li class="breadcrumb-item active">Nouvelle Plante</li>
        </ol>
    </nav>
    <h1><i class="fas fa-plus me-2"></i>Ajouter une Plante</h1>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('greenspaces.plants.store', $greenspace) }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nom de la plante *</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="species" class="form-label">Espèce</label>
                            <input type="text"
                                   class="form-control @error('species') is-invalid @enderror"
                                   id="species"
                                   name="species"
                                   value="{{ old('species') }}"
                                   placeholder="Ex: Ficus Benjamina">
                            @error('species')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="quantity" class="form-label">Quantité</label>
                            <input type="number"
                                   min="1"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   id="quantity"
                                   name="quantity"
                                   value="{{ old('quantity', 1) }}">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="planted_at" class="form-label">Date de plantation</label>
                            <input type="date"
                                   class="form-control @error('planted_at') is-invalid @enderror"
                                   id="planted_at"
                                   name="planted_at"
                                   value="{{ old('planted_at') }}">
                            @error('planted_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="status" class="form-label">Statut *</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status"
                                    name="status"
                                    required>
                                <option value="">Sélectionnez un statut</option>
                                <option value="en vie" {{ old('status') == 'en vie' ? 'selected' : '' }}>En vie</option>
                                <option value="malade" {{ old('status') == 'malade' ? 'selected' : '' }}>Malade</option>
                                <option value="abattu" {{ old('status') == 'abattu' ? 'selected' : '' }}>Abattu</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="maintenance" class="form-label">Maintenance</label>
                            <input type="text"
                                   class="form-control @error('maintenance') is-invalid @enderror"
                                   id="maintenance"
                                   name="maintenance"
                                   value="{{ old('maintenance') }}"
                                   placeholder="Ex: Arrosage hebdomadaire, taille annuelle...">
                            @error('maintenance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes"
                                      name="notes"
                                      rows="3"
                                      placeholder="Observations particulières, historique, etc.">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('greenspaces.plants.index', $greenspace) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Créer la Plante
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const fields = {
        name: {
            el: document.getElementById('name'),
            validate: value => value.trim().length >= 3,
            message: 'Le nom doit contenir au moins 3 caractères.'
        },
        species: {
            el: document.getElementById('species'),
            validate: value => /^[A-Za-zÀ-ÿ\s-]+$/.test(value) || value.trim() === '',
            message: "L'espèce ne doit contenir que des lettres, espaces ou tirets."
        },
        quantity: {
            el: document.getElementById('quantity'),
            validate: value => parseInt(value) > 0 && parseInt(value) <= 1000,
            message: 'La quantité doit être comprise entre 1 et 1000.'
        },
        planted_at: {
            el: document.getElementById('planted_at'),
            validate: value => {
                if (!value) return true; // champ optionnel
                const date = new Date(value);
                const today = new Date();
                return date <= today;
            },
            message: 'La date de plantation ne peut pas être dans le futur.'
        }
    };

    // Crée un conteneur d’erreur sous chaque champ
    Object.values(fields).forEach(f => {
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        f.el.parentNode.appendChild(feedback);
        f.feedback = feedback;

        f.el.addEventListener('input', () => validateField(f));
    });

    // Empêche la saisie de caractères non valides dans "Espèce"
    fields.species.el.addEventListener('keypress', function (e) {
        const regex = /^[A-Za-zÀ-ÿ\s-]$/;
        if (!regex.test(e.key)) {
            e.preventDefault();
        }
    });

    // Validation individuelle
    function validateField(f) {
        const value = f.el.value;
        const valid = f.validate(value);

        if (!valid) {
            f.el.classList.add('is-invalid');
            f.el.classList.remove('is-valid');
            f.feedback.textContent = f.message;
        } else {
            f.el.classList.remove('is-invalid');
            f.el.classList.add('is-valid');
            f.feedback.textContent = '';
        }
        return valid;
    }

    // Validation globale avant soumission
    form.addEventListener('submit', function (e) {
        let allValid = true;
        Object.values(fields).forEach(f => {
            if (!validateField(f)) allValid = false;
        });
        if (!allValid) {
            e.preventDefault();
            alert('Veuillez corriger les erreurs avant de soumettre le formulaire.');
        }
    });
});
</script>
@endsection
