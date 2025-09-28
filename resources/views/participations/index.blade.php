@extends('layouts.app')

@section('title', 'Participations - UrbanGreen')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><i class="fas fa-hand-holding-heart me-2"></i>Gestion des Participations</h1>
            <p class="mb-0 text-muted">Gérez les participations citoyennes aux espaces verts</p>
        </div>
        <a href="{{ route('participations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Participation
        </a>
    </div>
</div>

<!-- Filtres et recherche -->
<div class="search-filter-card">
    <form method="GET" action="{{ route('participations.index') }}">
        <div class="row g-3">
            <div class="col-md-4">
                <select name="statut" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En Attente</option>
                    <option value="confirmee" {{ request('statut') == 'confirmee' ? 'selected' : '' }}>Confirmée</option>
                    <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                    <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="date" 
                       class="form-control" 
                       name="date" 
                       placeholder="Date de participation"
                       value="{{ request('date') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search me-1"></i>Filtrer
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Liste des participations -->
<div class="card">
    <div class="card-body p-0">
        @if($participations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Participant</th>
                            <th>Espace Vert</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($participations as $participation)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($participation->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $participation->user->name }}</h6>
                                            <small class="text-muted">{{ $participation->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $participation->greenSpace->name }}</h6>
                                        <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $participation->greenSpace->location }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div>{{ $participation->date->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $participation->date->diffForHumans() }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge status-badge-{{ $participation->statut }}">
                                        {{ $participation->getStatutLabel() }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="{{ route('participations.show', $participation) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('participations.edit', $participation) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('participations.destroy', $participation) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette participation?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-hand-holding-heart"></i>
                <h3>Aucune participation trouvée</h3>
                <p>Commencez par créer votre première participation citoyenne.</p>
                <a href="{{ route('participations.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nouvelle Participation
                </a>
            </div>
        @endif
    </div>
</div>

@if($participations->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $participations->links() }}
    </div>
@endif
@endsection