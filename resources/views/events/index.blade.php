@extends('layouts.app')

@section('title', 'Événements et Ateliers')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">
                <i class="fas fa-calendar-alt me-2"></i>Événements et Ateliers
            </h1>
            <p class="text-muted mb-0">Participez aux événements pour sensibiliser et agir ensemble</p>
        </div>
        @auth
            <a href="{{ route('events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Créer un événement
            </a>
        @endauth
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ $stats['total'] }}</h3>
                    <p class="mb-0">Total événements</p>
                </div>
                <i class="fas fa-calendar-check fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ $stats['upcoming'] }}</h3>
                    <p class="mb-0">À venir</p>
                </div>
                <i class="fas fa-clock fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ $stats['past'] }}</h3>
                    <p class="mb-0">Terminés</p>
                </div>
                <i class="fas fa-history fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Card -->
<div class="search-filter-card">
    <form method="GET" action="{{ route('events.index') }}" class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Rechercher</label>
            <input type="text" name="search" class="form-control" placeholder="Titre, lieu..." value="{{ request('search') }}">
        </div>
        
        <div class="col-md-2">
            <label class="form-label">Type</label>
            <select name="type" class="form-control">
                <option value="">Tous</option>
                <option value="plantation" {{ request('type') == 'plantation' ? 'selected' : '' }}>Plantation</option>
                <option value="conference" {{ request('type') == 'conference' ? 'selected' : '' }}>Conférence</option>
                <option value="atelier" {{ request('type') == 'atelier' ? 'selected' : '' }}>Atelier</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-control">
                <option value="">Tous</option>
                <option value="planifie" {{ request('statut') == 'planifie' ? 'selected' : '' }}>Planifié</option>
                <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Association</label>
            <select name="association_id" class="form-control">
                <option value="">Toutes</option>
                @foreach($associations as $association)
                    <option value="{{ $association->id }}" {{ request('association_id') == $association->id ? 'selected' : '' }}>
                        {{ $association->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search me-2"></i>Filtrer
            </button>
        </div>
    </form>

    <!-- View Mode Toggle -->
    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('events.index', array_merge(request()->all(), ['view' => 'list'])) }}" 
           class="btn {{ $viewMode === 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="fas fa-list me-1"></i>Liste
        </a>
        <a href="{{ route('events.index', array_merge(request()->all(), ['view' => 'calendar'])) }}" 
           class="btn {{ $viewMode === 'calendar' ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="fas fa-calendar me-1"></i>Calendrier
        </a>
    </div>
</div>

@if($viewMode === 'calendar')
    <!-- Calendar View -->
    <div class="card">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
@else
    <!-- List View -->
    @if($events->count() > 0)
        <div class="row">
            @foreach($events as $event)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        @if($event->image)
                            <img src="{{ asset('storage/' . $event->image) }}" class="card-img-top" alt="{{ $event->titre }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-calendar-alt fa-4x text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge badge-{{ $event->type_badge_color }}">
                                    {{ ucfirst($event->type) }}
                                </span>
                                <span class="badge badge-{{ $event->status_badge_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $event->statut)) }}
                                </span>
                            </div>
                            
                            <h5 class="card-title">{{ $event->titre }}</h5>
                            <p class="card-text text-muted small">
                                {{ Str::limit($event->description, 100) }}
                            </p>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $event->date_debut->format('d/m/Y à H:i') }}
                                </small>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $event->lieu }}
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $event->association->name }}
                                </small>
                            </div>

                            @if($event->capacite_max)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span>Places disponibles</span>
                                        <span class="fw-bold">{{ $event->available_places }} / {{ $event->capacite_max }}</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $event->capacite_max > 0 ? (($event->capacite_max - $event->available_places) / $event->capacite_max * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            @endif
                            
                            <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-eye me-1"></i>Voir les détails
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="custom-pagination">
            @if ($events->onFirstPage())
                <span class="page-link disabled">« Précédent</span>
            @else
                <a href="{{ $events->previousPageUrl() }}" class="page-link">« Précédent</a>
            @endif

            @foreach ($events->getUrlRange(1, $events->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $events->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if ($events->hasMorePages())
                <a href="{{ $events->nextPageUrl() }}" class="page-link">Suivant »</a>
            @else
                <span class="page-link disabled">Suivant »</span>
            @endif
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>Aucun événement trouvé</h3>
            <p>Aucun événement ne correspond à vos critères de recherche.</p>
            @auth
                <a href="{{ route('events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Créer le premier événement
                </a>
            @endauth
        </div>
    @endif
@endif
@endsection

@section('scripts')
@if($viewMode === 'calendar')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/locales/fr.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'fr',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine',
            list: 'Liste'
        },
        events: function(info, successCallback, failureCallback) {
            fetch('{{ route("events.calendar-data") }}')
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        },
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },
        eventDidMount: function(info) {
            info.el.style.cursor = 'pointer';
        },
        height: 'auto',
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        }
    });
    calendar.render();
});
</script>
@endif
@endsection