@extends('auth.layout')

@section('title', 'Connexion - UrbanGreen')

@section('content')
<div class="auth-header">
    <div class="auth-logo">
        <i class="fas fa-leaf"></i>
    </div>
    <h1 class="auth-title">Connexion</h1>
    <p class="auth-subtitle">Accédez à votre espace UrbanGreen</p>
</div>

<div class="auth-body">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
        </div>
    @endif

    <form method="POST" action="{{ route('auth.login.post') }}" id="loginForm" novalidate>
        @csrf

        {{-- Email Field --}}
        <div class="form-group">
            <label for="email" class="form-label">
                <i class="fas fa-envelope me-1"></i>Adresse e-mail
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-at"></i>
                </span>
                <input 
                    type="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="votre@email.com"
                    required
                    autocomplete="email"
                    autofocus
                >
            </div>
            @error('email')
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        {{-- Password Field --}}
        <div class="form-group">
            <label for="password" class="form-label">
                <i class="fas fa-lock me-1"></i>Mot de passe
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-key"></i>
                </span>
                <input 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    id="password" 
                    name="password" 
                    placeholder="Votre mot de passe"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                    <i class="fas fa-eye" id="password-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="form-check">
            <input 
                class="form-check-input" 
                type="checkbox" 
                name="remember" 
                id="remember"
                {{ old('remember') ? 'checked' : '' }}
            >
            <label class="form-check-label" for="remember">
                Se souvenir de moi
            </label>
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn-auth" id="loginBtn">
            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
        </button>
    </form>
</div>

<div class="auth-footer">
    <p class="mb-2">
        Pas encore de compte ? 
        <a href="{{ route('auth.register') }}" class="auth-link">
            <i class="fas fa-user-plus me-1"></i>Créer un compte
        </a>
    </p>
    <p class="mb-0">
        <a href="{{ route('home') }}" class="auth-link">
            <i class="fas fa-arrow-left me-1"></i>Retour à l'accueil
        </a>
    </p>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('loginBtn');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        if (form.checkValidity()) {
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
        }
    });

    // Real-time validation feedback
    emailInput.addEventListener('blur', function() {
        validateEmail(this);
    });

    passwordInput.addEventListener('input', function() {
        if (this.value.length > 0) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // Email validation
    function validateEmail(input) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (input.value && emailRegex.test(input.value)) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        } else if (input.value) {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
        }
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
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

// Prevent multiple form submissions
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('loginBtn');
    if (btn.classList.contains('btn-loading')) {
        e.preventDefault();
        return false;
    }
});
</script>
@endsection