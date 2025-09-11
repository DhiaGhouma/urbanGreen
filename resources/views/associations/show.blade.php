@extends('layouts.app')

@section('title', $association->name . ' - UrbanGreen')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('associations.index') }}">Associations</a></li>
            <li class="breadcrumb-item active">{{ $association->name }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1><i class="fas fa-users me-2"></i>{{ $association->name }}</h1>
            <p class="text-muted">{{ $association->domain }}</p>
        </div>
        <div>
            <a href="{{ route('associations.edit', $association) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            <a href="{{ route('projects.create') }}?association_id={{ $association->id }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Ajouter un Projet
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informations de l'association -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="fw-bold text-muted">Email</label>
                    <div><i class="fas fa-envelope me-2"></i>{{ $association->email }}</div>
                </div>
                <div class="mb-3">
                    <label class="fw-bold text-muted">Téléphone</label>
                    <div><i class="fas fa-phone me-2"></i>{{ $association->phone }}</div>
                </div>
                <div class="mb-3">
                    <label class="fw-bold text-muted">Domaine d'action</label>
                    <div><span class="badge bg-light text-dark">{{ $association->domain }}</span></div>
                </div>
                <div class="mb-0">
                    <label class="fw-bold text-muted">Membre depuis</label>
                    <div><i class="fas fa-calendar me-2"></i>{{ $association->created_at->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projets de l'association -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-project-diagram me-2"></i>Projets ({{ $association->projects->count() }})</h5>
                <a href="{{ route('projects.create') }}?association_id={{ $association->id }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Nouveau Projet
                </a>
            </div>
            <div class="card-body p-0">
                @if($association->projects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Projet</th>
                                    <th>Budget</th>
                                    <th>Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($association->projects as $project)
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-1">{{ $project->title }}</h6>
                                                <small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ number_format($project->estimated_budget, 2) }} €</span>
                                        </td>
                                        <td>
                                            <span class="badge project-status-badge {{ $project->getStatusBadgeClass() }}">
                                                {{ ucfirst($project->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('projects.show', $project) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                        <h6>Aucun projet</h6>
                        <p class="text-muted">Cette association n'a pas encore de projets.</p>
                        <a href="{{ route('projects.create') }}?association_id={{ $association->id }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Créer le premier projet
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection