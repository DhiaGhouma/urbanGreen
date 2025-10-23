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
        <div class="card mb-4 project-card">
            <div class="card-header bg-gradient">
                <h5 class="mb-0 text-white"><i class="fas fa-info-circle me-2"></i>Description du Projet</h5>
            </div>
            <div class="card-body">
                <p class="lead">{{ $project->description }}</p>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="info-box mb-3">
                            <label class="fw-bold text-muted mb-2">
                                <i class="fas fa-users text-primary me-2"></i>Association
                            </label>
                            <div>
                                <a href="{{ route('associations.show', $project->association) }}"
                                   class="text-decoration-none association-link">
                                    {{ $project->association->name }}
                                </a>
                            </div>
                        </div>
                        <div class="info-box mb-3">
                            <label class="fw-bold text-muted mb-2">
                                <i class="fas fa-map-marker-alt text-success me-2"></i>Espace Vert
                            </label>
                            <div>
                                <div class="fw-semibold">{{ $project->greenSpace->name }}</div>
                                <small class="text-muted">{{ $project->greenSpace->location }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box mb-3">
                            <label class="fw-bold text-muted mb-2">
                                <i class="fas fa-euro-sign text-warning me-2"></i>Budget Estimé
                            </label>
                            <div class="fs-3 fw-bold text-success">
                                {{ number_format($project->estimated_budget, 2) }} €
                            </div>
                        </div>
                        <div class="info-box mb-3">
                            <label class="fw-bold text-muted mb-2">
                                <i class="fas fa-calendar text-info me-2"></i>Créé le
                            </label>
                            <div>{{ $project->created_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Participation -->
        <div class="card mb-4 participation-card">
            <div class="card-header bg-gradient">
                <h5 class="mb-0 text-white"><i class="fas fa-hands-helping me-2"></i>Participation</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <button class="btn btn-volunteer w-100 py-3" type="button">
                            <i class="fas fa-hand-holding-heart me-2 fs-5"></i>
                            <span class="d-block">Participer comme bénévole</span>
                        </button>
                        <small class="text-muted d-block mt-2 text-center">
                            Rejoignez l'équipe de bénévoles
                        </small>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-donate w-100 py-3" type="button">
                            <i class="fas fa-donate me-2 fs-5"></i>
                            <span class="d-block">Contribuer au financement</span>
                        </button>
                        <small class="text-muted d-block mt-2 text-center">
                            Soutenez financièrement ce projet
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Discussion - Enhanced Design -->
        <div class="card discussion-card">
            <div class="card-header discussion-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-comments me-2"></i>Discussion du Projet
                    </h5>
                    <span class="badge bg-white text-primary px-3 py-2">
                        <i class="fas fa-comment-dots me-1"></i>
                        {{ $project->messages->count() }} {{ $project->messages->count() > 1 ? 'messages' : 'message' }}
                    </span>
                </div>
            </div>
            <div class="card-body discussion-body">
                @auth
                    <!-- Message Form - Enhanced -->
                    <div class="message-form-container mb-4">
                        <form method="POST" action="{{ route('projects.messages.store', $project) }}" id="messageForm">
                            @csrf
                            <div class="message-input-wrapper">
                                <div class="user-avatar-small">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <textarea
                                    name="message"
                                    id="messageInput"
                                    class="form-control message-textarea @error('message') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Partagez vos idées, questions ou suggestions..."
                                    required
                                ></textarea>
                            </div>
                            @error('message')
                                <div class="text-danger small mt-2 ms-5">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="d-flex justify-content-between align-items-center mt-3 ms-5">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Maximum 1000 caractères
                                </small>
                                <button type="submit" class="btn btn-send-message">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer le message
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="auth-prompt">
                        <div class="auth-prompt-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h6 class="mb-2">Rejoignez la conversation</h6>
                        <p class="text-muted mb-3">
                            Connectez-vous pour partager vos idées et participer à la discussion
                        </p>
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </a>
                    </div>
                @endauth

                @if($project->messages->count() > 0)
                    <div class="messages-divider">
                        <span>Messages de la communauté</span>
                    </div>
                @endif

                <!-- Messages List - Enhanced -->
                <div class="messages-list" id="messagesList">
                    @forelse($project->messages as $message)
                        <div class="message-bubble animate-in">
                            <div class="message-header">
                                <div class="d-flex align-items-center">
                                    <div class="message-avatar">
                                        {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                    </div>
                                    <div class="message-meta">
                                        <div class="message-author">{{ $message->user->name }}</div>
                                        <div class="message-time">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $message->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>

                                @auth
                                    @if($message->user_id === auth()->id())
                                        <form method="POST"
                                              action="{{ route('projects.messages.destroy', [$project, $message]) }}"
                                              class="delete-message-form"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete-message" title="Supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                            <div class="message-content">
                                {{ $message->message }}
                            </div>
                        </div>
                    @empty
                        <div class="empty-messages">
                            <div class="empty-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <h6>Aucun message pour le moment</h6>
                            <p class="text-muted">Soyez le premier à lancer la discussion !</p>
                        </div>
                    @endforelse
                </div>

                @if($project->messages->count() > 5)
                    <div class="scroll-indicator">
                        <i class="fas fa-chevron-down me-2"></i>
                        Faites défiler pour voir plus de messages
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Statistiques -->
        <div class="card stats-card mb-4">
            <div class="card-header stats-header">
                <h6 class="mb-0 text-white"><i class="fas fa-chart-bar me-2"></i>Statistiques</h6>
            </div>
            <div class="card-body">
                <div class="stat-item-large">
                    <div class="stat-icon">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                    <div class="stat-value">{{ number_format($project->estimated_budget, 0) }} €</div>
                    <div class="stat-label">Budget Total</div>
                </div>

                <div class="progress-section mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold">Progression</span>
                        @php
                            $progress = match($project->status) {
                                'proposé' => 25,
                                'en cours' => 60,
                                'terminé' => 100,
                                default => 0
                            };
                        @endphp
                        <span class="badge bg-primary">{{ $progress }}%</span>
                    </div>
                    <div class="progress custom-progress">
                        <div class="progress-bar progress-bar-animated"
                             style="width: {{ $progress }}%"
                             role="progressbar"
                             aria-valuenow="{{ $progress }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="stat-item">
                    <i class="fas fa-comments stat-item-icon"></i>
                    <div class="stat-item-content">
                        <div class="stat-item-label">Messages</div>
                        <div class="stat-item-value">{{ $project->messages->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card actions-card">
            <div class="card-header actions-header">
                <h6 class="mb-0 text-white"><i class="fas fa-tools me-2"></i>Actions Rapides</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-action btn-action-warning">
                        <i class="fas fa-edit me-2"></i>Modifier le projet
                    </a>
                    <button class="btn btn-action btn-action-info" type="button">
                        <i class="fas fa-share-alt me-2"></i>Partager
                    </button>
                    <button class="btn btn-action btn-action-secondary" type="button">
                        <i class="fas fa-download me-2"></i>Exporter PDF
                    </button>
                </div>

                <hr class="my-3">

                <form method="POST" action="{{ route('projects.destroy', $project) }}"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-action btn-action-danger w-100">
                        <i class="fas fa-trash me-2"></i>Supprimer le projet
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* ============================================
   ENHANCED PROJECT CARD STYLES
   ============================================ */

.project-card,
.participation-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.project-card:hover,
.participation-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
}

.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.info-box {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.info-box:hover {
    background: #e9ecef;
}

.association-link {
    color: #667eea;
    font-weight: 600;
    transition: color 0.3s ease;
}

.association-link:hover {
    color: #764ba2;
}

/* ============================================
   PARTICIPATION BUTTONS
   ============================================ */

.btn-volunteer,
.btn-donate {
    font-weight: 600;
    border: none;
    border-radius: 10px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-volunteer {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.btn-volunteer:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(17, 153, 142, 0.4);
    color: white;
}

.btn-donate {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-donate:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    color: white;
}

/* ============================================
   DISCUSSION CARD - ENHANCED DESIGN
   ============================================ */

.discussion-card {
    border: none;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    overflow: hidden;
}

.discussion-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    border: none;
}

.discussion-body {
    background: #f8f9fc;
    padding: 25px;
}

/* ============================================
   MESSAGE FORM - MODERN DESIGN
   ============================================ */

.message-form-container {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.message-input-wrapper {
    display: flex;
    gap: 15px;
    align-items: start;
}

.user-avatar-small {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
    flex-shrink: 0;
    box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
}

.message-textarea {
    flex: 1;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    resize: none;
    transition: all 0.3s ease;
    padding: 12px 15px;
    font-size: 0.95rem;
}

.message-textarea:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
}

.btn-send-message {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-send-message:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

/* ============================================
   AUTH PROMPT
   ============================================ */

.auth-prompt {
    text-align: center;
    padding: 40px 20px;
    background: white;
    border-radius: 12px;
    margin-bottom: 25px;
}

.auth-prompt-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 1.8rem;
    color: white;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
}

/* ============================================
   MESSAGES DIVIDER
   ============================================ */

.messages-divider {
    text-align: center;
    position: relative;
    margin: 30px 0 25px;
}

.messages-divider span {
    background: #f8f9fc;
    padding: 0 15px;
    color: #6c757d;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    z-index: 1;
}

.messages-divider::before {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #dee2e6, transparent);
}

/* ============================================
   MESSAGE BUBBLES - ENHANCED
   ============================================ */

.messages-list {
    max-height: 600px;
    overflow-y: auto;
    padding-right: 10px;
}

.message-bubble {
    background: white;
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.message-bubble:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-left-color: #667eea;
    transform: translateX(3px);
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 12px;
}

.message-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
    flex-shrink: 0;
    margin-right: 12px;
    box-shadow: 0 3px 10px rgba(17, 153, 142, 0.3);
}

.message-meta {
    flex: 1;
}

.message-author {
    font-weight: 700;
    color: #2d3748;
    font-size: 0.95rem;
    margin-bottom: 2px;
}

.message-time {
    font-size: 0.8rem;
    color: #718096;
}

.message-content {
    color: #4a5568;
    line-height: 1.6;
    padding-left: 57px;
    font-size: 0.95rem;
}

.btn-delete-message {
    background: none;
    border: none;
    color: #cbd5e0;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.btn-delete-message:hover {
    background: #fee;
    color: #e53e3e;
}

/* ============================================
   EMPTY STATE
   ============================================ */

.empty-messages {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #e0e7ff 0%, #f3e8ff 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 2rem;
    color: #667eea;
}

/* ============================================
   SCROLL INDICATOR
   ============================================ */

.scroll-indicator {
    text-align: center;
    padding: 15px;
    color: #718096;
    font-size: 0.85rem;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

/* ============================================
   CUSTOM SCROLLBAR
   ============================================ */

.messages-list::-webkit-scrollbar {
    width: 8px;
}

.messages-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.messages-list::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

.messages-list::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

/* ============================================
   STATS CARD - ENHANCED
   ============================================ */

.stats-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    border-radius: 12px;
    overflow: hidden;
}

.stats-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 15px 20px;
}

.stat-item-large {
    text-align: center;
    padding: 25px;
    background: linear-gradient(135deg, #f8f9fc 0%, #fff 100%);
    border-radius: 10px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    color: #2d3748;
    margin-bottom: 5px;
}

.stat-label {
    color: #718096;
    font-size: 0.9rem;
    font-weight: 600;
}

.custom-progress {
    height: 10px;
    border-radius: 10px;
    background: #e9ecef;
    overflow: hidden;
}

.custom-progress .progress-bar {
    background: linear-gradient(90deg, #33694B 0%, #BAE8B6 100%);
    border-radius: 10px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px;
    background: #f8f9fc;
    border-radius: 8px;
}

.stat-item-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #33694B 0%, #1BAF8A 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
}

.stat-item-content {
    flex: 1;
}

.stat-item-label {
    color: #718096;
    font-size: 0.85rem;
    margin-bottom: 2px;
}

.stat-item-value {
    font-size: 1.3rem;
    font-weight: bold;
    color: #2d3748;
}

/* ============================================
   ACTIONS CARD
   ============================================ */

.actions-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    border-radius: 12px;
    overflow: hidden;
}

.actions-header {
    background: linear-gradient(135deg, #33694B 0%, #BAE8B6 100%);
    padding: 15px 20px;
}

.btn-action {
    border: none;
    padding: 12px 20px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-action-warning {
    background: linear-gradient(135deg, #c0b92eff 0%, #e6e143ff 100%);
    color: white;
}

.btn-action-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(240, 147, 251, 0.4);
    color: white;
}

.btn-action-info {
    background: linear-gradient(135deg, #23712cff 0%, #e4eee5ff 100%);
    color: white;
}

.btn-action-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
    color: white;
}

.btn-action-secondary {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    color: #2d3748;
}

.btn-action-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(168, 237, 234, 0.4);
}

.btn-action-danger {
    background: linear-gradient(135deg, #ef1717ff 0%, #f5576c 100%);
    color: white;
}

.btn-action-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
}

/* ============================================
   ANIMATIONS
   ============================================ */

.animate-in {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ============================================
   RESPONSIVE DESIGN
   ============================================ */

@media (max-width: 768px) {
    .message-input-wrapper {
        flex-direction: column;
    }

    .user-avatar-small {
        align-self: center;
    }

    .message-textarea {
        width: 100%;
    }

    .message-content {
        padding-left: 0;
        margin-top: 10px;
    }

    .stat-item-large {
        padding: 20px;
    }

    .stat-value {
        font-size: 1.5rem;
    }
}

/* ============================================
   HOVER EFFECTS & MICRO-INTERACTIONS
   ============================================ */

.card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.message-bubble::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #33694B 0%, #BAE8B6 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.message-bubble:hover::before {
    opacity: 1;
}

/* ============================================
   LOADING STATE (for future AJAX)
   ============================================ */

.message-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    color: #1BAF8A;
}

.spinner-dot {
    width: 8px;
    height: 8px;
    background: #1BAF8A;
    border-radius: 50%;
    margin: 0 4px;
    animation: pulse 1.4s infinite ease-in-out;
}

.spinner-dot:nth-child(2) {
    animation-delay: -0.2s;
}

.spinner-dot:nth-child(3) {
    animation-delay: -0.4s;
}

@keyframes pulse {
    0%, 80%, 100% {
        transform: scale(0.6);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>

@endsection
