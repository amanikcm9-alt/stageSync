@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-user-check text-primary"></i> 
                Affecter Stagiaire
            </h2>
            <small class="text-muted">
                {{ $stagiaire->nom }} {{ $stagiaire->prenom }}
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
            <form method="POST" action="{{ route('rh.assignments.update', $stagiaire) }}">
                @csrf
                @method('PUT')
                
                <!-- Informations du stagiaire -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="alert alert-info py-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-graduate me-2"></i>
                                <div>
                                    <strong>Stagiaire :</strong> {{ $stagiaire->nom }} {{ $stagiaire->prenom }}<br>
                                    <small class="text-muted">Email : {{ $stagiaire->email }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Affectation actuelle -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="mb-3 fs-6">Affectation Actuelle</h6>
                        @if($stagiaire->encadrant)
                            <div class="alert alert-success py-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-check me-2"></i>
                                    <div>
                                        <strong>Encadrant actuel :</strong> {{ $stagiaire->encadrant->nom }} {{ $stagiaire->encadrant->prenom }}<br>
                                        <small class="text-muted">Email : {{ $stagiaire->encadrant->email }}</small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning py-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-times me-2"></i>
                                    <div>
                                        <strong>Non affecté</strong><br>
                                        <small class="text-muted">Ce stagiaire n'a pas encore d'encadrant assigné</small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Nouvelle affectation -->
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="mb-3 fs-6">{{ $stagiaire->encadrant ? 'Modifier l\'affectation' : 'Nouvelle affectation' }}</h6>
                        <div class="form-floating mb-3">
                            <select class="form-select" id="encadrant_id" name="encadrant_id" required>
                                <option value="">Sélectionner un encadrant</option>
                                @foreach($encadrants as $encadrant)
                                    <option value="{{ $encadrant->id }}" 
                                            {{ $stagiaire->encadrant_id == $encadrant->id ? 'selected' : '' }}>
                                        {{ $encadrant->nom }} {{ $encadrant->prenom }} 
                                        ({{ $encadrant->email }})
                                    </option>
                                @endforeach
                            </select>
                            <label for="encadrant_id">Encadrant</label>
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
                            <div>
                                @if($stagiaire->encadrant)
                                    <button type="submit" name="remove_assignment" value="1" 
                                            class="btn btn-warning me-2"
                                            onclick="return confirm('Retirer l\'affectation de ce stagiaire ?')">
                                        <i class="fas fa-user-times"></i> Retirer l'affectation
                                    </button>
                                @endif
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> 
                                    {{ $stagiaire->encadrant ? 'Mettre à jour' : 'Affecter' }}
                                </button>
                            </div>
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

.alert-success {
    background-color: #d1e7dd;
    color: #0f5132;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
}

.fs-6 {
    font-size: 0.875rem;
    font-weight: 600;
}
</style>
@endsection
