@extends('layouts.app')

@section('title', 'Créer un Secteur')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-industry text-primary me-2"></i>
                Créer un Secteur
            </h4>
            <small class="text-muted">Ajouter un nouveau secteur d'activité</small>
        </div>
        <div>
            <a href="{{ route('rh.secteurs.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations du Secteur</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rh.secteurs.store') }}" method="POST">
                        @csrf

                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du secteur <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Ex: Informatique, Marketing, Finance, etc.</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Description détaillée du secteur d'activité (optionnel)</div>
                        </div>

                        <!-- Statut -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1" checked>
                                <label class="form-check-label" for="actif">
                                    Secteur actif
                                </label>
                            </div>
                            <div class="form-text">Un secteur actif peut être utilisé dans les offres de stage</div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rh.secteurs.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Créer le secteur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Informations
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Les secteurs d'activité permettent de classer les offres de stage par domaine professionnel.
                    </p>
                    <hr>
                    <h6 class="text-primary">Conseils</h6>
                    <ul class="small text-muted">
                        <li>Utilisez des noms clairs et concis</li>
                        <li>Évitez les abréviations</li>
                        <li>Un secteur peut être archivé mais pas supprimé s'il est utilisé</li>
                    </ul>
                </div>
            </div>

            <!-- Statistiques actuelles -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-1"></i>
                        Statistiques actuelles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">{{ \App\Models\Secteur::actif()->count() }}</h4>
                                <small class="text-muted">Secteurs actifs</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-secondary mb-1">{{ \App\Models\Secteur::count() }}</h4>
                            <small class="text-muted">Total secteurs</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
