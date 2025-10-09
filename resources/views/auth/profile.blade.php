@extends('layouts.app')

@section('title', 'Mon Profil - UrbanGreen')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 mb-2">
                <i class="fas fa-id-badge me-2" style="color: var(--primary-green);"></i>
                Mon Profil
            </h1>
            <p class="text-muted mb-0">Gérez vos informations et vos préférences</p>
        </div>
        <a href="{{ route('auth.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Retour au tableau de bord
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>Veuillez corriger les erreurs ci-dessous.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0"><i class="fas fa-user-edit me-2"></i>Informations de base</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('auth.profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="name" class="form-label"><i class="fas fa-user me-1"></i>Nom complet</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label"><i class="fas fa-envelope me-1"></i>Adresse e-mail</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0"><i class="fas fa-sliders-h me-2"></i>Préférences</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('auth.profile.update') }}">
                    @csrf
                    @method('PATCH')

                    @php
                        // Get preferences and normalize to ensure arrays
                        $rawPrefs = $user->preferences ?? [];
                        $prefs = [
                            'activities_interest' => is_array($rawPrefs['activities_interest'] ?? null) 
                                ? $rawPrefs['activities_interest'] 
                                : (isset($rawPrefs['preferred_activities']) && is_array($rawPrefs['preferred_activities']) 
                                    ? $rawPrefs['preferred_activities'] 
                                    : []),
                            'prefered_days' => is_array($rawPrefs['prefered_days'] ?? null) 
                                ? $rawPrefs['prefered_days'] 
                                : [],
                            'availability' => is_array($rawPrefs['availability'] ?? null) 
                                ? $rawPrefs['availability'] 
                                : [],
                            'volunteer_roles' => is_array($rawPrefs['volunteer_roles'] ?? null) 
                                ? $rawPrefs['volunteer_roles'] 
                                : [],
                            'radius_km' => $rawPrefs['radius_km'] ?? $rawPrefs['max_distance'] ?? '',
                        ];
                    @endphp

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-seedling me-1"></i>Activités d'intérêt</label>
                        <div class="row">
                            @foreach($options['activities_interest'] as $opt)
                                <div class="col-6 col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="activities_interest[]" id="act_{{ $opt }}" value="{{ $opt }}" {{ in_array($opt, old('activities_interest', $prefs['activities_interest'] ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="act_{{ $opt }}">{{ ucfirst($opt) }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('activities_interest.*')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-calendar-day me-1"></i>Jours préférés</label>
                        <div class="row">
                            @foreach($options['prefered_days'] as $opt)
                                <div class="col-6 col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="prefered_days[]" id="day_{{ $opt }}" value="{{ $opt }}" {{ in_array($opt, old('prefered_days', $prefs['prefered_days'] ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="day_{{ $opt }}">{{ ucfirst($opt) }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('prefered_days.*')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-clock me-1"></i>Disponibilités</label>
                        <div class="row">
                            @foreach($options['availability'] as $opt)
                                <div class="col-6 col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="availability[]" id="avail_{{ $opt }}" value="{{ $opt }}" {{ in_array($opt, old('availability', $prefs['availability'] ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="avail_{{ $opt }}">{{ ucfirst($opt) }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('availability.*')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-user-friends me-1"></i>Rôles bénévoles</label>
                        <div class="row">
                            @foreach($options['volunteer_roles'] as $opt)
                                <div class="col-6 col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="volunteer_roles[]" id="role_{{ $opt }}" value="{{ $opt }}" {{ in_array($opt, old('volunteer_roles', $prefs['volunteer_roles'] ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $opt }}">{{ ucfirst($opt) }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('volunteer_roles.*')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="radius_km" class="form-label"><i class="fas fa-location-dot me-1"></i>Rayon (km) pour propositions proches</label>
                        <input type="number" min="1" max="200" class="form-control @error('radius_km') is-invalid @enderror" id="radius_km" name="radius_km" value="{{ old('radius_km', $prefs['radius_km'] ?? '') }}" placeholder="Ex: 10">
                        @error('radius_km')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer les préférences
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection