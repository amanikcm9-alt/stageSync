@extends('layouts.app')

@section('title', 'Mon Stage')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title">Mon Stage</h1>
        <p class="page-subtitle">Informations détaillées de mon stage</p>
    </div>
</div>

<div class="container">
    @if(auth()->user()->offre_stage_id)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-briefcase"></i> Informations du Stage
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Informations du stage</h6>
                    <p><strong>Entreprise :</strong> {{ auth()->user()->offre_stage->entreprise->nom ?? 'Non spécifiée' }}</p>
                    <p><strong>Poste :</strong> {{ auth()->user()->offre_stage->poste ?? 'Non spécifié' }}</p>
                    <p><strong>Période :</strong> 
                        @if(auth()->user()->offre_stage->date_debut && auth()->user()->offre_stage->date_fin)
                            Du {{ auth()->user()->offre_stage->date_debut->format('d/m/Y') }} au {{ auth()->user()->offre_stage->date_fin->format('d/m/Y') }}
                        @else
                            Non définie
                        @endif
                    </p>
                    <p><strong>Lieu :</strong> {{ auth()->user()->offre_stage->lieu ?: 'Non spécifié' }}</p>
                    <p><strong>Description :</strong> {{ auth()->user()->offre_stage->description ?: 'Non spécifiée' }}</p>
                    
                    <!-- Informations de l'encadrant -->
                    <h6 class="text-primary mt-3">Encadrant associé</h6>
                    @if(auth()->user()->encadrant)
                        <p><strong>Nom :</strong> {{ auth()->user()->encadrant->nom ?? 'Non spécifié' }} {{ auth()->user()->encadrant->prenom ?? 'Non spécifié' }}</p>
                        <p><strong>Email :</strong> {{ auth()->user()->encadrant->email ?? 'Non spécifié' }}</p>
                        <p><strong>Téléphone :</strong> {{ auth()->user()->encadrant->telephone ?? 'Non spécifié' }}</p>
                        @if(auth()->user()->encadrant_faculte)
                            <p><strong>Encadrant faculté :</strong> {{ auth()->user()->encadrant_faculte->nom ?? 'Non spécifié' }} {{ auth()->user()->encadrant_faculte->prenom ?? 'Non spécifié' }}</p>
                        @endif
                        @if(auth()->user()->encadrant_entreprise)
                            <p><strong>Encadrant entreprise :</strong> {{ auth()->user()->encadrant_entreprise->nom ?? 'Non spécifié' }} {{ auth()->user()->encadrant_entreprise->prenom ?? 'Non spécifié' }}</p>
                        @endif
                    @else
                        <p class="text-muted">Aucun encadrant assigné</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Détails</h6>
                    <p><strong>Salaire :</strong> {{ auth()->user()->offre_stage->salaire ? number_format(auth()->user()->offre_stage->salaire, 2, ',', ' ') . ' &euro;' : 'Non spécifié' }}</p>
                    <p><strong>Heures/semaine :</strong> {{ auth()->user()->offre_stage->heures_semaine ?: '35h' }}</p>
                    <p><strong>Statut :</strong> 
                        <span class="badge bg-{{ auth()->user()->offre_stage->statut === 'actif' ? 'success' : 'secondary' }} text-white">
                            {{ ucfirst(auth()->user()->offre_stage->statut) }}
                        </span>
                    </p>
                    <p><strong>Date de candidature :</strong> 
                        @if(auth()->user()->offre_stage->created_at)
                            {{ auth()->user()->offre_stage->created_at->format('d/m/Y H:i') }}
                        @else
                            Non spécifiée
                        @endif
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('offres.show', auth()->user()->offre_stage) }}" class="btn btn-outline-primary btn-sm me-2">
                            <i class="fas fa-eye"></i> Voir l'offre complète
                        </a>
                        <a href="{{ route('stagiaire.dashboard') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour au dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-briefcase fa-3x text-warning mb-3"></i>
            <h5 class="text-muted">Aucune offre de stage assignée</h5>
            <p class="text-muted">Vous n'avez pas encore d'offre de stage associée à votre compte.</p>
            <p class="text-muted">Veuillez contacter le service RH pour obtenir une affectation.</p>
            <div class="mt-3">
                <a href="{{ route('offres') }}" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Consulter les offres
                </a>
                <a href="{{ route('stagiaire.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour au dashboard
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
