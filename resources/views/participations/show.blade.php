@extends('layouts.app')

@section('title', 'Participation - UrbanGreen')

@section('content')
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