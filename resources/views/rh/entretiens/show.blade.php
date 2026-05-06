@extends('layouts.app')

@section('title', 'Détails de l\'entretien')

@push('styles')
<style>
.card-body {
    padding: 0.5rem !important;
}
.small {
    font-size: 0.7rem !important;
}
.btn-sm {
    font-size: 0.7rem !important;
    padding: 0.2rem 0.4rem !important;
}
.badge {
    font-size: 0.6rem !important;
}
.mb-2 {
    margin-bottom: 0.5rem !important;
}
.py-1 {
    padding-top: 0.25rem !important;
    padding-bottom: 0.25rem !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="fas fa-calendar-check text-primary me-2"></i>
                Entretien - {{ $entretien->candidature->nom }} {{ $entretien->candidature->prenom }}
            </h2>
            <p class="text-muted mb-0">
                <i class="fas fa-clock me-1"></i>
                {{ $entretien->date_entretien->format('d/m/Y') }} à {{ $entretien->heure_entretien->format('H:i') }}
            </p>
        </div>
        <div>
            <a href="{{ route('rh.entretiens.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations du candidat -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-user"></i> Candidat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                             style="width: 60px; height: 60px; font-size: 20px;">
                            {{ strtoupper(substr($entretien->candidature->prenom, 0, 1)) }}{{ strtoupper(substr($entretien->candidature->nom, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $entretien->candidature->nom }} {{ $entretien->candidature->prenom }}</h5>
                            <p class="text-muted mb-0">{{ $entretien->candidature->email }}</p>
                            <p class="text-muted mb-0">{{ $entretien->candidature->telephone }}</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <strong>Adresse :</strong> {{ $entretien->candidature->adresse }}
                    </div>
                    
                    <div class="info-item">
                        <strong>Formation :</strong> {{ $entretien->candidature->dernier_diplome ?? 'Non spécifié' }}
                    </div>
                    
                    <div class="info-item">
                        <strong>Établissement :</strong> {{ $entretien->candidature->etablissement ?? 'Non spécifié' }}
                    </div>
                    
                    <div class="info-item">
                        <strong>Année diplôme :</strong> {{ $entretien->candidature->annee_diplome ?? 'Non spécifié' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Détails de l'entretien -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar"></i> Détails de l'entretien
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <strong>Date :</strong> {{ $entretien->date_entretien->format('d/m/Y') }}
                    </div>
                    
                    <div class="info-item">
                        <strong>Heure :</strong> {{ $entretien->heure_entretien->format('H:i') }}
                    </div>
                    
                    <div class="info-item">
                        <strong>Lieu :</strong> {{ $entretien->lieu_entretien }}
                    </div>
                    
                    <div class="info-item">
                        <strong>Statut :</strong>
                        @switch($entretien->statut)
                            @case('planifie')
                                <span class="badge bg-info">
                                    <i class="fas fa-clock me-1"></i>{{ $entretien->statut_label }}
                                </span>
                                @break
                            @case('en_cours')
                                <span class="badge bg-warning">
                                    <i class="fas fa-spinner me-1"></i>{{ $entretien->statut_label }}
                                </span>
                                @break
                            @case('termine')
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>{{ $entretien->statut_label }}
                                </span>
                                @break
                            @case('annule')
                                <span class="badge bg-danger">
                                    <i class="fas fa-times me-1"></i>{{ $entretien->statut_label }}
                                </span>
                                @break
                        @endswitch
                    </div>
                    
                    @if($entretien->notes_entretien)
                        <div class="info-item">
                            <strong>Notes :</strong>
                            <p class="text-muted mt-1">{{ $entretien->notes_entretien }}</p>
                        </div>
                    @endif
                    
                    @if($entretien->isEvalue())
                        <hr>
                        <div class="alert alert-info">
                            <h6 class="mb-2">
                                <i class="fas fa-clipboard-check me-2"></i>Évaluation
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Note :</strong> 
                                    <span class="badge bg-primary fs-6">{{ $entretien->note_evaluation }}/20</span>
                                    <small class="text-muted d-block">{{ $entretien->note_label }}</small>
                                </div>
                                <div class="col-md-6">
                                    <strong>Évalué par :</strong> 
                                    <div>{{ $entretien->evaluateur?->nom ?? 'N/A' }} {{ $entretien->evaluateur?->prenom ?? '' }}</div>
                                    <small class="text-muted">{{ $entretien->evaluated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                            @if($entretien->commentaires_evaluation)
                                <div class="mt-3">
                                    <strong>Commentaires :</strong>
                                    <p class="text-muted mt-1">{{ $entretien->commentaires_evaluation }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Offre associée -->
    <div class="card border-0 shadow-sm mb-2">
        <div class="card-body py-1">
            <div class="row g-0">
                <div class="col-md-6">
                    <div class="small fw-bold">{{ $entretien->candidature->offreStage?->titre ?? 'N/A' }}</div>
                    <div class="small text-muted">{{ $entretien->candidature->offreStage?->entreprise?->nom ?? 'N/A' }}</div>
                    <div class="small text-muted">{{ $entretien->candidature->offreStage?->lieu ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="small text-muted">{{ $entretien->candidature->offreStage?->duree_semaines ?? 'N/A' }} sem.</div>
                    <div>@if($entretien->candidature->offreStage?->statut == 'affectee')<span class="badge bg-warning" style="font-size: 0.65rem;">Affectée</span>@else<span class="badge bg-success" style="font-size: 0.65rem;">Disponible</span>@endif</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body text-center py-2">
            <h6 class="mb-2 small fw-bold">Décision sur la candidature</h6>
            <div class="row g-2">
                <div class="col-md-6">
                    <form method="POST" action="{{ route('rh.candidatures.accepter', $entretien->candidature) }}" class="d-inline">
                        @csrf
                        <button type="submit" 
                                class="btn btn-success w-100 btn-sm"
                                style="font-size: 0.8rem; padding: 0.3rem 0.6rem;"
                                onclick="return confirm('Accepter cette candidature ?')">
                            <i class="fas fa-user-check me-1"></i>
                            Accepter
                        </button>
                    </form>
                    <small class="text-muted d-block mt-1">Affecter l'offre</small>
                </div>
                <div class="col-md-6">
                    <form method="POST" action="{{ route('rh.candidatures.refuser', $entretien->candidature) }}" class="d-inline">
                        @csrf
                        <button type="submit" 
                                class="btn btn-danger w-100 btn-sm"
                                style="font-size: 0.8rem; padding: 0.3rem 0.6rem;"
                                onclick="return confirm('Refuser cette candidature ?')">
                            <i class="fas fa-user-times me-1"></i>
                            Refuser
                        </button>
                    </form>
                    <small class="text-muted d-block mt-1">Rejeter le candidat</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Section d'évaluation -->
    @if($entretien->isPlanifie())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-check"></i> Évaluer l'entretien
                </h5>
            </div>
            <div class="card-body">
                @if(!$entretien->isTermine())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Vous pouvez évaluer cet entretien même s'il n'est pas encore terminé.
                    </div>
                @endif
                
                <form method="POST" action="{{ route('rh.entretiens.evaluer', $entretien) }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="note_evaluation" class="form-label">
                                        <i class="fas fa-star me-1"></i> Note sur 20 *
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="note_evaluation" 
                                           name="note_evaluation" 
                                           min="0" 
                                           max="20" 
                                           step="0.5"
                                           required>
                                    <small class="text-muted">Notez le candidat sur 20</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="commentaires_evaluation" class="form-label">
                                <i class="fas fa-comment me-1"></i> Commentaires d'évaluation *
                            </label>
                            <textarea class="form-control" 
                                      id="commentaires_evaluation" 
                                      name="commentaires_evaluation" 
                                      rows="4" 
                                      placeholder="Décrivez votre évaluation du candidat..."
                                      required></textarea>
                            <small class="text-muted">Commentez votre évaluation et votre décision</small>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" name="decision" value="en_attente" class="btn btn-secondary">
                                <i class="fas fa-clock"></i> En attente
                            </button>
                        </div>
                    </form>
            </div>
        </div>
    @endif

    <!-- Documents du candidat -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-file-alt"></i> Documents du candidat
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>CV</h6>
                    @if($entretien->candidature->cv_path)
                        <a href="{{ asset('storage/' . $entretien->candidature->cv_path) }}" 
                           target="_blank" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-file-pdf"></i> Voir le CV
                        </a>
                    @else
                        <span class="text-muted">CV non fourni</span>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <h6>Lettre de motivation</h6>
                    @if($entretien->candidature->lettre_motivation)
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#lettreMotivationModal">
                            <i class="fas fa-envelope"></i> Voir la lettre
                        </button>
                    @else
                        <span class="text-muted">Lettre non fournie</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lettre de motivation -->
<div class="modal fade" id="lettreMotivationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-envelope me-2"></i>Lettre de motivation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Candidat :</strong> {{ $entretien->candidature->nom }} {{ $entretien->candidature->prenom }}
                </div>
                <div class="mb-3">
                    <strong>Offre :</strong> {{ $entretien->candidature->offreStage?->titre ?? 'N/A' }}
                </div>
                <hr>
                <div class="lettre-motivation-content">
                    {!! nl2br(e($entretien->candidature->lettre_motivation)) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<style>
.info-item {
    margin-bottom: 1rem;
}

.info-item strong {
    color: #495057;
}

.lettre-motivation-content {
    max-height: 400px;
    overflow-y: auto;
    white-space: pre-wrap;
    line-height: 1.6;
}
</style>
@endsection
