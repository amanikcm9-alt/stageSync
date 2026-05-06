@extends('layouts.app')

@section('title', 'Évaluer l\'encadrant')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-user-tie text-success"></i> 
                Évaluer l'encadrant
            </h2>
            <small class="text-muted">
                Évaluez l'encadrement et le suivi de votre encadrant
            </small>
        </div>
        <div>
            <a href="{{ route('stagiaire.evaluations.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour aux évaluations
            </a>
        </div>
    </div>

    <!-- Informations sur l'encadrant -->
    @if(Auth::user()->encadrant)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    {{ strtoupper(substr(Auth::user()->encadrant->prenom, 0, 1)) }}{{ strtoupper(substr(Auth::user()->encadrant->nom, 0, 1)) }}
                </div>
                <div>
                    <h6 class="mb-1">{{ Auth::user()->encadrant->prenom }} {{ Auth::user()->encadrant->nom }}</h6>
                    <small class="text-muted">{{ Auth::user()->encadrant->email }}</small>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Formulaire d'évaluation -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0">Formulaire d'évaluation de l'encadrant</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('evaluations.store') }}">
                @csrf
                
                <input type="hidden" name="evaluation_type" value="encadrant">
                <input type="hidden" name="stagiaire_id" value="{{ Auth::user()->id }}">
                @if(Auth::user()->encadrant)
                <input type="hidden" name="encadrant_id" value="{{ Auth::user()->encadrant->id }}">
                @endif

                <!-- Critères d'évaluation -->
                <div class="mb-4">
                    <h6 class="mb-3">Critères d'évaluation</h6>
                    
                    <!-- Disponibilité -->
                    <div class="mb-3">
                        <label class="form-label">Disponibilité et réactivité</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="disponibilite" 
                                       id="disponibilite_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="disponibilite_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très insatisfaisant) - 5 (Excellent)</small>
                    </div>

                    <!-- Qualité de l'encadrement -->
                    <div class="mb-3">
                        <label class="form-label">Qualité de l'encadrement technique</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="encadrement_technique" 
                                       id="technique_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="technique_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très insatisfaisant) - 5 (Excellent)</small>
                    </div>

                    <!-- Pédagogie -->
                    <div class="mb-3">
                        <label class="form-label">Approche pédagogique</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pedagogie" 
                                       id="pedagogie_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="pedagogie_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très insatisfaisant) - 5 (Excellent)</small>
                    </div>

                    <!-- Communication -->
                    <div class="mb-3">
                        <label class="form-label">Qualité de la communication</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="communication" 
                                       id="communication_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="communication_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très insatisfaisant) - 5 (Excellent)</small>
                    </div>

                    <!-- Soutien moral -->
                    <div class="mb-3">
                        <label class="form-label">Soutien moral et motivation</label>
                        <div class="d-flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="soutien_moral" 
                                       id="soutien_{{ $i }}" value="{{ $i }}" required>
                                <label class="form-check-label" for="soutien_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        <small class="text-muted">1 (Très insatisfaisant) - 5 (Excellent)</small>
                    </div>
                </div>

                <!-- Commentaires -->
                <div class="mb-4">
                    <label class="form-label">Commentaires sur l'encadrement</label>
                    <textarea class="form-control" name="commentaires" rows="4" 
                              placeholder="Veuillez partager vos commentaires sur l'encadrement reçu..."></textarea>
                </div>

                <!-- Points forts -->
                <div class="mb-4">
                    <label class="form-label">Points forts de l'encadrant</label>
                    <textarea class="form-control" name="points_forts" rows="3" 
                              placeholder="Quels sont les points forts de votre encadrant ?"></textarea>
                </div>

                <!-- Axes d'amélioration -->
                <div class="mb-4">
                    <label class="form-label">Axes d'amélioration suggérés</label>
                    <textarea class="form-control" name="axes_amelioration" rows="3" 
                              placeholder="Quelles pourraient être les améliorations ?"></textarea>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
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
