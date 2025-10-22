@extends('layouts.app')

@section('title', $event->titre)

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Événements</a></li>
            <li class="breadcrumb-item active">{{ $event->titre }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <div class="card mb-4">
            @if($event->image)
                <img src="{{ asset('storage/' . $event->image) }}"
                     class="card-img-top"
                     alt="{{ $event->titre }}"
                     style="height: 400px; object-fit: cover;">
            @endif

            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge badge-{{ $event->type_badge_color }} me-2">
                            {{ ucfirst($event->type) }}
                        </span>
                        <span class="badge badge-{{ $event->status_badge_color }}">
                            {{ ucfirst(str_replace('_', ' ', $event->statut)) }}
                        </span>
                    </div>

                    @auth
                        <div class="action-buttons">
                            <a href="{{ route('events.edit', $event) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>

                <h2 class="mb-4">{{ $event->titre }}</h2>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label><i class="fas fa-calendar me-2"></i>Date de début</label>
                            <div class="value-large">
                                {{ $event->date_debut->format('d/m/Y à H:i') }}
                            </div>
                        </div>
                    </div>

                    @if($event->date_fin)
                    <div class="col-md-6">
                        <div class="info-group">
                            <label><i class="fas fa-calendar-check me-2"></i>Date de fin</label>
                            <div class="value-large">
                                {{ $event->date_fin->format('d/m/Y à H:i') }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label><i class="fas fa-map-marker-alt me-2"></i>Lieu</label>
                            <div class="value">{{ $event->lieu }}</div>
                            @if($event->adresse)
                                <small class="text-muted">{{ $event->adresse }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-group">
                            <label><i class="fas fa-users me-2"></i>Organisé par</label>
                            <div class="value">
                                <a href="{{ route('associations.show', $event->association) }}">
                                    {{ $event->association->name }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if($event->capacite_max)
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">
                            <i class="fas fa-users me-2"></i>Capacité de l'événement
                        </h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Inscrits confirmés</span>
                            <strong>{{ $event->confirmedRegistrations->count() }} / {{ $event->capacite_max }}</strong>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar {{ $event->isFull() ? 'bg-danger' : 'bg-success' }}"
                                 role="progressbar"
                                 style="width: {{ ($event->confirmedRegistrations->count() / $event->capacite_max * 100) }}%">
                            </div>
                        </div>
                        @if($event->isFull())
                            <small class="text-danger mt-2 d-block">
                                <i class="fas fa-exclamation-triangle me-1"></i>Événement complet
                            </small>
                        @else
                            <small class="text-success mt-2 d-block">
                                <i class="fas fa-check-circle me-1"></i>{{ $event->available_places }} places disponibles
                            </small>
                        @endif
                    </div>
                </div>
                @endif

                <div class="mb-4">
                    <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Description</h5>
                    <div class="text-muted" style="white-space: pre-line;">{{ $event->description }}</div>
                </div>
            </div>
        </div>

        <!-- Registrations List (Admin View) -->
        @auth
        @if($event->registrations->count() > 0)
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-list-check me-2"></i>
                    Inscriptions ({{ $event->registrations->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Participant</th>
                                <th>Email</th>
                                <th>Date d'inscription</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($event->registrations as $registration)
                            <tr>
                                <td>
                                    <i class="fas fa-user-circle me-2"></i>
                                    {{ $registration->user->name }}
                                </td>
                                <td>{{ $registration->user->email }}</td>
                                <td>{{ $registration->date_inscription->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="status-badge-{{ $registration->statut }}">
                                        {{ $registration->status_display }}
                                    </span>
                                </td>
                                <td>
                                    @if($registration->statut === 'en_attente' && !$event->isFull())
                                        <form action="{{ route('events.update-registration-status', [$event, $registration]) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="statut" value="confirmee">
                                            <button type="submit" class="btn btn-sm btn-success" title="Confirmer">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($registration->statut !== 'annulee')
                                        <form action="{{ route('events.update-registration-status', [$event, $registration]) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="statut" value="annulee">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Annuler">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        @endauth
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Registration Card -->
        @auth
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-ticket-alt me-2"></i>Inscription
                </h5>

                @if($userRegistration)
                    <!-- User is already registered -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Vous êtes inscrit à cet événement
                    </div>

                    <div class="mb-3">
                        <strong>Statut de votre inscription :</strong>
                        <div class="mt-2">
                            <span class="status-badge-{{ $userRegistration->statut }}">
                                {{ $userRegistration->status_display }}
                            </span>
                        </div>
                    </div>

                    @if($userRegistration->commentaire)
                    <div class="mb-3">
                        <strong>Votre commentaire :</strong>
                        <p class="text-muted mb-0">{{ $userRegistration->commentaire }}</p>
                    </div>
                    @endif

                    @if($userRegistration->canBeCancelled())
                        <form action="{{ route('events.cancel-registration', $event) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler votre inscription ?')">
                                <i class="fas fa-times me-2"></i>Annuler mon inscription
                            </button>
                        </form>
                    @endif
                @else
                    <!-- User is not registered -->
                    @if($event->statut === 'annule')
                        <div class="alert alert-danger">
                            <i class="fas fa-ban me-2"></i>
                            Cet événement a été annulé
                        </div>
                    @elseif($event->date_debut->isPast())
                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            Cet événement est terminé
                        </div>
                    @elseif($event->isFull())
                        <div class="alert alert-danger">
                            <i class="fas fa-users me-2"></i>
                            Cet événement est complet
                        </div>
                    @else
                        <form action="{{ route('events.register', $event) }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="commentaire" class="form-label">
                                    Commentaire (optionnel)
                                </label>
                                <textarea class="form-control"
                                          id="commentaire"
                                          name="commentaire"
                                          rows="3"
                                          placeholder="Ajoutez un message ou des questions..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check-circle me-2"></i>S'inscrire à cet événement
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
        @else
        <div class="card mb-4">
            <div class="card-body text-center">
                <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                <h5>Inscription requise</h5>
                <p class="text-muted">Connectez-vous pour vous inscrire à cet événement</p>
                <a href="{{ route('auth.login') }}" class="btn btn-primary w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                </a>
            </div>
        </div>
        @endauth

        <!-- Event Info Card -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-info me-2"></i>Informations
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Type d'événement</small>
                    <strong>{{ ucfirst($event->type) }}</strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Statut</small>
                    <span class="badge badge-{{ $event->status_badge_color }}">
                        {{ ucfirst(str_replace('_', ' ', $event->statut)) }}
                    </span>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Date de création</small>
                    <strong>{{ $event->created_at->format('d/m/Y') }}</strong>
                </div>

                @if($event->capacite_max)
                <div class="mb-3">
                    <small class="text-muted d-block">Capacité</small>
                    <strong>{{ $event->capacite_max }} participants</strong>
                </div>
                @endif

                <div class="mb-0">
                    <small class="text-muted d-block">Organisé par</small>
                    <a href="{{ route('associations.show', $event->association) }}" class="text-decoration-none">
                        <strong>{{ $event->association->name }}</strong>
                    </a>
                </div>
            </div>
        </div>

        <!-- Share Card -->
        <div class="card mt-4">
            <div class="card-body text-center">
                <h6 class="mb-3">
                    <i class="fas fa-share-alt me-2"></i>Partager cet événement
                </h6>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-outline-primary btn-sm" onclick="shareOnFacebook()">
                        <i class="fab fa-facebook-f"></i>
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="shareOnTwitter()">
                        <i class="fab fa-twitter"></i>
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="shareOnWhatsApp()">
                        <i class="fab fa-whatsapp"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="copyLink()">
                        <i class="fas fa-link"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const eventUrl = window.location.href;
const eventTitle = "{{ $event->titre }}";

function shareOnFacebook() {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(eventUrl)}`, '_blank');
}

function shareOnTwitter() {
    window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(eventUrl)}&text=${encodeURIComponent(eventTitle)}`, '_blank');
}

function shareOnWhatsApp() {
    window.open(`https://wa.me/?text=${encodeURIComponent(eventTitle + ' - ' + eventUrl)}`, '_blank');
}

function copyLink() {
    navigator.clipboard.writeText(eventUrl).then(() => {
        alert('Lien copié dans le presse-papier !');
    });
}
</script>
@endsection
