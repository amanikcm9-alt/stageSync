@extends('layouts.app')

@section('title', 'Auto-évaluations des stagiaires')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-user-check text-success"></i> 
                Auto-évaluations des stagiaires
            </h2>
            <small class="text-muted">
                Consultez les auto-évaluations de vos stagiaires
            </small>
        </div>
        <div>
            <a href="{{ route('encadrant.evaluations.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour aux évaluations
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-primary mb-1">{{ $autoEvaluations->count() }}</h5>
                    <small class="text-muted">Total auto-évaluations</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-success mb-1">{{ $stagiaires->count() }}</h5>
                    <small class="text-muted">Stagiaires évalués</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-info mb-1">
                        @if($autoEvaluations->count() > 0)
                        {{ round($autoEvaluations->avg('note_generale'), 1) }}
                        @else
                        -
                        @endif
                    </h5>
                    <small class="text-muted">Note moyenne</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-warning mb-1">{{ $autoEvaluations->where('statut', 'validee')->count() }}</h5>
                    <small class="text-muted">Validées</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="fas fa-filter text-primary"></i> 
                Filtres
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="stagiaire_id" class="form-select form-select-sm">
                        <option value="">Tous les stagiaires</option>
                        @foreach($stagiaires as $stagiaire)
                        <option value="{{ $stagiaire->id }}" {{ request('stagiaire_id') == $stagiaire->id ? 'selected' : '' }}>
                            {{ $stagiaire->prenom }} {{ $stagiaire->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="validee" {{ request('statut') == 'validee' ? 'selected' : '' }}>Validée</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des auto-évaluations -->
    @if($autoEvaluations->count() > 0)
    <div class="row">
        @foreach($autoEvaluations as $evaluation)
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-{{ $evaluation->statut == 'validee' ? 'success' : 'secondary' }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Auto-évaluation</h6>
                        <span class="badge bg-light text-dark">{{ $evaluation->statut_label }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($evaluation->stagiaire)
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-user"></i> 
                            Stagiaire: {{ $evaluation->stagiaire->prenom }} {{ $evaluation->stagiaire->nom }}
                        </small>
                    </div>
                    @endif
                    
                    @if($evaluation->activity)
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-tasks"></i> 
                            Activité: {{ $evaluation->activity->titre }}
                        </small>
                    </div>
                    @endif

                    @if($evaluation->statut == 'validee')
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong>Note générale:</strong></span>
                            <span class="badge bg-primary">{{ $evaluation->note_generale }}/20</span>
                        </div>
                        @if($evaluation->note_generale)
                        <div class="mt-1">
                            {!! $evaluation->note_etoiles !!}
                        </div>
                        @endif
                    </div>

                    @if($evaluation->note_technique)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong>Note technique:</strong></span>
                            <span>{{ $evaluation->note_technique }}/20</span>
                        </div>
                    </div>
                    @endif

                    @if($evaluation->note_comportement)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong>Note comportement:</strong></span>
                            <span>{{ $evaluation->note_comportement }}/20</span>
                        </div>
                    </div>
                    @endif
                    @endif

                    @if($evaluation->commentaires)
                    <div class="mb-2">
                        <small class="text-muted">
                            <strong>Commentaires:</strong><br>
                            {{ Str::limit($evaluation->commentaires, 150) }}
                        </small>
                    </div>
                    @endif

                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> 
                            {{ $evaluation->created_at->format('d/m/Y H:i') }}
                            @if($evaluation->updated_at != $evaluation->created_at)
                            <br>Modifiée: {{ $evaluation->updated_at->format('d/m/Y H:i') }}
                            @endif
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="btn-group btn-group-sm w-100">
                        <a href="{{ route('evaluations.show', $evaluation) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        @if($evaluation->statut == 'validee')
                        <button class="btn btn-outline-success" onclick="telechargerPDF({{ $evaluation->id }})">
                            <i class="fas fa-download"></i> PDF
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-5">
        <i class="fas fa-user-check fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">Aucune auto-évaluation</h5>
        <p class="text-muted">Vos stagiaires n'ont pas encore réalisé d'auto-évaluations.</p>
        <p class="text-muted small">Les auto-évaluations apparaîtront ici dès que les stagiaires les auront complétées.</p>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function telechargerPDF(evaluationId) {
    window.open(`/evaluations/${evaluationId}/pdf`, '_blank');
}
</script>
@endsection
