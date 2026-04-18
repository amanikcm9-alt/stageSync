@extends('layouts.app')

@section('title', 'Détails Utilisateur')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1 fs-4">
                        <i class="fas fa-user text-primary"></i> 
                        {{ $user->nom }} {{ $user->prenom }}
                    </h2>
                    <small class="text-muted">Détails de l'utilisateur</small>
                </div>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm ms-2">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-info-circle text-primary me-2"></i>Informations personnelles
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Nom complet</label>
                            <div class="fw-bold">{{ $user->nom }} {{ $user->prenom }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Email</label>
                            <div class="fw-bold">{{ $user->email }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Téléphone</label>
                            <div class="fw-bold">{{ $user->telephone ?? 'Non renseigné' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Rôle</label>
                            <div class="fw-bold">
                                @switch($user->role->name)
                                    @case('admin')
                                        <span class="badge bg-danger">Admin</span>
                                        @break
                                    @case('rh')
                                        <span class="badge bg-primary">RH</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $user->role->name }}</span>
                                @endswitch
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Statut</label>
                            <div class="fw-bold">
                                @if($user->active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Date de création</label>
                            <div class="fw-bold">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Dernière mise à jour</label>
                            <div class="fw-bold">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-tools text-primary me-2"></i>Actions rapides
                    </h5>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Modifier
                        </a>
                        @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cet utilisateur ?')">
                                    <i class="fas fa-trash me-1"></i> Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations système -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-cog text-primary me-2"></i>Informations système
                    </h5>
                    
                    <div class="mb-3">
                        <label class="small text-muted">ID Utilisateur</label>
                        <div class="fw-bold">#{{ $user->id }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted">ID Rôle</label>
                        <div class="fw-bold">#{{ $user->role_id }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted">Email vérifié</label>
                        <div class="fw-bold">
                            @if($user->email_verified_at)
                                <span class="badge bg-success">Oui</span>
                            @else
                                <span class="badge bg-warning">Non</span>
                            @endif
                        </div>
                    </div>
                    @if($user->email_verified_at)
                        <div class="mb-3">
                            <label class="small text-muted">Date de vérification</label>
                            <div class="fw-bold">{{ $user->email_verified_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
