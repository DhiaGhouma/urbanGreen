@extends('layouts.app')

@section('title', 'Liste des Signalements')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><i class="fas fa-flag me-2"></i>Gestion des Signalements</h1>
            <p class="mb-0 text-muted">Gérez les signalements d'incidents dans les espaces verts</p>
        </div>
        <a href="{{ route('reports.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau Signalement
        </a>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                @if($reports->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Espace Vert</th>
                                    <th>Catégorie</th>
                                    <th>Priorité</th>
                                    <th>Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                <tr>
                                    <td>{{ Str::limit($report->title, 40) }}</td>
                                    <td>{{ $report->greenSpace->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas {{ $report->getCategoryIcon() }} me-1"></i>
                                            {{ $report->getCategoryLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $report->getPriorityBadgeClass() }}">
                                            {{ ucfirst($report->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $report->getStatusBadgeClass() }}">
                                            {{ $report->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @auth
                                            @if(Auth::user()->isAdmin() || $report->user_id === Auth::id())
                                                <a href="{{ route('reports.edit', $report) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('reports.destroy', $report) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce signalement ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <p class="text-muted">Aucun signalement trouvé.</p>
                        <a href="{{ route('reports.create') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-plus me-1"></i>Créer le premier signalement
                        </a>
                    </div>
                @endif

                {{-- Pagination --}}
                @if($reports->hasPages())
                    <div class="d-flex justify-content-center p-3 border-top">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
