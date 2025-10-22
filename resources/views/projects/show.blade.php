@extends('layouts.app')

@section('title', $project->title . ' - UrbanGreen')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projets</a></li>
            <li class="breadcrumb-item active">{{ $project->title }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1><i class="fas fa-project-diagram me-2"></i>{{ $project->title }}</h1>
            <span class="badge project-status-badge {{ $project->getStatusBadgeClass() }} fs-6">
                {{ ucfirst($project->status) }}
            </span>
        </div>
        <div>
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informations du projet -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Description du Projet</h5>
            </div>
            <div class="card-body">
                <p class="lead">{{ $project->description }}</p>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="fw-bold text-muted">Association</label>
                            <div>
                                <a href="{{ route('associations.show', $project->association) }}"
                                   class="text-decoration-none">
                                    <i class="fas fa-users me-2"></i>{{ $project->association->name }}
                                </a>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold text-muted">Espace Vert</label>
                            <div>
                                <i class="fas fa-map-marker-alt me-2"></i>{{ $project->greenSpace->name }}
                                <small class="d-block text-muted">{{ $project->greenSpace->location }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="fw-bold text-muted">Budget Estimé</label>
                            <div class="fs-4 fw-bold text-success">
                                <i class="fas fa-euro-sign me-1"></i>{{ number_format($project->estimated_budget, 2) }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold text-muted">Créé le</label>
                            <div><i class="fas fa-calendar me-2"></i>{{ $project->created_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Participation -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-hands-helping me-2"></i>Participation</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-success w-100" type="button">
                            <i class="fas fa-hand-holding-heart me-2"></i>Participer comme bénévole
                        </button>
                        <small class="text-muted d-block mt-1">
                            Rejoignez l'équipe de bénévoles pour ce projet
                        </small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-primary w-100" type="button">
                            <i class="fas fa-donate me-2"></i>Contribuer au financement
                        </button>
                        <small class="text-muted d-block mt-1">
                            Soutenez financièrement ce projet
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Discussion -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-comments me-2"></i>Discussion du Projet
                    <span class="badge bg-secondary ms-2">{{ $project->messages->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @auth
                    <!-- Message Form -->
                    <form method="POST" action="{{ route('projects.messages.store', $project) }}" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <textarea
                                name="message"
                                class="form-control @error('message') is-invalid @enderror"
                                rows="3"
                                placeholder="Écrivez votre message..."
                                required
                            ></textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer
                        </button>
                    </form>
                @else
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <a href="{{ route('login') }}">Connectez-vous</a> pour participer à la discussion.
                    </div>
                @endauth

                <hr>

                <!-- Messages List -->
                <div class="messages-list">
                    @forelse($project->messages as $message)
                        <div class="message-item border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-circle me-2">
                                            {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $message->user->name }}</strong>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-clock me-1"></i>{{ $message->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                    <p class="mb-0 ms-5">{{ $message->message }}</p>
                                </div>

                                @auth
                                    @if($message->user_id === auth()->id())
                                        <form method="POST" action="{{ route('projects.messages.destroy', [$project, $message]) }}"
                                              class="ms-2"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-comments fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">Aucun message pour le moment. Soyez le premier à participer !</p>
                        </div>
                    @endforelse
                </div>

                @if($project->messages->count() > 5)
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-arrow-up me-1"></i>Faites défiler pour voir tous les messages
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Statistiques -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistiques</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="stats-card">
                        <div class="fs-3 fw-bold">{{ number_format($project->estimated_budget, 0) }} €</div>
                        <div>Budget Total</div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Progression</span>
                    @php
                        $progress = match($project->status) {
                            'proposé' => 25,
                            'en cours' => 60,
                            'terminé' => 100,
                            default => 0
                        };
                    @endphp
                    <span class="fw-bold">{{ $progress }}%</span>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-comments me-2"></i>Messages</span>
                    <span class="badge bg-primary">{{ $project->messages->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Actions Rapides</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit me-2"></i>Modifier le projet
                    </a>
                    <button class="btn btn-outline-info btn-sm" type="button">
                        <i class="fas fa-share-alt me-2"></i>Partager
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" type="button">
                        <i class="fas fa-download me-2"></i>Exporter PDF
                    </button>
                </div>

                <hr>

                <form method="POST" action="{{ route('projects.destroy', $project) }}"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="fas fa-trash me-2"></i>Supprimer le projet
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.message-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.messages-list {
    max-height: 600px;
    overflow-y: auto;
}

.messages-list::-webkit-scrollbar {
    width: 8px;
}

.messages-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.messages-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.messages-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection
