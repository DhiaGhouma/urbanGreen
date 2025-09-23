@extends('layouts.app')

@section('title', $greenspace->name . ' - UrbanGreen')

@section('content')
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
            <a href="{{ route('greenspaces.edit', $greenspace) }}" class="btn btn-warning me-2">Modifier</a>
            <a href="{{ route('projects.create') }}?greenspace_id={{ $greenspace->id }}" class="btn btn-success">Ajouter un Projet</a>
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
    </div>
</div>
@endsection
