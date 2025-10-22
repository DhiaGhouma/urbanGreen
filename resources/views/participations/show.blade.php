@extends('layouts.app')

@section('title', 'Participation - UrbanGreen')

@section('content')
@php
    $feedback = $participation->feedback;
    $canManageFeedback = auth()->check() && (auth()->user()->isAdmin() || auth()->id() === $participation->user_id);
    $canLeaveFeedback = $canManageFeedback && $participation->statut === 'terminee';
@endphp
<style>
    .star-rating {
        display: inline-flex;
        gap: 0.35rem;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        font-size: 1.75rem;
        color: #d1d5db;
        cursor: pointer;
        transition: color 0.2s ease-in-out;
    }

    .star-rating label.active {
        color: #fbbf24;
    }
</style>
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('participations.index') }}">Participations</a></li>
            <li class="breadcrumb-item active">Détails</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1><i class="fas fa-hand-holding-heart me-2"></i>Participation</h1>
        </div>
        <div>
            <a href="{{ route('participations.edit', $participation) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
            <a href="{{ route('participations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informations principales -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Informations de la Participation</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label>Participant</label>
                            <div class="d-flex align-items-center mt-2">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    {{ strtoupper(substr($participation->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $participation->user->name }}</h6>
                                    <small class="text-muted">{{ $participation->user->email }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label>Espace Vert</label>
                            <div class="mt-2">
                                <h6 class="mb-1">{{ $participation->greenSpace->name }}</h6>
                                <div class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $participation->greenSpace->location }}
                                </div>
                                <div class="text-muted">
                                    <i class="fas fa-leaf me-1"></i>{{ $participation->greenSpace->type }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-4">
                        <div class="info-group">
                            <label>Date de Participation</label>
                            <div class="value-large">{{ $participation->date->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $participation->date->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-group">
                            <label>Statut</label>
                            <div class="mt-2">
                                <span class="badge status-badge-{{ $participation->statut }} fs-6">
                                    {{ $participation->getStatutLabel() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-group">
                            <label>Créé le</label>
                            <div class="value">{{ $participation->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $participation->created_at->format('H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-comment-dots me-2"></i>Mon retour d'expérience</h5>
                @if($feedback && $canLeaveFeedback)
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#feedbackEditForm" aria-expanded="false" aria-controls="feedbackEditForm">
                        <i class="fas fa-pen me-1"></i>Modifier
                    </button>
                @endif
            </div>
            <div class="card-body">
                @if($feedback)
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }} me-1"></i>
                            @endfor
                        </div>
                        <p class="mb-2 text-muted">
                            <i class="fas fa-calendar-day me-2"></i>Publié le {{ $feedback->created_at->format('d/m/Y à H:i') }}
                        </p>
                        <p class="lead" style="white-space: pre-line;">{{ $feedback->comment }}</p>
                    </div>

                    @if($canLeaveFeedback)
                        <div class="collapse" id="feedbackEditForm">
                            <form action="{{ route('participations.feedback.update', $participation) }}" method="POST" class="border-top pt-3 mb-3">
                                @csrf
                                @method('PATCH')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Votre note</label>
                                    <div class="star-rating" data-initial="{{ $feedback->rating }}">
                                        @for($i = 1; $i <= 5; $i++)
                                            <input type="radio" id="edit-rating-{{ $i }}" name="rating" value="{{ $i }}" {{ $feedback->rating === $i ? 'checked' : '' }}>
                                            <label for="edit-rating-{{ $i }}" class="fas fa-star"></label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="edit-comment" class="form-label fw-semibold">Votre commentaire</label>
                                    <textarea id="edit-comment" name="comment" class="form-control" rows="4" required>{{ old('comment', $feedback->comment) }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
                                </button>
                            </form>
                            <form action="{{ route('participations.feedback.destroy', $participation) }}" method="POST" onsubmit="return confirm('Supprimer votre avis ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fas fa-trash me-2"></i>Supprimer
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <p class="text-muted mb-0">
                        @if($canLeaveFeedback)
                            Partagez votre expérience pour aider d'autres volontaires !
                        @elseif($participation->statut !== 'terminee')
                            Vous pourrez laisser un avis une fois votre participation terminée.
                        @else
                            L'avis de cette participation sera bientôt disponible.
                        @endif
                    </p>

                    @if($canLeaveFeedback)
                        <form action="{{ route('participations.feedback.store', $participation) }}" method="POST" class="mt-3">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Votre note</label>
                                <div class="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <input type="radio" id="create-rating-{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required>
                                        <label for="create-rating-{{ $i }}" class="fas fa-star"></label>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="create-comment" class="form-label fw-semibold">Votre commentaire</label>
                                <textarea id="create-comment" name="comment" class="form-control" rows="4" required>{{ old('comment') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Publier mon avis
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-cogs me-2"></i>Actions Rapides</h5>
            </div>
            <div class="card-body">
                <!-- Quick Status Update -->
                <form action="{{ route('participations.updateStatus', $participation) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-3">
                        <label for="statut" class="form-label">Changer le statut</label>
                        <select name="statut" id="statut" class="form-select">
                            <option value="en_attente" {{ $participation->statut === 'en_attente' ? 'selected' : '' }}>
                                En Attente
                            </option>
                            <option value="confirmee" {{ $participation->statut === 'confirmee' ? 'selected' : '' }}>
                                Confirmée
                            </option>
                            <option value="annulee" {{ $participation->statut === 'annulee' ? 'selected' : '' }}>
                                Annulée
                            </option>
                            <option value="terminee" {{ $participation->statut === 'terminee' ? 'selected' : '' }}>
                                Terminée
                            </option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-sync-alt me-2"></i>Mettre à jour le statut
                    </button>
                </form>

                <hr>

                <div class="d-grid gap-2">
                    <a href="{{ route('participations.edit', $participation) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Modifier la participation
                    </a>
                    
                    <form action="{{ route('participations.destroy', $participation) }}" method="POST" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette participation?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations supplémentaires -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-clock me-2"></i>Historique</h5>
            </div>
            <div class="card-body">
                <div class="timeline-item">
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Participation créée</h6>
                        <small class="text-muted">{{ $participation->created_at->format('d/m/Y à H:i') }}</small>
                    </div>
                </div>
                @if($participation->updated_at != $participation->created_at)
                <div class="timeline-item">
                    <div class="timeline-marker bg-info"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Dernière modification</h6>
                        <small class="text-muted">{{ $participation->updated_at->format('d/m/Y à H:i') }}</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

        @section('scripts')
            @parent
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('.star-rating').forEach(function (container) {
                        const inputs = container.querySelectorAll('input[type="radio"]');
                        const labels = container.querySelectorAll('label');
                        const initial = Number(container.dataset.initial || 0);

                        function setActive(value) {
                            labels.forEach(function (label) {
                                const targetId = label.getAttribute('for');
                                const input = container.querySelector('#' + targetId);
                                if (!input) {
                                    return;
                                }
                                label.classList.toggle('active', Number(input.value) <= value);
                            });
                        }

                        if (initial > 0) {
                            setActive(initial);
                        }

                        const checked = container.querySelector('input:checked');
                        if (checked) {
                            setActive(Number(checked.value));
                        }

                        inputs.forEach(function (input) {
                            input.addEventListener('change', function () {
                                setActive(Number(this.value));
                            });
                        });

                        labels.forEach(function (label) {
                            const targetId = label.getAttribute('for');
                            const relatedInput = container.querySelector('#' + targetId);
                            if (!relatedInput) {
                                return;
                            }
                            label.addEventListener('mouseenter', function () {
                                setActive(Number(relatedInput.value));
                            });
                            label.addEventListener('click', function () {
                                relatedInput.checked = true;
                                relatedInput.dispatchEvent(new Event('change'));
                            });
                        });

                        container.addEventListener('mouseleave', function () {
                            const current = container.querySelector('input:checked');
                            if (current) {
                                setActive(Number(current.value));
                            } else if (initial > 0) {
                                setActive(initial);
                            } else {
                                setActive(0);
                            }
                        });
                    });
                });
            </script>
        @endsection