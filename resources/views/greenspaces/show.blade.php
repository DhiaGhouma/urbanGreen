@extends('layouts.app')

@section('title', $greenspace->name . ' - UrbanGreen')

@section('content')
@php
    $feedbacks = $greenspace->participations->pluck('feedback')->filter();
    $averageRating = $feedbacks->avg('rating');
@endphp
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('greenspaces.index') }}">Espaces Verts</a></li>
            <li class="breadcrumb-item active">{{ $greenspace->name }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1>{{ $greenspace->name }}</h1>
            <p>{{ $greenspace->type }}</p>
        </div>
        <div>
            <a href="{{ route('greenspaces.edit', $greenspace) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
            <a href="{{ route('projects.create') }}?greenspace_id={{ $greenspace->id }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i>Ajouter un Projet
            </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Informations</div>
            <div class="card-body">
                <p><strong>Localisation:</strong> {{ $greenspace->location }}</p>
                <p><strong>Surface:</strong> {{ $greenspace->getFormattedSurface() }}</p>
                <p><strong>Statut:</strong> <span class="badge {{ $greenspace->getStatusBadgeClass() }}">{{ ucfirst($greenspace->status) }}</span></p>
                <p><strong>Coordonnées GPS:</strong> Lat: {{ $greenspace->latitude ?? '-' }}, Lng: {{ $greenspace->longitude ?? '-' }}</p>
                @if($greenspace->description)
                    <p><strong>Description:</strong> {{ $greenspace->description }}</p>
                @endif
            </div>
        </div>

        <!-- Environmental Widget -->
        @if($greenspace->latitude && $greenspace->longitude)
            <div class="mt-4">
                @include('components.environmental-widget', ['greenSpaceId' => $greenspace->id])
            </div>
        @endif

        <div class="card mt-4">
            <div class="card-header">Avis des participants</div>
            <div class="card-body">
                @if($feedbacks->isNotEmpty())
                    <div class="d-flex align-items-center mb-3">
                        <div class="display-6 fw-bold me-3">{{ number_format($averageRating, 1) }}</div>
                        <div>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= round($averageRating) ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                            <small class="text-muted">Basé sur {{ $feedbacks->count() }} avis</small>
                        </div>
                    </div>
                    <p class="mb-0 text-muted">"{{ \Illuminate\Support\Str::limit($feedbacks->sortByDesc('created_at')->first()->comment, 120) }}"</p>
                @else
                    <p class="text-muted mb-0">Aucun avis pour l'instant. Encouragez vos bénévoles à partager leur expérience !</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">Projets ({{ $greenspace->projects->count() }})</div>
            <div class="card-body p-0">
                @if($greenspace->projects->count() > 0)
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Projet</th>
                                <th>Budget</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($greenspace->projects as $project)
                                <tr>
                                    <td>{{ $project->title }}</td>
                                    <td>{{ number_format($project->estimated_budget, 2) }} €</td>
                                    <td><span class="badge {{ $project->getStatusBadgeClass() }}">{{ ucfirst($project->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-center py-3">Aucun projet.</p>
                @endif
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Retours des volontaires</span>
                @if($feedbacks->isNotEmpty())
                    <span class="badge bg-light text-dark">{{ $feedbacks->count() }} avis</span>
                @endif
            </div>
            <div class="card-body">
                @if($feedbacks->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-comments fa-2x mb-3"></i>
                        <p class="mb-0">Pas encore d'avis sur cet espace vert.</p>
                    </div>
                @else
                    @foreach($feedbacks->sortByDesc('created_at') as $feedback)
                        <div class="mb-4 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ optional($feedback->user)->name ?? 'Participant' }}</h6>
                                    <small class="text-muted">{{ optional($feedback->created_at)->format('d/m/Y') }}</small>
                                </div>
                                <div>
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="mt-3 mb-0" style="white-space: pre-line;">{{ $feedback->comment }}</p>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection