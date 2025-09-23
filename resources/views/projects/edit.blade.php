@extends('layouts.app')

@section('title', 'Modifier ' . $project->title . ' - UrbanGreen')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projets</a></li>
            <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol>
    </nav>
    <h1><i class="fas fa-edit me-2"></i>Modifier {{ $project->title }}</h1>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('projects.update', $project) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="title" class="form-label">Titre du projet *</label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $project->title) }}" 
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
                                            {{ old('association_id', $project->association_id) == $association->id ? 'selected' : '' }}>
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
                                    <option value="{{ $space->id }}" 
                                            {{ old('green_space_id', $project->green_space_id) == $space->id ? 'selected' : '' }}>
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
                                      required>{{ old('description', $project->description) }}</textarea>
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
                                   value="{{ old('estimated_budget', $project->estimated_budget) }}" 
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
                                <option value="proposé" {{ old('status', $project->status) == 'proposé' ? 'selected' : '' }}>Proposé</option>
                                <option value="en cours" {{ old('status', $project->status) == 'en cours' ? 'selected' : '' }}>En cours</option>
                                <option value="terminé" {{ old('status', $project->status) == 'terminé' ? 'selected' : '' }}>Terminé</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">
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