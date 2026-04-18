@extends('layouts.app')

@section('title', 'Affecter Stagiaire')

@section('content')
<div class="container-fluid py-4">
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
                                    <small class="text-muted">{{ $stagiaire->email }}</small>
                                    @if($stagiaire->offreStage)
                                        <br><small class="text-muted">Offre : {{ $stagiaire->offreStage->titre }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="encadrant_id" name="encadrant_id">
                                <option value="">Sélectionner un encadrant</option>
                                @foreach($encadrants as $encadrant)
                                    <option value="{{ $encadrant->id }}" 
                                            {{ $stagiaire->encadrant_id == $encadrant->id ? 'selected' : '' }}>
                                        {{ $encadrant->nom }} {{ $encadrant->prenom }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="encadrant_id">Encadrant</label>
                        </div>
                    </div>
                </div>

                <!-- Informations importantes -->
                <div class="alert alert-warning py-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <small>
                                <strong>Attention :</strong>
                                <ul class="mb-0 small">
                                    <li>Si vous ne sélectionnez pas d'encadrant, le stagiaire ne sera plus affecté</li>
                                    <li>Un stagiaire ne peut avoir qu'un seul encadrant à la fois</li>
                                </ul>
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

.alert-info {
    background-color: #e7f5ff;
    border-color: #bde0fe;
}

.alert-warning {
    background-color: #fff3cd;
    border-color: #ffeaa7;
}
</style>
@endsection
