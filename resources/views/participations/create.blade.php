@extends('layouts.app')

@section('title', 'Nouvelle Participation - UrbanGreen')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('participations.index') }}">Participations</a></li>
            <li class="breadcrumb-item active">Nouvelle Participation</li>
        </ol>
    </nav>
    <h1><i class="fas fa-plus me-2"></i>Créer une Participation</h1>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('participations.store') }}">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="user_id" class="form-label">Utilisateur *</label>
                            <select name="user_id" 
                                    id="user_id" 
                                    class="form-select @error('user_id') is-invalid @enderror" 
                                    required>
                                <option value="">Sélectionner un utilisateur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="green_space_id" class="form-label">Espace Vert *</label>
                            <select name="green_space_id" 
                                    id="green_space_id" 
                                    class="form-select @error('green_space_id') is-invalid @enderror" 
                                    required>
                                <option value="">Sélectionner un espace vert</option>
                                @foreach($greenSpaces as $greenSpace)
                                    <option value="{{ $greenSpace->id }}" {{ old('green_space_id') == $greenSpace->id ? 'selected' : '' }}>
                                        {{ $greenSpace->name }} - {{ $greenSpace->location }}
                                    </option>
                                @endforeach
                            </select>
                            @error('green_space_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="date" class="form-label">Date de Participation *</label>
                            <input type="date" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   id="date" 
                                   name="date" 
                                   value="{{ old('date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="statut" class="form-label">Statut *</label>
                            <select name="statut" 
                                    id="statut" 
                                    class="form-select @error('statut') is-invalid @enderror" 
                                    required>
                                <option value="en_attente" {{ old('statut', 'en_attente') == 'en_attente' ? 'selected' : '' }}>
                                    En Attente
                                </option>
                                <option value="confirmee" {{ old('statut') == 'confirmee' ? 'selected' : '' }}>
                                    Confirmée
                                </option>
                                <option value="annulee" {{ old('statut') == 'annulee' ? 'selected' : '' }}>
                                    Annulée
                                </option>
                                <option value="terminee" {{ old('statut') == 'terminee' ? 'selected' : '' }}>
                                    Terminée
                                </option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Créer la Participation
                        </button>
                        <a href="{{ route('participations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection