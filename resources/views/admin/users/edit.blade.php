@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1 fs-4">
                        <i class="fas fa-user-edit text-primary"></i> 
                        Modifier {{ $user->nom }} {{ $user->prenom }}
                    </h2>
                    <small class="text-muted">Modifier les informations de l'utilisateur</small>
                </div>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label for="nom" class="form-label">Nom *</label>
                    <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom', $user->nom) }}" required>
                    @error('nom')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="prenom" class="form-label">Prénom *</label>
                    <input type="text" name="prenom" id="prenom" class="form-control" value="{{ old('prenom', $user->prenom) }}" required>
                    @error('prenom')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" name="telephone" id="telephone" class="form-control" value="{{ old('telephone', $user->telephone) }}">
                    @error('telephone')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" name="password" id="password" class="form-control">
                    <small class="form-text text-muted">Laisser vide pour ne pas changer</small>
                    @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>

                <div class="col-md-6">
                    <label for="role_id" class="form-label">Rôle *</label>
                    <select name="role_id" id="role_id" class="form-select" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="hidden" name="active" value="0">
                        <input class="form-check-input" type="checkbox" name="active" 
                               id="active" value="1" {{ $user->active ? 'checked' : '' }}>
                        <label class="form-check-label" for="active">
                            Compte actif
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
