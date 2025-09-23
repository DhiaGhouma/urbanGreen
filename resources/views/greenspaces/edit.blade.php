@extends('layouts.app')

@section('title', 'Modifier ' . $greenspace->name . ' - UrbanGreen')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('greenspaces.index') }}">Espaces Verts</a></li>
            <li class="breadcrumb-item"><a href="{{ route('greenspaces.show', $greenspace) }}">{{ $greenspace->name }}</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol>
    </nav>
    <h1><i class="fas fa-edit me-2"></i>Modifier {{ $greenspace->name }}</h1>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('greenspaces.update', $greenspace) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nom de l'espace vert *</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $greenspace->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="location" class="form-label">Localisation *</label>
                            <input type="text"
                                   class="form-control @error('location') is-invalid @enderror"
                                   id="location"
                                   name="location"
                                   value="{{ old('location', $greenspace->location) }}"
                                   required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3">{{ old('description', $greenspace->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="type" class="form-label">Type *</label>
                            <select class="form-select @error('type') is-invalid @enderror"
                                    id="type"
                                    name="type"
                                    required>
                                <option value="">Sélectionnez un type</option>
                                <option value="Parc" {{ old('type', $greenspace->type) == 'Parc' ? 'selected' : '' }}>Parc</option>
                                <option value="Jardin" {{ old('type', $greenspace->type) == 'Jardin' ? 'selected' : '' }}>Jardin</option>
                                <option value="Terrain vague" {{ old('type', $greenspace->type) == 'Terrain vague' ? 'selected' : '' }}>Terrain vague</option>
                                <option value="Aire de jeux" {{ old('type', $greenspace->type) == 'Aire de jeux' ? 'selected' : '' }}>Aire de jeux</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="surface" class="form-label">Surface (m²)</label>
                            <input type="number"
                                   step="0.01"
                                   class="form-control @error('surface') is-invalid @enderror"
                                   id="surface"
                                   name="surface"
                                   value="{{ old('surface', $greenspace->surface) }}">
                            @error('surface')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="status" class="form-label">Statut *</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status"
                                    name="status"
                                    required>
                                <option value="">Sélectionnez un statut</option>
                                <option value="proposé" {{ old('status', $greenspace->status) == 'proposé' ? 'selected' : '' }}>Proposé</option>
                                <option value="en cours" {{ old('status', $greenspace->status) == 'en cours' ? 'selected' : '' }}>En cours</option>
                                <option value="terminé" {{ old('status', $greenspace->status) == 'terminé' ? 'selected' : '' }}>Terminé</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="number"
                                   step="any"
                                   class="form-control @error('latitude') is-invalid @enderror"
                                   id="latitude"
                                   name="latitude"
                                   value="{{ old('latitude', $greenspace->latitude) }}">
                            @error('latitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="number"
                                   step="any"
                                   class="form-control @error('longitude') is-invalid @enderror"
                                   id="longitude"
                                   name="longitude"
                                   value="{{ old('longitude', $greenspace->longitude) }}">
                            @error('longitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="photos_before" class="form-label">Photos avant</label>
                            <input type="file"
                                   class="form-control @error('photos_before.*') is-invalid @enderror"
                                   id="photos_before"
                                   name="photos_before[]"
                                   multiple>
                            @if($greenspace->photos_before)
                                <div class="mt-2">
                                    @foreach($greenspace->photos_before as $photo)
                                        <img src="{{ asset('storage/'.$photo) }}" alt="Photo avant" class="img-thumbnail me-1" style="height:80px;">
                                    @endforeach
                                </div>
                            @endif
                            @error('photos_before.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="photos_after" class="form-label">Photos après</label>
                            <input type="file"
                                   class="form-control @error('photos_after.*') is-invalid @enderror"
                                   id="photos_after"
                                   name="photos_after[]"
                                   multiple>
                            @if($greenspace->photos_after)
                                <div class="mt-2">
                                    @foreach($greenspace->photos_after as $photo)
                                        <img src="{{ asset('storage/'.$photo) }}" alt="Photo après" class="img-thumbnail me-1" style="height:80px;">
                                    @endforeach
                                </div>
                            @endif
                            @error('photos_after.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('greenspaces.show', $greenspace) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Mettre à Jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
