@extends('layouts.app')

@section('title', 'Nouvelle Participation - UrbanGreen')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('participations.index') }}">Participations</a></li>
            <li class="breadcrumb-item active">Nouvelle Participation</li>
        </ol>
    </nav>
    <h1><i class="fas fa-plus me-2"></i>Créer une Participation</h1>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('participations.store') }}">
                    @csrf
                    
                    <!-- Display current user info -->
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-circle me-3 fs-4"></i>
                            <div>
                                <strong>Participation pour:</strong><br>
                                <span class="text-muted">{{ Auth::user()->name }} ({{ Auth::user()->email }})</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="green_space_id" class="form-label">Espace Vert *</label>
                            <select name="green_space_id" 
                                    id="green_space_id" 
                                    class="form-select @error('green_space_id') is-invalid @enderror" 
                                    required>
                                <option value="">Sélectionner un espace vert</option>
                                @foreach($greenSpaces as $greenSpace)
                                    <option value="{{ $greenSpace->id }}" {{ old('green_space_id') == $greenSpace->id ? 'selected' : '' }}>
                                        {{ $greenSpace->name }} - {{ $greenSpace->location }}
                                    </option>
                                @endforeach
                            </select>
                            @error('green_space_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="mt-2">
                                <button type="button" id="btn-ai-suggest" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-wand-magic-sparkles me-1"></i> Suggérer avec l'IA
                                </button>
                                <span id="ai-suggest-status" class="ms-2 text-muted" style="display:none"></span>
                                <div id="ai-suggest-reason" class="form-text mt-1" style="display:none"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="date" class="form-label">Date de Participation *</label>
                            <input type="date" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   id="date" 
                                   name="date" 
                                   value="{{ old('date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="statut" class="form-label">Statut *</label>
                            <select name="statut" 
                                    id="statut" 
                                    class="form-select @error('statut') is-invalid @enderror" 
                                    required>
                                <option value="en_attente" {{ old('statut', 'en_attente') == 'en_attente' ? 'selected' : '' }}>
                                    En Attente
                                </option>
                                <option value="confirmee" {{ old('statut') == 'confirmee' ? 'selected' : '' }}>
                                    Confirmée
                                </option>
                                <option value="annulee" {{ old('statut') == 'annulee' ? 'selected' : '' }}>
                                    Annulée
                                </option>
                                <option value="terminee" {{ old('statut') == 'terminee' ? 'selected' : '' }}>
                                    Terminée
                                </option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Créer la Participation
                        </button>
                        <a href="{{ route('participations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btn-ai-suggest');
    const status = document.getElementById('ai-suggest-status');
    const select = document.getElementById('green_space_id');
    const reason = document.getElementById('ai-suggest-reason');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        status.style.display = 'inline';
        status.textContent = '🤖 Analyse IA en cours... (peut prendre 1-3 minutes)';
        status.classList.remove('text-danger', 'text-success', 'text-warning');
        status.classList.add('text-info');
        btn.disabled = true;
        reason.style.display = 'none';

        const startTime = Date.now();

        try {
            const res = await fetch("{{ route('participations.suggest') }}", {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            const elapsed = ((Date.now() - startTime) / 1000).toFixed(1);
            
            console.log('AI suggestion result:', data);
            if (!res.ok) throw new Error(data.error || 'Erreur lors de la suggestion');
            
            if (data.best_match_id) {
                select.value = String(data.best_match_id);
                const label = [...select.options].find(o => o.value === String(data.best_match_id))?.text || '#'+data.best_match_id;
                
                status.textContent = `✅ Suggestion: ${label}`;
                status.classList.remove('text-danger', 'text-info');
                status.classList.add('text-success');
                
                if (data.reason) {
                    const engineInfo = data.engine ? ` [Engine: ${data.engine}]` : '';
                    const timeInfo = data.computation_time ? ` | Temps: ${data.computation_time}` : ` | Temps: ${elapsed}s`;
                    reason.innerHTML = `<strong>Raison (Ollama):</strong> ${data.reason}${engineInfo}${timeInfo}`;
                    reason.style.display = 'block';
                }
            } else {
                status.textContent = '⚠️ Aucune suggestion disponible';
                status.classList.add('text-warning');
                reason.style.display = 'none';
            }
        } catch (e) {
            status.textContent = '❌ ' + (e.message || 'Erreur IA');
            status.classList.remove('text-info');
            status.classList.add('text-danger');
            reason.style.display = 'none';
        } finally {
            btn.disabled = false;
            // Keep status visible longer so user can read it
            setTimeout(() => { 
                status.style.display = 'none'; 
                reason.style.display = 'none';
            }, 15000); // 15 seconds to read the reasoning
        }
    });
});
</script>
@endsection