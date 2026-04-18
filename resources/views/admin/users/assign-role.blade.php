@extends('layouts.app')

@section('title', 'Attribuer un Rôle')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-user-tag text-primary"></i> 
                Attribuer un Rôle
            </h2>
            <small class="text-muted">
                {{ $user->nom }} {{ $user->prenom }}
            </small>
        </div>
        <div>
            <div class="btn-group">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Formulaire d'attribution -->
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="mb-3 fs-6 fw-bold">Attribution de Rôle</h6>
                    
                    <!-- Informations utilisateur -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Nom complet</small>
                                <div class="fw-bold">{{ $user->nom }} {{ $user->prenom }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Email</small>
                                <div class="fw-bold">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <small class="text-muted">Rôle actuel</small>
                                <div>
                                    @switch($user->role->name)
                                        @case('admin')
                                            <span class="badge bg-danger">Admin</span>
                                            @break
                                        @case('rh')
                                            <span class="badge bg-primary">RH</span>
                                            @break
                                        @case('stagiaire')
                                            <span class="badge bg-success">Stagiaire</span>
                                            @break
                                        @case('encadrant')
                                            <span class="badge bg-info">Encadrant</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $user->role->name }}</span>
                                    @endswitch
                                </div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Statut</small>
                                <div>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire -->
                    <form action="{{ route('admin.users.update-role', $user) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="role_id" class="form-label">Nouveau rôle *</label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <option value="">Sélectionner un rôle</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Seuls les rôles RH et Admin peuvent être attribués par l'administrateur</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-modern btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-modern btn-primary">
                                <i class="fas fa-save me-2"></i>Attribuer le rôle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-body.p-4 {
    padding: 1.5rem !important;
}

.fs-6 {
    font-size: 0.875rem;
    font-weight: 600;
}
</style>
@endsection
