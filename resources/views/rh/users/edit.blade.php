@extends('layouts.app')

@section('title', 'Modifier Utilisateur')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-edit text-success"></i> 
                Modifier {{ $user->nom }} {{ $user->prenom }}
            </h2>
            <small class="text-muted">
                {{ $user->role->name }}
            </small>
        </div>
        <div>
            <a href="{{ route('rh.users.show', $user) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Formulaire de modification -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('rh.users.update', $user->id) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label for="nom" class="form-label small text-muted">Nom *</label>
                    <input type="text" name="nom" id="nom" class="form-control form-control-sm" 
                           value="{{ old('nom', $user->nom) }}" required>
                </div>

                <div class="col-md-6">
                    <label for="prenom" class="form-label small text-muted">Prénom *</label>
                    <input type="text" name="prenom" id="prenom" class="form-control form-control-sm" 
                           value="{{ old('prenom', $user->prenom) }}" required>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label small text-muted">Email *</label>
                    <input type="email" name="email" id="email" class="form-control form-control-sm" 
                           value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="col-md-6">
                    <label for="telephone" class="form-label small text-muted">Téléphone</label>
                    <input type="tel" name="telephone" id="telephone" class="form-control form-control-sm" 
                           value="{{ old('telephone', $user->telephone) }}">
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label small text-muted">Mot de passe</label>
                    <input type="password" name="password" id="password" class="form-control form-control-sm">
                    <small class="text-muted">Laisser vide pour ne pas changer</small>
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label small text-muted">Confirmer mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           class="form-control form-control-sm">
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="form-check">
                                <input type="hidden" name="active" value="0">
                                <input class="form-check-input" type="checkbox" name="active" 
                                       id="active" value="1" {{ $user->active ? 'checked' : '' }}>
                                <label class="form-check-label small" for="active">
                                    Compte actif
                                </label>
                            </div>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('rh.users.show', $user) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card-body.p-4 {
    padding: 1.5rem !important;
}

.fs-4 {
    font-size: 1.5rem;
    font-weight: 600;
}

.form-control-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.btn-sm {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.small.text-muted {
    font-size: 0.75rem;
    font-weight: 500;
}
</style>
@endsection
