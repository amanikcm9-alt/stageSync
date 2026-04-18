@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
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

    <!-- Formulaire d'affectation -->
    <div class="card border-0 shadow-sm">
        <div class="card-body py-3">
            <form method="POST" action="{{ route('rh.assignments.store') }}">
                @csrf
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="stagiaire_id" name="stagiaire_id" required>
                                <option value="">Sélectionner un stagiaire</option>
                                @foreach($stagiaires as $stagiaire)
                                    <option value="{{ $stagiaire->id }}">
                                        {{ $stagiaire->nom }} {{ $stagiaire->prenom }}
                                        ({{ $stagiaire->email }})
                                        @if($stagiaire->encadrant)
                                            - Déjà affecté à {{ $stagiaire->encadrant->nom }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <label for="stagiaire_id">Stagiaire</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="encadrant_id" name="encadrant_id" required>
                                <option value="">Sélectionner un encadrant</option>
                                @foreach($encadrants as $encadrant)
                                    <option value="{{ $encadrant->id }}">
                                        {{ $encadrant->nom }} {{ $encadrant->prenom }}
                                        ({{ $encadrant->email }})
                                    </option>
                                @endforeach
                            </select>
                            <label for="encadrant_id">Encadrant</label>
                        </div>
                    </div>
                </div>

                <!-- Informations -->
                <div class="alert alert-info py-2">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle me-2 mt-1"></i>
                        <div>
                            <strong>Information :</strong><br>
                            <small>
                                • Un stagiaire ne peut avoir qu'un seul encadrant à la fois<br>
                                • Vous pouvez modifier l'affectation existante à tout moment<br>
                                • Les stagiaires déjà affectés sont indiqués dans la liste
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rh.assignments.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer l'Affectation
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
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

.alert {
    border: none;
    border-radius: 0.5rem;
}

.alert-info {
    background-color: #e7f3ff;
    color: #004085;
}
</style>
@endsection
