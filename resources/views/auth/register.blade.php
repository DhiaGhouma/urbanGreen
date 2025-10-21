@extends('auth.layout')

@section('title', 'Inscription - UrbanGreen')

@section('content')
<div class="auth-header">
    <div class="auth-logo">
        <i class="fas fa-seedling"></i>
    </div>
    <h1 class="auth-title">Inscription</h1>
    <p class="auth-subtitle">Rejoignez la communauté UrbanGreen</p>
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

    @if($errors->has('registration'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first('registration') }}
        </div>
    @endif

    <form method="POST" action="{{ route('auth.register.post') }}" id="registerForm" novalidate>
        @csrf

        {{-- Name Field --}}
        <div class="form-group">
            <label for="name" class="form-label">
                <i class="fas fa-user me-1"></i>Nom complet
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-id-card"></i>
                </span>
                <input 
                    type="text" 
                    class="form-control @error('name') is-invalid @enderror" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    placeholder="Votre nom complet"
                    required
                    autocomplete="name"
                    autofocus
                    minlength="2"
                    maxlength="255"
                >
            </div>
            @error('name')
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
            <div class="form-text">
                <i class="fas fa-info-circle me-1"></i>Utilisez votre vrai nom pour faciliter les échanges avec la communauté
            </div>
        </div>

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
                    placeholder="Créez un mot de passe sécurisé"
                    required
                    autocomplete="new-password"
                    minlength="8"
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
            
            {{-- Password Strength Indicator --}}
            <div class="password-strength" id="passwordStrength" style="display: none;">
                <div class="strength-bar">
                    <div class="strength-fill" id="strengthFill"></div>
                </div>
                <small class="text-muted" id="strengthText">Force du mot de passe</small>
            </div>
            
            <div class="form-text">
                <i class="fas fa-shield-alt me-1"></i>
                Minimum 8 caractères avec majuscules, minuscules, chiffres et symboles
            </div>
        </div>

        {{-- Password Confirmation Field --}}
        <div class="form-group">
            <label for="password_confirmation" class="form-label">
                <i class="fas fa-lock me-1"></i>Confirmer le mot de passe
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-check-double"></i>
                </span>
                <input 
                    type="password" 
                    class="form-control @error('password_confirmation') is-invalid @enderror" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    placeholder="Répétez votre mot de passe"
                    required
                    autocomplete="new-password"
                >
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                    <i class="fas fa-eye" id="password_confirmation-eye"></i>
                </button>
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
            <div id="passwordMatch" class="form-text"></div>
        </div>

        {{-- Terms and Conditions --}}
        <div class="form-check">
            <input 
                class="form-check-input @error('terms') is-invalid @enderror" 
                type="checkbox" 
                name="terms" 
                id="terms"
                required
                {{ old('terms') ? 'checked' : '' }}
            >
            <label class="form-check-label" for="terms">
                J'accepte les 
                <a href="#" class="auth-link" data-bs-toggle="modal" data-bs-target="#termsModal">
                    conditions d'utilisation
                </a> 
                et la 
                <a href="#" class="auth-link" data-bs-toggle="modal" data-bs-target="#privacyModal">
                    politique de confidentialité
                </a>
            </label>
            @error('terms')
                <div class="invalid-feedback">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn-auth" id="registerBtn">
            <i class="fas fa-user-plus me-2"></i>Créer mon compte
        </button>
    </form>
</div>

<div class="auth-footer">
    <p class="mb-2">
        Déjà un compte ? 
        <a href="{{ route('auth.login') }}" class="auth-link">
            <i class="fas fa-sign-in-alt me-1"></i>Se connecter
        </a>
    </p>
    <p class="mb-0">
        <a href="{{ route('home') }}" class="auth-link">
            <i class="fas fa-arrow-left me-1"></i>Retour à l'accueil
        </a>
    </p>
</div>

{{-- Terms Modal --}}
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--gradient-secondary);">
                <h5 class="modal-title" id="termsModalLabel">
                    <i class="fas fa-file-contract me-2"></i>Conditions d'utilisation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <h6><i class="fas fa-leaf me-2" style="color: var(--accent-green);"></i>Bienvenue sur UrbanGreen</h6>
                <p>En utilisant UrbanGreen, vous acceptez de :</p>
                <ul>
                    <li>Respecter l'environnement et promouvoir le développement durable</li>
                    <li>Partager des informations exactes et utiles avec la communauté</li>
                    <li>Respecter les autres utilisateurs et leurs projets</li>
                    <li>Ne pas utiliser la plateforme à des fins commerciales non autorisées</li>
                    <li>Signaler tout contenu inapproprié ou non conforme à nos valeurs</li>
                </ul>
                <p><strong>Engagement environnemental :</strong> UrbanGreen s'engage à promouvoir des pratiques respectueuses de l'environnement et à soutenir les initiatives de développement durable.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

