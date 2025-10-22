<div class="card mb-3">
    <div class="card-header">
        <h5>Informations</h5>
    </div>
    <div class="card-body">
        <p><strong>Titre :</strong> {{ $report->title }}</p>
        <p><strong>Statut :</strong> {{ ucfirst($report->statut) }}</p>
        <p><strong>Priorité :</strong> {{ ucfirst($report->priority) }}</p>
        <p><strong>Catégorie :</strong> {{ ucfirst($report->category) }}</p>
        <p><strong>Date :</strong> {{ $report->date_signalement->format('d/m/Y H:i') }}</p>
    </div>
</div>

@if(isset($associations) && $associations->count())
    <div class="card">
        <div class="card-header">
            <h5>Associations</h5>
        </div>
        <div class="card-body">
            <ul class="list-group">
                @foreach ($associations as $association)
                    <li class="list-group-item">{{ $association->name }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
