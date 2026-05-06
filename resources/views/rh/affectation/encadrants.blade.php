@extends('layouts.app')

@section('title', 'Choisir un encadrant')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="fas fa-user-tie text-primary me-2"></i>
                Choisir un encadrant
            </h2>
            <p class="text-muted mb-0">
                Pour : <strong>{{ $stagiaire->nom }} {{ $stagiaire->prenom }}</strong>
            </p>
        </div>
        <div>
            <a href="{{ route('rh.affectation.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Infos du stagiaire -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-user-graduate me-2"></i>
                Informations du stagiaire
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Nom complet :</strong> {{ $stagiaire->nom }} {{ $stagiaire->prenom }}
                </div>
                <div class="col-md-6">
                    <strong>Email :</strong> {{ $stagiaire->email }}
                </div>
                <div class="col-md-6 mt-3">
                    <strong>Offre :</strong> 
                    <span class="badge bg-info">{{ $stagiaire->candidature->offreStage->titre ?? 'N/A' }}</span>
                </div>
                <div class="col-md-6 mt-3">
                    <strong>Secteur requis :</strong> 
                    <span class="badge bg-warning">{{ $stagiaire->candidature->offreStage->secteur->nom ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtre intelligent -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-filter me-2"></i>
        <strong>Filtre intelligent activé</strong> : 
        Affichage uniquement des encadrants du secteur 
        <strong>"{{ $stagiaire->candidature->offreStage->secteur->nom ?? 'N/A' }}"</strong>
    </div>

    <!-- Liste des encadrants -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>
                Encadrants disponibles
                <span class="badge bg-success ms-2">{{ $encadrants->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($encadrants->count() > 0)
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

                                    <!-- Étape 3: Infos utiles -->
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
                                    <form method="POST" action="{{ route('rh.affectation.assign', $stagiaire->id) }}">
                                        @csrf
                                        <input type="hidden" name="encadrant_id" value="{{ $encadrant['id'] }}">
                                        <button type="submit" 
                                                class="btn btn-primary w-100"
                                                onclick="return confirm('Affecter {{ $encadrant['nom'] }} {{ $encadrant['prenom'] } à {{ $stagiaire->nom }} {{ $stagiaire->prenom }} ?')">
                                            <i class="fas fa-user-plus me-1"></i>
                                            Affecter cet encadrant
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun encadrant disponible</h5>
                    <p class="text-muted">
                        Aucun encadrant trouvé dans le secteur 
                        <strong>"{{ $stagiaire->candidature->offreStage->secteur->nom ?? 'N/A' }}"</strong>.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar {
    font-size: 1.2rem;
    font-weight: bold;
}
</style>
@endsection
