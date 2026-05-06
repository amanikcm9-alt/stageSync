@extends('layouts.app')

@section('title', 'Évaluer un stagiaire')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-user-graduate text-primary"></i> 
                Évaluer un stagiaire
            </h2>
            <small class="text-muted">
                Évaluez les performances et compétences de vos stagiaires
            </small>
        </div>
        <div>
            <a href="{{ route('encadrant.evaluations.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour aux évaluations
            </a>
        </div>
    </div>

    <!-- Formulaire d'évaluation -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0">Formulaire d'évaluation de stagiaire</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('evaluations.store') }}">
                @csrf
                
                <input type="hidden" name="evaluation_type" value="stagiaire">
                <input type="hidden" name="encadrant_id" value="{{ Auth::user()->id }}">

                <!-- Sélection du stagiaire -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Stagiaire à évaluer *</label>
                        <select name="stagiaire_id" class="form-select" required>
                            <option value="">Sélectionnez un stagiaire</option>
                            @foreach($stagiaires as $stagiaire)
                            <option value="{{ $stagiaire->id }}">{{ $stagiaire->prenom }} {{ $stagiaire->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Activité concernée</label>
                        <select name="activity_id" class="form-select">
                            <option value="">Sélectionnez une activité (optionnel)</option>
                        </select>
                    </div>
                </div>

                <!-- Critères d'évaluation -->
                <div class="mb-4">
                    <h6 class="mb-3">Critères d'évaluation</h6>
                    
                    <!-- Compétences techniques -->
                    <div class="mb-3">
                        <label class="form-label">Compétences techniques</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="note_technique" 
                                       id="technique_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="technique_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très faible) - 5 (Excellent)</small>
                    </div>

                    <!-- Qualité du travail -->
                    <div class="mb-3">
                        <label class="form-label">Qualité du travail</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="note_qualite" 
                                       id="qualite_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="qualite_{{ $i }}">{{ $i }}</label>
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
                                <input class="form-check-input" type="radio" name="note_autonomie" 
                                       id="autonomie_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="autonomie_{{ $i }}">{{ $i }}</label>
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
                                <input class="form-check-input" type="radio" name="note_communication" 
                                       id="communication_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="communication_{{ $i }}">{{ $i }}</label>
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
                                <input class="form-check-input" type="radio" name="note_delais" 
                                       id="delais_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="delais_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très faible) - 5 (Excellent)</small>
                    </div>
                </div>

                <!-- Note générale -->
                <div class="mb-4">
                    <h6 class="mb-3">Note générale</h6>
                    <div class="mb-3">
                        <label class="form-label">Note générale sur 20</label>
                        <input type="number" name="note_generale" class="form-control" 
                               min="0" max="20" step="0.5" required>
                        <small class="text-muted">Note finale sur 20</small>
                    </div>
                </div>

                <!-- Commentaires -->
                <div class="mb-4">
                    <h6 class="mb-3">Commentaires détaillés</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Points forts</label>
                        <textarea class="form-control" name="points_forts" rows="3" 
                                  placeholder="Quels sont les points forts du stagiaire ?"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Axes d'amélioration</label>
                        <textarea class="form-control" name="axes_amelioration" rows="3" 
                                  placeholder="Quels sont les axes d'amélioration suggérés ?"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Commentaires généraux</label>
                        <textarea class="form-control" name="commentaires" rows="4" 
                                  placeholder="Commentaires généraux sur le travail du stagiaire..."></textarea>
                    </div>
                </div>

                <!-- Recommandations -->
                <div class="mb-4">
                    <h6 class="mb-3">Recommandations</h6>
                    <div class="mb-3">
                        <label class="form-label">Recommandation finale</label>
                        <select name="recommandation" class="form-select">
                            <option value="">Sélectionnez une recommandation</option>
                            <option value="excellent">Excellent - Dépasse les attentes</option>
                            <option value="bon">Bon - Atteint les objectifs</option>
                            <option value="moyen">Moyen - À améliorer</option>
                            <option value="insuffisant">Insuffisant - En dessous des attentes</option>
                        </select>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Sauvegarder l'évaluation
                    </button>
                    <button type="submit" name="save_draft" value="1" class="btn btn-outline-secondary">
                        <i class="fas fa-file-alt me-2"></i>Sauvegarder comme brouillon
                    </button>
                    <a href="{{ route('encadrant.evaluations.index') }}" class="btn btn-outline-danger">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
