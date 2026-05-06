@extends('layouts.app')

@section('title', 'Évaluer l\'organisation')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-building text-primary"></i> 
                Évaluer l'organisation
            </h2>
            <small class="text-muted">
                Évaluez l'organisation de votre stage et l'entreprise d'accueil
            </small>
        </div>
        <div>
            <a href="{{ route('stagiaire.evaluations.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour aux évaluations
            </a>
        </div>
    </div>

    <!-- Formulaire d'évaluation -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0">Formulaire d'évaluation de l'organisation</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('evaluations.store') }}">
                @csrf
                
                <input type="hidden" name="evaluation_type" value="organisation">
                <input type="hidden" name="stagiaire_id" value="{{ Auth::user()->id }}">

                <!-- Informations sur l'entreprise -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Nom de l'entreprise</label>
                        <input type="text" class="form-control" name="entreprise_nom" 
                               value="{{ Auth::user()->offre_stage->entreprise->nom ?? '' }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Période d'évaluation</label>
                        <input type="text" class="form-control" name="periode" 
                               value="{{ now()->format('F Y') }}" required>
                    </div>
                </div>

                <!-- Critères d'évaluation -->
                <div class="mb-4">
                    <h6 class="mb-3">Critères d'évaluation</h6>
                    
                    <!-- Accueil et intégration -->
                    <div class="mb-3">
                        <label class="form-label">Accueil et intégration</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="accueil_integration" 
                                       id="accueil_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="accueil_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très insatisfaisant) - 5 (Excellent)</small>
                    </div>

                    <!-- Organisation du travail -->
                    <div class="mb-3">
                        <label class="form-label">Organisation du travail</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="organisation_travail" 
                                       id="travail_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="travail_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très insatisfaisant) - 5 (Excellent)</small>
                    </div>

                    <!-- Environnement de travail -->
                    <div class="mb-3">
                        <label class="form-label">Environnement de travail</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="environnement_travail" 
                                       id="environnement_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="environnement_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très insatisfaisant) - 5 (Excellent)</small>
                    </div>

                    <!-- Ressources mises à disposition -->
                    <div class="mb-3">
                        <label class="form-label">Ressources mises à disposition</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ressources" 
                                       id="ressources_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="ressources_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très insatisfaisant) - 5 (Excellent)</small>
                    </div>
                </div>

                <!-- Commentaires -->
                <div class="mb-4">
                    <label class="form-label">Commentaires supplémentaires</label>
                    <textarea class="form-control" name="commentaires" rows="4" 
                              placeholder="Veuillez partager vos commentaires sur l'organisation..."></textarea>
                </div>

                <!-- Suggestions -->
                <div class="mb-4">
                    <label class="form-label">Suggestions d'amélioration</label>
                    <textarea class="form-control" name="suggestions" rows="4" 
                              placeholder="Quelles pourraient être les améliorations ?"></textarea>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Sauvegarder l'évaluation
                    </button>
                    <a href="{{ route('stagiaire.evaluations.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
