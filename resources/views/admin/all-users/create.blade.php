@extends('layouts.app')

@section('content')
<div class="container">
    @php
        $roleName = request('role');
        $roleId = null;
        $roleTitle = 'un utilisateur';
        
        if ($roleName) {
            $role = \App\Models\Role::where('name', $roleName)->first();
            $roleId = $role ? $role->id : null;
            
            switch($roleName) {
                case 'stagiaire':
                    $roleTitle = 'un stagiaire';
                    break;
                case 'encadrant':
                    $roleTitle = 'un encadrant';
                    break;
                case 'rh':
                    $roleTitle = 'un RH';
                    break;
                case 'admin':
                    $roleTitle = 'un administrateur';
                    break;
            }
        }
    @endphp
    
    <h1>Ajouter {{ $roleTitle }}</h1>

    <form action="{{ route('admin.all-users.store') }}" method="POST" class="row g-3">
        @csrf
        
        <!-- Afficher les erreurs de validation -->
        @if($errors->any())
            <div class="col-12">
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        
        @if($roleId)
            <input type="hidden" name="role_id" value="{{ $roleId }}">
        @endif

        <div class="col-md-6">
            <label for="nom" class="form-label">Nom *</label>
            <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
        </div>

        <div class="col-md-6">
            <label for="prenom" class="form-label">Prénom *</label>
            <input type="text" name="prenom" id="prenom" class="form-control" value="{{ old('prenom') }}" required>
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label">Email *</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="col-md-6">
            <label for="password" class="form-label">Mot de passe *</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label for="role_id" class="form-label">Rôle *</label>
            <select name="role_id" id="role_id" class="form-select" @if($roleId) disabled @endif required>
                <option value="">Choisir un rôle</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ ($roleId && $role->id == $roleId) || old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            @if($roleId)
                <small class="form-text text-muted">Rôle prédéfini : {{ ucfirst($roleName) }}</small>
            @endif
        </div>

        <div class="col-md-6">
            <label for="active" class="form-label">Statut</label>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="active" id="active" value="1" {{ old('active') ? 'checked' : '' }}>
                <label class="form-check-label" for="active">
                    Compte actif
                </label>
            </div>
        </div>

        @if($roleName === 'stagiaire')
        <div class="col-md-6">
            <label for="encadrant_id" class="form-label">Encadrant</label>
            <select name="encadrant_id" id="encadrant_id" class="form-select">
                <option value="">Aucun</option>
                @foreach($encadrants as $enc)
                    <option value="{{ $enc->id }}" {{ old('encadrant_id') == $enc->id ? 'selected' : '' }}>
                        {{ $enc->nom }} {{ $enc->prenom }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Créer {{ $roleTitle }}
            </button>
            <a href="{{ route('admin.all-users.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
@endsection
