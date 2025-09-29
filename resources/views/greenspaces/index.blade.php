@extends('layouts.app')

@section('title', 'Espaces Verts - UrbanGreen')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><i class="fas fa-tree me-2"></i>Gestion des Espaces Verts</h1>
            <p class="mb-0 text-muted">Gérez les espaces verts d'UrbanGreen</p>
        </div>
        <a href="{{ route('greenspaces.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvel Espace Vert
        </a>
    </div>
</div>

<!-- Recherche et filtres -->
<div class="search-filter-card my-3">
    <form method="GET" action="{{ route('greenspaces.index') }}">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Rechercher un espace vert..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="type" class="form-select">
                    <option value="">Tous les types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-search me-1"></i>Filtrer</button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($greenSpaces->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Localisation</th>
                            <th>Type</th>
                            <th>Surface</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($greenSpaces as $greenSpace)
                            <tr>
                                <td>{{ $greenSpace->name }}</td>
                                <td>{{ $greenSpace->location }}</td>
                                <td>{{ $greenSpace->type }}</td>
                                <td>{{ $greenSpace->getFormattedSurface() }}</td>
                                <td><span class="badge {{ $greenSpace->getStatusBadgeClass() }}">{{ ucfirst($greenSpace->status) }}</span></td>
                                    <td class="text-center">
                                    <a href="{{ route('greenspaces.show', $greenSpace) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
+                                    <a href="{{ route('greenspaces.plants.index', $greenSpace) }}" class="btn btn-sm btn-outline-success" title="Plantes">
+                                        <i class="fas fa-seedling"></i>
+                                    </a>
                                    <a href="{{ route('greenspaces.edit', $greenSpace) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('greenspaces.destroy', $greenSpace) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet espace vert ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <p class="text-muted">Aucun espace vert trouvé.</p>
                <a href="{{ route('greenspaces.create') }}" class="btn btn-primary">Créer un espace vert</a>
            </div>
        @endif
    </div>
</div>

@if($greenSpaces instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="d-flex justify-content-center mt-4">{{ $greenSpaces->links() }}</div>
@endif
@endsection
