@extends('layouts.app')

@section('title', 'Liste des Signalements')

@section('content')
<div class="page-header">
    <h1 class="display-4 fw-bold text-primary">
        <i class="fas fa-list me-3"></i>Liste des Signalements
    </h1>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Tous les signalements</h5>
                    <a href="{{ route('reports.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Ajouter un signalement
                    </a>
                </div>

                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#ID</th>
                            <th scope="col">Titre</th>
                            <th scope="col">Espace Vert</th>
                            <th scope="col">Catégorie</th>
                            <th scope="col">Priorité</th>
                            <th scope="col">Statut</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>{{ Str::limit($report->title, 30) }}</td>
                            <td>{{ $report->greenSpace->name ?? '—' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $report->category)) }}</td>
                            <td>
                                <span class="badge bg-{{ match($report->priority) {
                                    'basse' => 'secondary',
                                    'normale' => 'info',
                                    'haute' => 'warning',
                                    'urgente' => 'danger',
                                    default => 'secondary'
                                } }}">
                                    {{ ucfirst($report->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ match($report->status) {
                                    'en_attente' => 'secondary',
                                    'en_cours' => 'primary',
                                    'resolu' => 'success',
                                    'rejete' => 'danger',
                                    default => 'secondary'
                                } }}">
                                    {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('reports.edit', $report) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('reports.destroy', $report) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Aucun signalement trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
