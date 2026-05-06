@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-clipboard-check text-success"></i> 
                Mes Évaluations
            </h2>
            <small class="text-muted">
                Gérez les évaluations de vos stagiaires
            </small>
        </div>
        <div>
            <a href="{{ route('encadrant.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour au dashboard
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-primary mb-1">{{ $evaluations->count() }}</h5>
                    <small class="text-muted">Total évaluations</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-success mb-1">{{ $evaluations->where('statut', 'validee')->count() }}</h5>
                    <small class="text-muted">Évaluations validées</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-info mb-1">{{ $evaluations->where('statut', 'brouillon')->count() }}</h5>
                    <small class="text-muted">En préparation</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-warning mb-1">
                        @if($evaluations->where('statut', 'validee')->count() > 0)
                        {{ round($evaluations->where('statut', 'validee')->avg('note_generale'), 1) }}
                        @else
                        -
                        @endif
                    </h5>
                    <small class="text-muted">Note moyenne</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="fas fa-filter text-primary"></i> 
                Recherche et filtres
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="validee" {{ request('statut') == 'validee' ? 'selected' : '' }}>Validée</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="stagiaire_id" class="form-select form-select-sm">
                        <option value="">Tous les stagiaires</option>
                        @foreach($stagiaires as $stagiaire)
                        <option value="{{ $stagiaire->id }}" {{ request('stagiaire_id') == $stagiaire->id ? 'selected' : '' }}>
                            {{ $stagiaire->prenom }} {{ $stagiaire->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="activity_id" class="form-select form-select-sm">
                        <option value="">Toutes les activités</option>
                        @foreach($activities as $activity)
                        <option value="{{ $activity->id }}" {{ request('activity_id') == $activity->id ? 'selected' : '' }}>
                            {{ $activity->titre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Section pour créer des évaluations -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="fas fa-plus-circle text-primary"></i> 
                Actions d'évaluation
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Évaluer le stagiaire -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-graduate fa-3x text-primary"></i>
                            </div>
                            <h6 class="card-title">Évaluer le stagiaire</h6>
                            <p class="card-text small text-muted">
                                Évaluez les performances et compétences de vos stagiaires
                            </p>
                            <a href="{{ route('encadrant.evaluations.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-star me-2"></i>Évaluer un stagiaire
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Consulter les auto-évaluations -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-check fa-3x text-success"></i>
                            </div>
                            <h6 class="card-title">Auto-évaluations</h6>
                            <p class="card-text small text-muted">
                                Consultez les auto-évaluations des stagiaires
                            </p>
                            <a href="{{ route('encadrant.evaluations.auto.index') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-eye me-2"></i>Voir les auto-évaluations
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Liste des évaluations -->
    @if($evaluations->count() > 0)
    <div class="row">
        @foreach($evaluations as $evaluation)
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-{{ $evaluation->statut == 'validee' ? 'success' : 'secondary' }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ $evaluation->titre ?: 'Évaluation' }}</h6>
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
                        <a href="{{ route('evaluations.edit', $evaluation) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit"></i> Modifier
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
        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">Aucune évaluation</h5>
        <p class="text-muted">Vous n'avez pas encore créé d'évaluations.</p>
        <p class="text-muted small">Utilisez le bouton "Évaluer un stagiaire" ci-dessus pour commencer.</p>
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
