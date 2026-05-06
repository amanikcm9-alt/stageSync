@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-plus text-primary"></i> 
                Nouvelle Offre
            </h2>
            <small class="text-muted">
                Créer une nouvelle offre de stage
            </small>
        </div>
        <div>
            <a href="{{ route('admin.offres') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Formulaire compact -->
    <form method="POST" action="{{ route('admin.offres.store') }}">
        @csrf
        
        <!-- Informations principales -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="titre" name="titre" 
                                   placeholder="Titre de l'offre" required>
                            <label for="titre" class="form-label">Titre</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="type_stage" name="type_stage" required>
                                <option value="">Type</option>
                                @foreach($typesStage as $key => $type)
                                    <option value="{{ $key }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            <label for="type_stage" class="form-label">Type</label>
                        </div>
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="entreprise_id" name="entreprise_id" required>
                                <option value="">Entreprise</option>
                                @foreach($entreprises as $entreprise)
                                    <option value="{{ $entreprise->id }}">{{ $entreprise->nom }}</option>
                                @endforeach
                            </select>
                            <label for="entreprise_id" class="form-label">Entreprise</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="secteur" name="secteur" required>
                                <option value="">Secteur</option>
                                @foreach($secteurs as $key => $secteur)
                                    <option value="{{ $key }}">{{ $secteur }}</option>
                                @endforeach
                            </select>
                            <label for="secteur" class="form-label">Secteur</label>
                        </div>
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                            <label for="date_debut" class="form-label">Date début</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input type="number" class="form-control" id="duree" name="duree" 
                                   placeholder="Durée en semaines" min="1" required>
                            <label for="duree" class="form-label">Durée (sem)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="lieu" name="lieu" 
                                   placeholder="Lieu du stage" required>
                            <label for="lieu" class="form-label">Lieu</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description et missions -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3" placeholder="Description de l'offre" required></textarea>
                            <label for="description" class="form-label">Description</label>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="missions" name="missions" 
                                      rows="3" placeholder="Missions principales"></textarea>
                            <label for="missions" class="form-label">Missions</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rémunération et statut -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="remuneration" name="remuneration" 
                                   placeholder="Ex: 800€/mois">
                            <label for="remuneration" class="form-label">Rémunération</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="statut" name="statut" required>
                                <option value="brouillon">Brouillon</option>
                                <option value="publiee">Publiée</option>
                            </select>
                            <label for="statut" class="form-label">Statut</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1" checked>
                            <label class="form-check-label" for="active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.offres') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer l'Offre
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.form-floating {
    margin-bottom: 1rem;
}

.form-floating > .form-control {
    height: 58px;
}

.form-floating > .form-select {
    height: 58px;
}

.form-floating > label {
    padding: 1rem;
    font-size: 0.875rem;
}

.card-body.py-3 {
    padding: 1rem;
}
</style>
@endsection
