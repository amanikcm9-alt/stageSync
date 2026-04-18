@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1 fs-4">
                        <i class="fas fa-user-plus text-primary"></i> 
                        Ajouter RH/Admin
                    </h2>
                    <small class="text-muted">Créer un nouvel utilisateur RH ou Admin</small>
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
            <form action="{{ route('admin.users.store') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label for="nom" class="form-label">Nom *</label>
                    <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
                    @error('nom')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="prenom" class="form-label">Prénom *</label>
                    <input type="text" name="prenom" id="prenom" class="form-control" value="{{ old('prenom') }}" required>
                    @error('prenom')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" name="telephone" id="telephone" class="form-control" value="{{ old('telephone') }}">
                    @error('telephone')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label">Mot de passe *</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label for="role_id" class="form-label">Rôle *</label>
                    <select name="role_id" id="role_id" class="form-select" required>
                        <option value="">Sélectionner un rôle</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Créer l'utilisateur
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
