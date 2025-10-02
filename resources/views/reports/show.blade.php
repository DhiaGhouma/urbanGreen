@extends('layouts.app')

@section('title', 'Détails du Signalement')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Signalements</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($report->title, 30) }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1>
                <i class="fas {{ $report->getCategoryIcon() }} me-2"></i>{{ $report->title }}
            </h1>
            <div class="mt-2">
                <span class="badge {{ $report->getStatusBadgeClass() }} me-2">
                    {{ $report->getStatusLabel() }}
                </span>
                <span class="badge {{ $report->getPriorityBadgeClass() }}">
                    {{ $report->getPriorityLabel() }}
                </span>
            </div>
        </div>
        <div>
            @auth
                @if(Auth::user()->isAdmin() || $report->user_id === Auth::id())
                    <a href="{{ route('reports.edit', $report) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-1"></i>Modifier
                    </a>
                @endif
            @endauth
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>
</div>

<div class="row mt-4">
    {{-- Colonne principale --}}
    <div class="col-lg-8">
        {{-- Informations principales --}}
        <div class="card mb-3">
            <div class="card-body">
                @if($report->photo)
                    <img src="{{ Storage::url($report->photo) }}" 
                         class="img-fluid rounded mb-3" 
                         alt="{{ $report->title }}"
                         style="width: 100%; max-height: 400px; object-fit: cover;">
                @endif

                <h5 class="mb-2"><i class="fas fa-info-circle me-2"></i>Description</h5>
                <p class="text-muted" style="white-space: pre-line;">{{ $report->description }}</p>

                @if($report->latitude && $report->longitude)
                    <div class="alert alert-info mt-3">
                        <h6 class="alert-heading mb-2">
                            <i class="fas fa-map-marker-alt me-1"></i>Localisation
                        </h6>
                        <p class="mb-2">
                            <strong>GPS:</strong> {{ $report->latitude }}, {{ $report->longitude }}
                        </p>
                        <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-map me-1"></i>Voir sur Google Maps
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Mises à jour / Suivi --}}
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historique des mises à jour</h5>
            </div>
            <div class="card-body">
                @if($report->updates->count() > 0)
                    <div class="timeline">
                        @foreach($report->updates as $update)
                            <div class="timeline-item mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-start">
                                    <div class="timeline-marker {{ $update->getStatusBadgeClass() }} me-3 mt-1"></div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-0">
                                                    <i class="fas fa-user-circle me-1"></i>{{ $update->user->name }}
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>{{ $update->created_at->format('d/m/Y à H:i') }}
                                                </small>
                                            </div>
                                            <span class="badge {{ $update->getStatusBadgeClass() }}">
                                                {{ $update->getStatusLabel() }}
                                            </span>
                                        </div>
                                        <p class="mb-2" style="white-space: pre-line;">{{ $update->comment }}</p>
                                        @if($update->photo)
                                            <img src="{{ Storage::url($update->photo) }}" 
                                                 class="img-fluid rounded" 
                                                 alt="Photo mise à jour"
                                                 style="max-height: 180px;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-2 opacity-50"></i>
                        <p class="mb-0">Aucune mise à jour pour le moment</p>
                    </div>
                @endif

                {{-- Formulaire d'ajout de mise à jour --}}
                @auth
                    @if(Auth::user()->isAdmin() || $report->assignedAssociation)
                        <div class="card bg-light mt-3">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-plus-circle me-1"></i>Ajouter une mise à jour
                                </h6>
                                <form action="{{ route('reports.update.add', $report) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Nouveau statut</label>
                                        <select name="status" id="status" class="form-select" required>
                                            <option value="en_attente" {{ $report->status == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                            <option value="en_cours" {{ $report->status == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                            <option value="resolu" {{ $report->status == 'resolu' ? 'selected' : '' }}>Résolu</option>
                                            <option value="rejete" {{ $report->status == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="comment" class="form-label">Commentaire</label>
                                        <textarea name="comment" id="comment" rows="3" class="form-control" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="update_photo" class="form-label">Photo (optionnel)</label>
                                        <input type="file" name="photo" id="update_photo" class="form-control" accept="image/*">
                                        <img id="updatePhotoPreview" style="max-height: 180px; display:none;" class="img-fluid rounded mt-2">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-1"></i>Publier la mise à jour
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        @include('reports.partials.sidebar', ['report' => $report, 'associations' => $associations ?? collect()])
    </div>
</div>

<style>
.timeline {
    position: relative;
    margin-left: 20px;
    padding-left: 20px;
    border-left: 2px solid #dee2e6;
}
.timeline-item {
    position: relative;
    padding-left: 20px;
}
.timeline-marker {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #6c757d;
}
</style>
@endsection

@section('scripts')
<script>
document.getElementById('update_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('updatePhotoPreview');
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = "block";
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
