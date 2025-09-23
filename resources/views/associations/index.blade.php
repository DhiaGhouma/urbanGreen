@extends('layouts.app')

@section('title', 'Associations - UrbanGreen')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><i class="fas fa-users me-2"></i>Gestion des Associations</h1>
            <p class="mb-0 text-muted">Gérez les associations partenaires d'UrbanGreen</p>
        </div>
        <a href="{{ route('associations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Association
        </a>
    </div>
</div>

<!-- Filtres et recherche -->
<div class="search-filter-card">
    <form method="GET" action="{{ route('associations.index') }}">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Rechercher une association..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="domain" class="form-select">
                    <option value="">Tous les domaines</option>
                    @foreach($domains as $domain)
                        <option value="{{ $domain }}" {{ request('domain') == $domain ? 'selected' : '' }}>
                            {{ $domain }}
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

<!-- Liste des associations -->
<div class="card">
    <div class="card-body p-0">
        @if($associations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Association</th>
                            <th>Contact</th>
                            <th>Domaine</th>
                            <th>Projets</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($associations as $association)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($association->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $association->name }}</h6>
                                            <small class="text-muted">Créée le {{ $association->created_at->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div><i class="fas fa-envelope me-1 text-muted"></i>{{ $association->email }}</div>
                                        <div><i class="fas fa-phone me-1 text-muted"></i>{{ $association->phone }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $association->domain }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $association->projects->count() }} projets</span>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="{{ route('associations.show', $association) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('associations.edit', $association) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('associations.destroy', $association) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette association ?')">
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
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5>Aucune association trouvée</h5>
                <p class="text-muted">Commencez par créer votre première association.</p>
                <a href="{{ route('associations.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Créer une association
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($associations->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $associations->links() }}
    </div>
@endif
@endsection