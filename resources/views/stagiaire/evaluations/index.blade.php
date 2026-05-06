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
                Consultez vos évaluations et feedbacks
            </small>
        </div>
        <div>
            <a href="{{ route('stagiaire.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour au dashboard
            </a>
        </div>
    </div>

    <!-- Statistiques des évaluations -->
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
                    <h5 class="text-info mb-1">
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
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-warning mb-1">{{ $evaluations->where('statut', 'brouillon')->count() }}</h5>
                    <small class="text-muted">En préparation</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Section pour créer des évaluations -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="fas fa-plus-circle text-primary"></i> 
                Nouvelles Évaluations
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Évaluer l'organisation -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-building fa-3x text-primary"></i>
                            </div>
                            <h6 class="card-title">Évaluer l'organisation</h6>
                            <p class="card-text small text-muted">
                                Évaluez l'organisation de votre stage et l'entreprise d'accueil
                            </p>
                            <a href="{{ route('stagiaire.evaluations.organisation.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-2"></i>Commencer l'évaluation
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Évaluer l'encadrant -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-tie fa-3x text-success"></i>
                            </div>
                            <h6 class="card-title">Évaluer l'encadrant</h6>
                            <p class="card-text small text-muted">
                                Évaluez l'encadrement et le suivi de votre encadrant
                            </p>
                            <a href="{{ route('stagiaire.evaluations.encadrant.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-star me-2"></i>Évaluer mon encadrant
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Réaliser une auto-évaluation -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-check fa-3x text-info"></i>
                            </div>
                            <h6 class="card-title">Auto-évaluation</h6>
                            <p class="card-text small text-muted">
                                Évaluez votre propre travail et vos compétences
                            </p>
                            <a href="{{ route('stagiaire.evaluations.auto.create') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-clipboard me-2"></i>M'auto-évaluer
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
                <div class="card-header bg-{{ $evaluation->statut == 'validee' ? 'success' : ($evaluation->statut == 'brouillon' ? 'secondary' : 'info') }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ $evaluation->titre ?: 'Évaluation' }}</h6>
                        <span class="badge bg-light text-dark">{{ $evaluation->statut_label }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($evaluation->activity)
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-tasks"></i> 
                            Activité: {{ $evaluation->activity->titre }}
                        </small>
                    </div>
                    @endif
                    
                    @if($evaluation->evaluateur)
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-user"></i> 
                            Évaluateur: {{ $evaluation->evaluateur->prenom }} {{ $evaluation->evaluateur->nom }}
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
                            <i class="fas fa-eye"></i> Voir détails
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
        <p class="text-muted">Vous n'avez pas encore reçu d'évaluations.</p>
        <p class="text-muted small">Vos encadrants évalueront vos activités au fur et à mesure de leur réalisation.</p>
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
