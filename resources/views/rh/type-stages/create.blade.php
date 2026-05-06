@extends('layouts.app')

@section('title', 'Créer un Type de Stage')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-graduation-cap text-primary me-2"></i>
                Créer un Type de Stage
            </h4>
            <small class="text-muted">Ajouter un nouveau type de stage</small>
        </div>
        <div>
            <a href="{{ route('rh.type-stages.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations du Type de Stage</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rh.type-stages.store') }}" method="POST">
                        @csrf

                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du type <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Ex: Stage en entreprise, PFE, Initiation, etc.</div>
                        </div>

                        <!-- Code -->
                        <div class="mb-3">
                            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" value="{{ old('code') }}" required
                                   pattern="[a-zA-Z0-9_-]+" title="Lettres, chiffres, underscore et tiret uniquement">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Code unique sans espaces (ex: entreprise, pfe, initiation)</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Description détaillée du type de stage (optionnel)</div>
                        </div>

                        <!-- Statut -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1" checked>
                                <label class="form-check-label" for="actif">
                                    Type actif
                                </label>
                            </div>
                            <div class="form-text">Un type actif peut être utilisé dans les offres de stage</div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rh.type-stages.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Créer le type
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
                        Les types de stage permettent de classer les offres selon leur nature académique ou professionnelle.
                    </p>
                    <hr>
                    <h6 class="text-primary">Conseils</h6>
                    <ul class="small text-muted">
                        <li>Le code doit être unique et sans espaces</li>
                        <li>Utilisez des noms clairs et descriptifs</li>
                        <li>Un type peut être archivé mais pas supprimé s'il est utilisé</li>
                    </ul>
                </div>
            </div>

            <!-- Exemples -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-1"></i>
                        Exemples courants
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Stage en entreprise</td>
                                    <td><code>entreprise</code></td>
                                </tr>
                                <tr>
                                    <td>Projet de fin d'études</td>
                                    <td><code>pfe</code></td>
                                </tr>
                                <tr>
                                    <td>Stage d'initiation</td>
                                    <td><code>initiation</code></td>
                                </tr>
                                <tr>
                                    <td>Stage de perfectionnement</td>
                                    <td><code>perfectionnement</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
                                <h4 class="text-primary mb-1">{{ \App\Models\TypeStage::actif()->count() }}</h4>
                                <small class="text-muted">Types actifs</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-secondary mb-1">{{ \App\Models\TypeStage::count() }}</h4>
                            <small class="text-muted">Total types</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
