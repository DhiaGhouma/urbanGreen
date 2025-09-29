@extends('layouts.app')

@section('title', '{{ $plant->name }} - UrbanGreen')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('greenspaces.index') }}">Espaces Verts</a></li>
            <li class="breadcrumb-item"><a href="{{ route('greenspaces.show', $greenspace) }}">{{ $greenspace->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('greenspaces.plants.index', $greenspace) }}">Plantes</a></li>
            <li class="breadcrumb-item active">{{ $plant->name }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-seedling me-2"></i>{{ $plant->name }}</h1>
        <div class="btn-group">
            <a href="{{ route('greenspaces.plants.edit', [$greenspace, $plant]) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            <form action="{{ route('greenspaces.plants.destroy', [$greenspace, $plant]) }}" 
                  method="POST" 
                  class="d-inline"
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette plante ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-trash me-2"></i>Supprimer
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Informations générales</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nom</label>
                        <p class="form-control-plaintext">{{ $plant->name }}</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Espèce</label>
                        <p class="form-control-plaintext">{{ $plant->species ?? 'Non spécifiée' }}</p>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Quantité</label>
                        <p class="form-control-plaintext">{{ $plant->quantity ?? 1 }}</p>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Date de plantation</label>
                        <p class="form-control-plaintext">
                            {{ $plant->planted_at ? $plant->planted_at->format('d/m/Y') : 'Non spécifiée' }}
                        </p>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Statut</label>
                        <p class="form-control-plaintext">
                            @switch($plant->status)
                                @case('en vie')
                                    <span class="badge bg-success fs-6">En vie</span>
                                    @break
                                @case('malade')
                                    <span class="badge bg-warning fs-6">Malade</span>
                                    @break
                                @case('abattu')
                                    <span class="badge bg-danger fs-6">Abattu</span>
                                    @break
                            @endswitch
                        </p>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Maintenance</label>
                        <p class="form-control-plaintext">{{ $plant->maintenance ?? 'Aucune maintenance spécifiée' }}</p>
                    </div>

                    @if($plant->notes)
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Notes</label>
                            <p class="form-control-plaintext">{{ $plant->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Espace vert associé</h5>
                
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-tree fa-2x text-success me-3"></i>
                    <div>
                        <h6 class="mb-1">{{ $greenspace->name }}</h6>
                        <small class="text-muted">{{ $greenspace->location }}</small>
                    </div>
                </div>

                <a href="{{ route('greenspaces.show', $greenspace) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye me-1"></i>Voir l'espace vert
                </a>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title mb-3">Informations système</h5>
                
                <small class="text-muted">
                    <strong>Créé le :</strong> {{ $plant->created_at->format('d/m/Y à H:i') }}<br>
                    @if($plant->updated_at != $plant->created_at)
                        <strong>Modifié le :</strong> {{ $plant->updated_at->format('d/m/Y à H:i') }}
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-start mt-4">
    <a href="{{ route('greenspaces.plants.index', $greenspace) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
</div>
@endsection