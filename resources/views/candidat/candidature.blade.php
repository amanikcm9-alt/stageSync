@extends('layouts.app')

@section('title', 'Détails Candidature')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-briefcase"></i> Détails de ma Candidature
                        </h4>
                        <a href="{{ route('candidat.dashboard') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informations de l'offre -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h5 class="mb-3">
                                <i class="fas fa-briefcase"></i> {{ $candidature->offreStage->titre }}
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Entreprise</label>
                                        <div class="d-flex align-items-center">
                                            @if($candidature->offreStage->entreprise->logo_path)
                                                <img src="{{ asset('storage/' . $candidature->offreStage->entreprise->logo_path) }}" 
                                                     alt="{{ $candidature->offreStage->entreprise->nom }}" 
                                                     class="rounded me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $candidature->offreStage->entreprise->nom }}</div>
                                                <small class="text-muted">{{ $candidature->offreStage->entreprise->secteur_activite }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Lieu</label>
                                        <div>
                                            <i class="fas fa-map-marker-alt text-danger"></i> 
                                            {{ $candidature->offreStage->lieu }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Durée</label>
                                        <div>
                                            <i class="fas fa-clock text-primary"></i> 
                                            {{ $candidature->offreStage->duree_semaines }} semaines
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Rémunération</label>
                                        <div>
                                            <i class="fas fa-euro-sign text-success"></i> 
                                            {{ $candidature->offreStage->remuneration_formatee }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label class="form-label text-muted">Description</label>
                                <p class="card-text">{{ $candidature->offreStage->description }}</p>
                            </div>

                            <!-- Missions -->
                            <div class="mb-3">
                                <label class="form-label text-muted">Missions</label>
                                <p class="card-text">{{ $candidature->offreStage->missions }}</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Statut de la candidature -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-body text-center">
                                    <div class="status-icon mb-3">
                                        <i class="fas fa-{{ 
                                            $candidature->statut === 'recue' ? 'inbox' : 
                                            ($candidature->statut === 'en_cours' ? 'clock' : 
                                            ($candidature->statut === 'accepte' ? 'check-circle' : 'times-circle')) }} fa-3x text-{{ 
                                            $candidature->statut === 'recue' ? 'warning' : 
                                            ($candidature->statut === 'en_cours' ? 'info' : 
                                            ($candidature->statut === 'accepte' ? 'success' : 'danger') }}"></i>
                                    </div>
                                    <h5 class="fw-bold">{{ 
                                        $candidature->statut === 'recue' ? 'Candidature Reçue' : 
                                        ($candidature->statut === 'en_cours' ? 'En cours de traitement' : 
                                        ($candidature->statut === 'accepte' ? 'Candidature Acceptée !' : 'Candidature Refusée') }}</h5>
                                    
                                    @if($candidature->date_decision)
                                        <small class="text-muted">
                                            Décision le {{ $candidature->date_decision->format('d/m/Y H:i') }}
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <!-- Informations de soumission -->
                            <div class="card border-0 mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-paper-plane"></i> Informations de soumission
                                    </h6>
                                    <div class="small">
                                        <div class="mb-2">
                                            <strong>Date de soumission :</strong><br>
                                            {{ $candidature->created_at->format('d/m/Y H:i') }}
                                        </div>
                                        @if($candidature->message)
                                            <div class="mb-2">
                                                <strong>Message accompagnant :</strong><br>
                                                <em>{{ $candidature->message }}</em>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>Documents :</strong><br>
                                            <i class="fas fa-file-pdf text-danger"></i> CV déposé<br>
                                            @if($candidature->lettre_motivation_path)
                                                <i class="fas fa-file-alt text-primary"></i> Lettre de motivation déposée
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Entretien -->
                            @if($candidature->date_entretien)
                                <div class="card border-0 border-info mb-4">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-calendar-check"></i> Entretien planifié
                                        </h6>
                                        <div class="small">
                                            <div class="mb-2">
                                                <strong>Date :</strong> {{ $candidature->date_entretien->format('d/m/Y') }}
                                            </div>
                                            @if($candidature->heure_entretien)
                                                <div class="mb-2">
                                                    <strong>Heure :</strong> {{ $candidature->heure_entretien }}
                                                </div>
                                            @endif
                                            @if($candidature->lieu_entretien)
                                                <div class="mb-2">
                                                    <strong>Lieu :</strong> {{ $candidature->lieu_entretien }}
                                                </div>
                                            @endif
                                            @if($candidature->notes_entretien)
                                                <div>
                                                    <strong>Notes :</strong><br>
                                                    <em>{{ $candidature->notes_entretien }}</em>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Commentaire RH -->
                            @if($candidature->commentaire)
                                <div class="card border-0 border-success mb-4">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-comment"></i> Commentaire RH
                                        </h6>
                                        <div class="small">
                                            <em>{{ $candidature->commentaire }}</em>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Motif de refus -->
                            @if($candidature->motif_refus)
                                <div class="card border-0 border-danger">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-times-circle"></i> Motif de refus
                                        </h6>
                                        <div class="small text-danger">
                                            {{ $candidature->motif_refus }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
