@extends('layouts.app')

@section('title', 'Modifier ' . $association->name . ' - UrbanGreen')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('associations.index') }}">Associations</a></li>
            <li class="breadcrumb-item"><a href="{{ route('associations.show', $association) }}">{{ $association->name }}</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol>
    </nav>
    <h1><i class="fas fa-edit me-2"></i>Modifier {{ $association->name }}</h1>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('associations.update', $association) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="name" class="form-label">Nom de l'association *</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $association->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $association->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Téléphone *</label>
                            <input type="text" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $association->phone) }}" 
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="domain" class="form-label">Domaine d'action *</label>
                            <select class="form-select @error('domain') is-invalid @enderror" 
                                    id="domain" 
                                    name="domain" 
                                    required>
                                <option value="">Sélectionnez un domaine</option>
                                <option value="Environnement" {{ old('domain', $association->domain) == 'Environnement' ? 'selected' : '' }}>Environnement</option>
                                <option value="Jardinage urbain" {{ old('domain', $association->domain) == 'Jardinage urbain' ? 'selected' : '' }}>Jardinage urbain</option>
                                <option value="Éducation environnementale" {{ old('domain', $association->domain) == 'Éducation environnementale' ? 'selected' : '' }}>Éducation environnementale</option>
                                <option value="Développement durable" {{ old('domain', $association->domain) == 'Développement durable' ? 'selected' : '' }}>Développement durable</option>
                                <option value="Animation sociale" {{ old('domain', $association->domain) == 'Animation sociale' ? 'selected' : '' }}>Animation sociale</option>
                                <option value="Biodiversité" {{ old('domain', $association->domain) == 'Biodiversité' ? 'selected' : '' }}>Biodiversité</option>
                            </select>
                            @error('domain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('associations.show', $association) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Mettre à Jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection