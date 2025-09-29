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
                                   value="{{ old('species') }}">
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