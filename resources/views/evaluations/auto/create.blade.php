@extends('layouts.app')

@section('title', 'Auto-évaluation')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-user-check text-info"></i> 
                Auto-évaluation
            </h2>
            <small class="text-muted">
                Évaluez votre propre travail et vos compétences
            </small>
        </div>
        <div>
            <a href="{{ route('stagiaire.evaluations.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour aux évaluations
            </a>
        </div>
    </div>

    <!-- Formulaire d'auto-évaluation -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0">Formulaire d'auto-évaluation</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('evaluations.store') }}">
                @csrf
                
                <input type="hidden" name="evaluation_type" value="auto">
                <input type="hidden" name="stagiaire_id" value="{{ Auth::user()->id }}">

                <!-- Période d'évaluation -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Période d'évaluation</label>
                        <input type="text" class="form-control" name="periode" 
                               value="{{ now()->format('F Y') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Stage/Projet concerné</label>
                        <input type="text" class="form-control" name="projet" 
                               placeholder="Nom du stage ou projet" required>
                    </div>
                </div>

                <!-- Auto-évaluation des compétences -->
                <div class="mb-4">
                    <h6 class="mb-3">Auto-évaluation des compétences</h6>
                    
                    <!-- Compétences techniques -->
                    <div class="mb-3">
                        <label class="form-label">Compétences techniques acquises</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="competences_techniques" 
                                       id="tech_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="tech_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très faible) - 5 (Excellent)</small>
                    </div>

                    <!-- Autonomie -->
                    <div class="mb-3">
                        <label class="form-label">Autonomie et initiative</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="autonomie" 
                                       id="autonomie_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="autonomie_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très faible) - 5 (Excellent)</small>
                    </div>

                    <!-- Qualité du travail -->
                    <div class="mb-3">
                        <label class="form-label">Qualité du travail réalisé</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="qualite_travail" 
                                       id="qualite_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="qualite_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très faible) - 5 (Excellent)</small>
                    </div>

                    <!-- Respect des délais -->
                    <div class="mb-3">
                        <label class="form-label">Respect des délais et organisation</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="respect_delais" 
                                       id="delais_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="delais_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très faible) - 5 (Excellent)</small>
                    </div>

                    <!-- Communication -->
                    <div class="mb-3">
                        <label class="form-label">Communication et collaboration</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="communication" 
                                       id="comm_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="comm_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très faible) - 5 (Excellent)</small>
                    </div>
                </div>

                <!-- Bilan personnel -->
                <div class="mb-4">
                    <h6 class="mb-3">Bilan personnel</h6>
                    
                    <!-- Réalisations -->
                    <div class="mb-3">
                        <label class="form-label">Principales réalisations</label>
                        <textarea class="form-control" name="realisations" rows="3" 
                                  placeholder="Quelles sont vos principales réalisations durant cette période ?"></textarea>
                    </div>

                    <!-- Difficultés rencontrées -->
                    <div class="mb-3">
                        <label class="form-label">Difficultés rencontrées et solutions apportées</label>
                        <textarea class="form-control" name="difficultes" rows="3" 
                                  placeholder="Quelles difficultés avez-vous rencontrées et comment les avez-vous surmontées ?"></textarea>
                    </div>

                    <!-- Apprentissages -->
                    <div class="mb-3">
                        <label class="form-label">Ce que j'ai appris</label>
                        <textarea class="form-control" name="apprentissages" rows="3" 
                                  placeholder="Quelles compétences ou connaissances avez-vous acquises ?"></textarea>
                    </div>
                </div>

                <!-- Objectifs futurs -->
                <div class="mb-4">
                    <h6 class="mb-3">Objectifs futurs</h6>
                    
                    <!-- Objectifs à court terme -->
                    <div class="mb-3">
                        <label class="form-label">Objectifs à court terme (prochains 3 mois)</label>
                        <textarea class="form-control" name="objectifs_court_terme" rows="2" 
                                  placeholder="Quels sont vos objectifs pour les prochains mois ?"></textarea>
                    </div>

                    <!-- Objectifs à long terme -->
                    <div class="mb-3">
                        <label class="form-label">Objectifs à long terme</label>
                        <textarea class="form-control" name="objectifs_long_terme" rows="2" 
                                  placeholder="Quelles sont vos ambitions professionnelles ?"></textarea>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save me-2"></i>Sauvegarder l'auto-évaluation
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
