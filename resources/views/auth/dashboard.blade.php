@extends('layouts.app')

@section('title', 'Tableau de bord - UrbanGreen')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 mb-2">
                <i class="fas fa-tachometer-alt me-2" style="color: var(--primary-green);"></i>
                Tableau de bord
            </h1>
            <p class="text-muted mb-0">
                Bienvenue {{ Auth::user()->name }}, gérez votre espace UrbanGreen
            </p>
        </div>
        <div class="text-end">
            <div class="badge bg-success fs-6 px-3 py-2">
                <i class="fas fa-user-shield me-1"></i>{{ Auth::user()->getRoleDisplayAttribute() }}
            </div>
        </div>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    {{-- User Stats Cards --}}
    <div class="col-md-4 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-calendar-check fa-3x mb-3"></i>
                <h3 class="mb-1">{{ $recentActivity['total_participations'] }}</h3>
                <p class="mb-0">Participations</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-3x mb-3"></i>
                <h3 class="mb-1">
                    {{ $recentActivity['last_login'] ? $recentActivity['last_login']->diffForHumans() : 'Jamais' }}
                </h3>
                <p class="mb-0">Dernière connexion</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-user-plus fa-3x mb-3"></i>
                <h3 class="mb-1">{{ $recentActivity['member_since']->diffForHumans() }}</h3>
                <p class="mb-0">Membre depuis</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- User Profile Card --}}
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-edit me-2"></i>Profil utilisateur
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('auth.profile.update') }}" id="profileForm">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="profile_name" class="form-label">
                            <i class="fas fa-user me-1"></i>Nom complet
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="profile_name" 
                            name="name" 
                            value="{{ old('name', Auth::user()->name) }}"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="profile_email" class="form-label">
                            <i class="fas fa-envelope me-1"></i>Adresse e-mail
                        </label>
                        <input 
                            type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            id="profile_email" 
                            name="email" 
                            value="{{ old('email', Auth::user()->email) }}"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-shield-alt me-1"></i>Statut du compte
                        </label>
                        <div class="d-flex align-items-center">
                            @if(Auth::user()->email_verified_at)
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-check-circle me-1"></i>Vérifié
                                </span>
                            @else
                                <span class="badge bg-warning me-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Non vérifié
                                </span>
                            @endif
                            
                            @if(Auth::user()->isLocked())
                                <span class="badge bg-danger">
                                    <i class="fas fa-lock me-1"></i>Verrouillé
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="fas fa-unlock me-1"></i>Actif
                                </span>
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Mettre à jour le profil
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Password Change Card --}}
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-key me-2"></i>Changer le mot de passe
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('auth.password.change') }}" id="passwordForm">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">
                            <i class="fas fa-lock me-1"></i>Mot de passe actuel
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                class="form-control @error('current_password') is-invalid @enderror" 
                                id="current_password" 
                                name="current_password"
                                required
                            >
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('current_password')">
                                <i class="fas fa-eye" id="current_password-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">
                            <i class="fas fa-key me-1"></i>Nouveau mot de passe
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                id="new_password" 
                                name="password"
                                required
                                minlength="8"
                            >
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('new_password')">
                                <i class="fas fa-eye" id="new_password-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Minimum 8 caractères avec majuscules, minuscules, chiffres et symboles
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-check-double me-1"></i>Confirmer le nouveau mot de passe
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password_confirmation" 
                                name="password_confirmation"
                                required
                            >
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye" id="password_confirmation-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-shield-alt me-2"></i>Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Actions rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('associations.index') }}" class="btn btn-outline-primary btn-lg w-100">
                            <i class="fas fa-users fa-2x mb-2 d-block"></i>
                            <span>Associations</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-success btn-lg w-100">
                            <i class="fas fa-project-diagram fa-2x mb-2 d-block"></i>
                            <span>Projets</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('greenspaces.index') }}" class="btn btn-outline-info btn-lg w-100">
                            <i class="fas fa-tree fa-2x mb-2 d-block"></i>
                            <span>Espaces verts</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('participations.index') }}" class="btn btn-outline-warning btn-lg w-100">
                            <i class="fas fa-calendar-check fa-2x mb-2 d-block"></i>
                            <span>Participations</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Security Info --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="fas fa-shield-alt me-2"></i>Informations de sécurité
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Dernière connexion :</strong> 
                            {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('d/m/Y à H:i') : 'Inconnue' }}
                        </p>
                        <p><strong>Tentatives de connexion échouées :</strong> 
                            <span class="badge {{ Auth::user()->failed_login_attempts > 0 ? 'bg-warning' : 'bg-success' }}">
                                {{ Auth::user()->failed_login_attempts }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Compte créé le :</strong> {{ Auth::user()->created_at->format('d/m/Y à H:i') }}</p>
                        <p><strong>E-mail vérifié :</strong> 
                            @if(Auth::user()->email_verified_at)
                                <span class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>Oui, le {{ Auth::user()->email_verified_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Non vérifié
                                </span>
                            @endif
                        </p>
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
    // Form validation
    const profileForm = document.getElementById('profileForm');
    const passwordForm = document.getElementById('passwordForm');

    // Profile form submission
    profileForm.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mise à jour...';
        submitBtn.disabled = true;
    });

    // Password form submission
    passwordForm.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas!');
            return false;
        }

        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Changement...';
        submitBtn.disabled = true;
    });

    // Real-time password confirmation
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('password_confirmation');

    function checkPasswordMatch() {
        if (confirmPasswordInput.value && newPasswordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.setCustomValidity('Les mots de passe ne correspondent pas');
            confirmPasswordInput.classList.add('is-invalid');
        } else {
            confirmPasswordInput.setCustomValidity('');
            confirmPasswordInput.classList.remove('is-invalid');
        }
    }

    newPasswordInput.addEventListener('input', checkPasswordMatch);
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);
});

// Password visibility toggle
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection