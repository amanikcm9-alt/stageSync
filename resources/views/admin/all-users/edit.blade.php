@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modifier {{ ucfirst($user->role->name) }}</h1>

    <form action="{{ route('admin.all-users.update', $user->id) }}" method="POST" class="row g-3">
        @csrf
        @method('PUT')

        <div class="col-md-6">
            <label for="nom" class="form-label">Nom *</label>
            <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom', $user->nom) }}" required>
        </div>

        <div class="col-md-6">
            <label for="prenom" class="form-label">Prénom *</label>
            <input type="text" name="prenom" id="prenom" class="form-control" value="{{ old('prenom', $user->prenom) }}" required>
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label">Email *</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="col-md-6">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control">
            <small class="form-text text-muted">Laisser vide pour ne pas changer</small>
        </div>

        <div class="col-md-6">
            <label for="role_id" class="form-label">Rôle *</label>
            <select name="role_id" id="role_id" class="form-select" required>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="active" class="form-label">Statut</label>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="active" id="active" {{ $user->email_verified_at ? 'checked' : '' }}>
                <label class="form-check-label" for="active">
                    Compte actif
                </label>
            </div>
        </div>

        @if($user->role->name === 'stagiaire')
        <div class="col-md-6">
            <label for="encadrant_id" class="form-label">Encadrant</label>
            <select name="encadrant_id" id="encadrant_id" class="form-select">
                <option value="">Aucun</option>
                @foreach($encadrants as $enc)
                    <option value="{{ $enc->id }}" {{ $user->encadrant_id == $enc->id ? 'selected' : '' }}>
                        {{ $enc->nom }} {{ $enc->prenom }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Mettre à jour
            </button>
            <a href="{{ route('admin.all-users.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
@endsection
