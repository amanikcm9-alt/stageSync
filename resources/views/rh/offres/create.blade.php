@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">
                <i class="fas fa-plus text-primary"></i> 
                Créer une Nouvelle Offre
            </h1>
            <p class="text-muted mb-0">
                Remplissez les informations ci-dessous pour créer une offre de stage
            </p>
        </div>
        <div>
            <a href="{{ route('rh.offres') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <form method="POST" action="{{ route('rh.offres.store') }}">
        @csrf
        
        <!-- Informations principales -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Informations Principales
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Titre de l'offre *</label>
                        <input type="text" class="form-control" name="titre" placeholder="Ex: Développeur Web Junior" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Secteur d'activité *</label>
                        <select class="form-select" name="secteur" required>
                            <option value="">Choisir un secteur...</option>
                            @foreach($secteurs as $key => $secteur)
                                <option value="{{ $key }}">{{ $secteur }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lieu du stage *</label>
                        <input type="text" class="form-control" name="lieu" placeholder="Ex: Paris (75)" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Durée (semaines) *</label>
                        <input type="number" class="form-control" name="duree_semaines" placeholder="12" min="1" max="52" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Rémunération (€/mois)</label>
                        <input type="number" class="form-control" name="remuneration" placeholder="800.00" min="0" max="9999.99" step="0.01">
                    </div>
                </div>
            </div>
        </div>

        <!-- Description et missions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt"></i> Description et Missions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Description de l'offre *</label>
                        <textarea class="form-control" name="description" rows="4" placeholder="Décrivez l'offre, le contexte, les avantages..." required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Missions principales *</label>
                        <textarea class="form-control" name="missions" rows="4" placeholder="Listez les tâches et responsabilités..." required></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dates et entreprise -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-calendar"></i> Période et Entreprise
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date de début</label>
                        <input type="date" class="form-control" name="date_debut">
                        <small class="text-muted">Laissez vide pour "immédiat"</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date de fin</label>
                        <input type="date" class="form-control" name="date_fin">
                        <small class="text-muted">Date limite de candidature</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Entreprise *</label>
                        <select class="form-select" name="entreprise_id" required>
                            <option value="">Choisir une entreprise...</option>
                            @foreach($entreprises as $entreprise)
                                <option value="{{ $entreprise->id }}">{{ $entreprise->nom }} ({{ $entreprise->ville }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statut -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-cog"></i> Publication
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Statut de l'offre *</label>
                        <select class="form-select" name="statut" required>
                            <option value="">Choisir un statut...</option>
                            <option value="brouillon">📝 Brouillon (non visible publiquement)</option>
                            <option value="publiee">✅ Publiée (visible publiquement)</option>
                            <option value="cloturee">❌ Clôturée (plus de candidatures)</option>
                        </select>
                        <small class="text-muted">
                            <strong>Brouillon :</strong> Enregistrement sans publication<br>
                            <strong>Publiée :</strong> Visible immédiatement par les candidats<br>
                            <strong>Clôturée :</strong> Plus recevable pour les candidatures
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('rh.offres') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Créer l'offre
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
