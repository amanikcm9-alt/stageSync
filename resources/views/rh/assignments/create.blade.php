@extends('layouts.app')

@section('title', 'Nouvelle Affectation')

@section('content')
<div class="container-fluid py-4">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-user-plus text-primary"></i> 
                Nouvelle Affectation
            </h2>
            <small class="text-muted">
                Affecter un stagiaire à un encadrant
            </small>
        </div>
        <div>
            <a href="{{ route('rh.assignments.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if($selectedStagiaire)
    <!-- Stagiaire pré-sélectionné -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="fas fa-user-graduate me-2"></i>
                Stagiaire sélectionné
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Nom complet :</strong> {{ $selectedStagiaire->nom }} {{ $selectedStagiaire->prenom }}
                </div>
                <div class="col-md-6">
                    <strong>Email :</strong> {{ $selectedStagiaire->email }}
                </div>
                @if($selectedStagiaire->candidature && $selectedStagiaire->candidature->offreStage)
                <div class="col-md-6 mt-3">
                    <strong>Offre :</strong> 
                    <span class="badge bg-info">{{ $selectedStagiaire->candidature->offreStage->titre }}</span>
                </div>
                <div class="col-md-6 mt-3">
                    <strong>Secteur requis :</strong> 
                    <span class="badge bg-warning">{{ $selectedStagiaire->candidature->offreStage->secteur->nom ?? 'N/A' }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Filtre intelligent activé -->
    @if($selectedStagiaire && $encadrants->count() > 0)
        @if($selectedStagiaire->candidature && $selectedStagiaire->candidature->offreStage && $selectedStagiaire->candidature->offreStage->secteur)
            <div class="alert alert-info mb-4">
                <i class="fas fa-filter me-2"></i>
                <strong>Filtre intelligent activé</strong> : 
                Affichage uniquement des encadrants du secteur 
                <strong>"{{ $selectedStagiaire->candidature->offreStage->secteur->nom }}"</strong>
            </div>
        @else
            <div class="alert alert-warning mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Affichage de tous les encadrants</strong> : 
                Le secteur du stagiaire n'est pas défini, tous les encadrants disponibles sont affichés
            </div>
        @endif
    @endif

    <!-- Liste des encadrants -->
    @if($encadrants->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Encadrants disponibles
                    <span class="badge bg-success ms-2">{{ $encadrants->count() }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="row g-3 p-3">
                    @foreach($encadrants as $encadrant)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border h-100">
                                <div class="card-body">
                                    <!-- En-tête de l'encadrant -->
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            {{ strtoupper(substr($encadrant['prenom'], 0, 1)) . strtoupper(substr($encadrant['nom'], 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $encadrant['nom'] }} {{ $encadrant['prenom'] }}</h6>
                                            <small class="text-muted">{{ $encadrant['email'] }}</small>
                                        </div>
                                    </div>

                                    <!-- Informations utiles -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-briefcase me-1"></i>
                                                {{ $encadrant['secteur'] }}
                                            </span>
                                            <span class="badge bg-{{ $encadrant['couleur_disponibilite'] }}">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $encadrant['disponibilite'] }}
                                            </span>
                                        </div>
                                        
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-{{ $encadrant['couleur_disponibilite'] }}" 
                                                 style="width: {{ min($encadrant['nombre_stagiaires'] * 20, 100) }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $encadrant['nombre_stagiaires'] }} stagiaire{{ $encadrant['nombre_stagiaires'] > 1 ? 's' : '' }} affecté{{ $encadrant['nombre_stagiaires'] > 1 ? 's' : '' }}
                                        </small>
                                    </div>

                                    <!-- Bouton d'affectation -->
                                    <form method="POST" action="{{ route('rh.assignments.store') }}">
                                        @csrf
                                        <input type="hidden" name="stagiaire_id" value="{{ $selectedStagiaire->id }}">
                                        <input type="hidden" name="encadrant_id" value="{{ $encadrant['id'] }}">
                                        <button type="submit" 
                                                class="btn btn-primary w-100"
                                                onclick="return confirm('Affecter {{ $encadrant['nom'] }} {{ $encadrant['prenom'] }} à {{ $selectedStagiaire->nom }} {{ $selectedStagiaire->prenom }} ?')">
                                            <i class="fas fa-user-plus me-1"></i>
                                            Affecter cet encadrant
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucun encadrant disponible</h5>
                <p class="text-muted">
                    @if($selectedStagiaire)
                        Aucun encadrant trouvé dans le secteur 
                        <strong>"{{ $selectedStagiaire->candidature->offreStage->secteur->nom ?? 'N/A' }}"</strong>.
                    @else
                        Veuillez sélectionner un stagiaire pour voir les encadrants disponibles.
                    @endif
                </p>
            </div>
        </div>
    @endif

    @if(!$selectedStagiaire)
    <!-- Formulaire de sélection de stagiaire -->
    <div class="card border-0 shadow-sm">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('rh.assignments.create') }}">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="stagiaire_id" name="stagiaire_id" required onchange="this.form.submit()">
                                <option value="">Sélectionner un stagiaire</option>
                                @foreach($stagiaires as $stagiaire)
                                    <option value="{{ $stagiaire->id }}">
                                        {{ $stagiaire->nom }} {{ $stagiaire->prenom }}
                                        @if($stagiaire->encadrant_id)
                                            (déjà affecté à {{ $stagiaire->encadrant->nom }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <label for="stagiaire_id">Stagiaire</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

<!-- Styles -->
<style>
.form-floating label {
    color: #6c757d;
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-select:focus ~ label {
    color: #0d6efd;
}

.alert {
    border-radius: 0.5rem;
}

.btn {
    border-radius: 0.375rem;
}
</style>
@endsection
