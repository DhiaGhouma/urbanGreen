@extends('layouts.app')

@section('title', 'Projets - UrbanGreen')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><i class="fas fa-project-diagram me-2"></i>Gestion des Projets</h1>
            <p class="mb-0 text-muted">Gérez les projets de végétalisation urbaine</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau Projet
        </a>
    </div>
</div>

<!-- Filtres et recherche -->
<div class="search-filter-card">
    <form method="GET" action="{{ route('projects.index') }}">
        <div class="row g-3">
            <div class="col-md-5">
                <select name="association_id" class="form-select">
                    <option value="">Toutes les associations</option>
                    @foreach($associations as $association)
                        <option value="{{ $association->id }}" 
                                {{ request('association_id') == $association->id ? 'selected' : '' }}>
                            {{ $association->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <select name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search me-1"></i>Filtrer
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Liste des projets -->
<div class="row">
    @forelse($projects as $project)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0">{{ $project->title }}</h5>
                        <span class="badge project-status-badge {{ $project->getStatusBadgeClass() }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>
                    
                    <p class="card-text text-muted">{{ Str::limit($project->description, 100) }}</p>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">
                            <i class="fas fa-users me-1"></i>{{ $project->association->name }}
                        </small>
                        <small class="text-muted d-block">
                            <i class="fas fa-map-marker-alt me-1"></i>{{ $project->greenSpace->name ?? 'Espace vert non défini' }}
                        </small>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-success">{{ number_format($project->estimated_budget, 2) }} €</span>
                        </div>
                        <div class="action-buttons">
                            <a href="{{ route('projects.show', $project) }}" 
                               class="btn btn-sm btn-outline-primary" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('projects.edit', $project) }}" 
                               class="btn btn-sm btn-outline-warning" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('projects.destroy', $project) }}" 
                                  class="d-inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                    <h5>Aucun projet trouvé</h5>
                    <p class="text-muted">Commencez par créer votre premier projet de végétalisation.</p>
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Créer un projet
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($projects->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $projects->links() }}
    </div>
@endif
@endsection