{{-- Privacy Modal --}}
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--gradient-secondary);">
                <h5 class="modal-title" id="privacyModalLabel">
                    <i class="fas fa-shield-alt me-2"></i>Politique de confidentialité
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <h6><i class="fas fa-user-shield me-2" style="color: var(--accent-green);"></i>Protection de vos données</h6>
                <p>Nous respectons votre vie privée et protégeons vos données personnelles :</p>
                <ul>
                    <li><strong>Collecte :</strong> Nous collectons uniquement les informations nécessaires au fonctionnement du service</li>
                    <li><strong>Utilisation :</strong> Vos données sont utilisées pour améliorer votre expérience et faciliter les échanges communautaires</li>
                    <li><strong>Partage :</strong> Nous ne vendons ni ne partageons vos données avec des tiers non autorisés</li>
                    <li><strong>Sécurité :</strong> Vos données sont protégées par des mesures de sécurité avancées</li>
                    <li><strong>Droits :</strong> Vous pouvez accéder, modifier ou supprimer vos données à tout moment</li>
                </ul>
                <p><strong>Contact :</strong> Pour toute question sur la confidentialité, contactez-nous à privacy@urbangreen.fr</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('registerBtn');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const termsCheckbox = document.getElementById('terms');

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        if (form.checkValidity()) {
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
        }
    });

    // Real-time validation
    nameInput.addEventListener('input', function() {
        validateName(this);
    });

    emailInput.addEventListener('blur', function() {
        validateEmail(this);
    });

    passwordInput.addEventListener('input', function() {
        validatePassword(this);
        checkPasswordMatch();
    });

    passwordConfirmInput.addEventListener('input', function() {
        checkPasswordMatch();
    });

    termsCheckbox.addEventListener('change', function() {
        updateSubmitButton();
    });

    // Name validation
    function validateName(input) {
        const nameRegex = /^[a-zA-ZÀ-ÿ\s\-\'\.]+$/u;
        if (input.value.length >= 2 && nameRegex.test(input.value)) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        } else if (input.value.length > 0) {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
        }
        updateSubmitButton();
    }

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
        updateSubmitButton();
    }

    // Password validation and strength
    function validatePassword(input) {
        const password = input.value;
        const strengthIndicator = document.getElementById('passwordStrength');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');

        if (password.length > 0) {
            strengthIndicator.style.display = 'block';
            
            let score = 0;
            let feedback = [];

            // Length check
            if (password.length >= 8) score += 1;
            else feedback.push('8 caractères minimum');

            // Lowercase check
            if (/[a-z]/.test(password)) score += 1;
            else feedback.push('une minuscule');

            // Uppercase check
            if (/[A-Z]/.test(password)) score += 1;
            else feedback.push('une majuscule');

            // Number check
            if (/\d/.test(password)) score += 1;
            else feedback.push('un chiffre');

            // Special character check
            if (/[^a-zA-Z\d]/.test(password)) score += 1;
            else feedback.push('un symbole');

            // Update strength indicator
            const percentage = (score / 5) * 100;
            strengthFill.style.width = percentage + '%';

            if (score <= 2) {
                strengthFill.className = 'strength-fill strength-weak';
                strengthText.textContent = 'Faible - Manque: ' + feedback.join(', ');
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
            } else if (score === 3) {
                strengthFill.className = 'strength-fill strength-fair';
                strengthText.textContent = 'Moyen - Manque: ' + feedback.join(', ');
                input.classList.remove('is-invalid');
                input.classList.remove('is-valid');
            } else if (score === 4) {
                strengthFill.className = 'strength-fill strength-good';
                strengthText.textContent = 'Bon - Manque: ' + feedback.join(', ');
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else {
                strengthFill.className = 'strength-fill strength-strong';
                strengthText.textContent = 'Excellent - Mot de passe sécurisé!';
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            }
        } else {
            strengthIndicator.style.display = 'none';
            input.classList.remove('is-valid', 'is-invalid');
        }
        updateSubmitButton();
    }

    // Password match validation
    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = passwordConfirmInput.value;
        const matchDiv = document.getElementById('passwordMatch');

        if (confirmPassword.length > 0) {
            if (password === confirmPassword) {
                passwordConfirmInput.classList.remove('is-invalid');
                passwordConfirmInput.classList.add('is-valid');
                matchDiv.innerHTML = '<i class="fas fa-check-circle me-1" style="color: var(--accent-green);"></i>Les mots de passe correspondent';
                matchDiv.style.color = 'var(--accent-green)';
            } else {
                passwordConfirmInput.classList.remove('is-valid');
                passwordConfirmInput.classList.add('is-invalid');
                matchDiv.innerHTML = '<i class="fas fa-exclamation-circle me-1" style="color: #dc3545;"></i>Les mots de passe ne correspondent pas';
                matchDiv.style.color = '#dc3545';
            }
        } else {
            passwordConfirmInput.classList.remove('is-valid', 'is-invalid');
            matchDiv.innerHTML = '';
        }
        updateSubmitButton();
    }

    // Update submit button state
    function updateSubmitButton() {
        const isFormValid = 
            nameInput.classList.contains('is-valid') &&
            emailInput.classList.contains('is-valid') &&
            passwordInput.classList.contains('is-valid') &&
            passwordConfirmInput.classList.contains('is-valid') &&
            termsCheckbox.checked;

        submitBtn.disabled = !isFormValid;
        if (isFormValid) {
            submitBtn.style.opacity = '1';
        } else {
            submitBtn.style.opacity = '0.6';
        }
    }

    // Auto-hide alerts
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
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('registerBtn');
    if (btn.classList.contains('btn-loading')) {
        e.preventDefault();
        return false;
    }
});
</script>
@endsection