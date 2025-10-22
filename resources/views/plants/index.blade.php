@extends('layouts.app')

@section('title', 'Plantes - {{ $greenspace->name }} - UrbanGreen')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('greenspaces.index') }}">Espaces Verts</a></li>
            <li class="breadcrumb-item"><a href="{{ route('greenspaces.show', $greenspace) }}">{{ $greenspace->name }}</a></li>
            <li class="breadcrumb-item active">Plantes</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-seedling me-2"></i>Plantes de {{ $greenspace->name }}</h1>
        <a href="{{ route('greenspaces.plants.create', $greenspace) }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Ajouter une Plante
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($plants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Espèce</th>
                                    <th>Quantité</th>
                                    <th>Statut</th>
                                    <th>Date de plantation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plants as $plant)
                                    <tr>
                                        <td>
                                            <strong>{{ $plant->name }}</strong>
                                        </td>
                                        <td>{{ $plant->species ?? '-' }}</td>
                                        <td>{{ $plant->quantity ?? 1 }}</td>
                                        <td>
                                            @switch($plant->status)
                                                @case('en vie')
                                                    <span class="badge bg-success">En vie</span>
                                                    @break
                                                @case('malade')
                                                    <span class="badge bg-warning">Malade</span>
                                                    @break
                                                @case('abattu')
                                                    <span class="badge bg-danger">Abattu</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ $plant->planted_at ? $plant->planted_at->format('d/m/Y') : '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('greenspaces.plants.show', [$greenspace, $plant]) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('greenspaces.plants.edit', [$greenspace, $plant]) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('greenspaces.plants.destroy', [$greenspace, $plant]) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette plante ?')">
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
                        <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune plante enregistrée</h5>
                        <p class="text-muted">Commencez par ajouter une plante à cet espace vert.</p>
                        <a href="{{ route('greenspaces.plants.create', $greenspace) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Ajouter une Plante
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection