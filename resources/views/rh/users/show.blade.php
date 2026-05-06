@extends('layouts.app')

@section('title', 'Détails Utilisateur')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-user text-success"></i> 
                {{ $user->nom }} {{ $user->prenom }}
            </h2>
            <small class="text-muted">
                {{ $user->role->name }}
            </small>
        </div>
        <div>
            <div class="btn-group">
                <a href="{{ route('rh.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <a href="{{ route('rh.users.edit', $user) }}" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            </div>
        </div>
    </div>

    <!-- Informations utilisateur -->
    <div class="row g-3">
        <!-- Carte principale -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="mb-3 fs-6 fw-bold">Informations personnelles</h6>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="small text-muted">Nom complet</label>
                                <div class="fw-bold">{{ $user->nom }} {{ $user->prenom }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="small text-muted">Email</label>
                                <div class="fw-bold">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="small text-muted">Téléphone</label>
                                <div class="fw-bold">{{ $user->telephone ?? 'Non renseigné' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="small text-muted">Rôle</label>
                                <div>
                                    @switch($user->role->name)
                                        @case('stagiaire')
                                            <span class="badge bg-primary">Stagiaire</span>
                                            @break
                                        @case('encadrant')
                                            <span class="badge bg-success">Encadrant</span>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="small text-muted">Statut</label>
                                <div>
                                    @if($user->active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="small text-muted">Date de création</label>
                                <div class="fw-bold">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte latérale -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="mb-3 fs-6 fw-bold">Actions rapides</h6>
                    
                    <div class="d-grid gap-2">
                        @if($user->id !== auth()->id())
                            @if($user->active)
                                <form action="{{ route('rh.users.deactivate', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning btn-sm w-100"
                                            onclick="return confirm('Désactiver {{ $user->nom }} {{ $user->prenom }} ?')">
                                        <i class="fas fa-user-slash"></i> Désactiver
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('rh.users.activate', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success btn-sm w-100"
                                            onclick="return confirm('Activer {{ $user->nom }} {{ $user->prenom }} ?')">
                                        <i class="fas fa-user-check"></i> Activer
                                    </button>
                                </form>
                            @endif
                        @endif
                        
                        @if($user->id !== auth()->id())
                            <form action="{{ route('rh.users.destroy', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                        onclick="return confirm('Supprimer {{ $user->nom }} {{ $user->prenom }} ?')">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informations spécifiques au rôle -->
            @if($user->role->name === 'stagiaire')
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body p-4">
                        <h6 class="mb-3 fs-6 fw-bold">Informations stagiaire</h6>
                        
                        <div class="mb-3">
                            <label class="small text-muted">Encadrant</label>
                            <div class="fw-bold">
                                @if($user->encadrant)
                                    {{ $user->encadrant->nom }} {{ $user->encadrant->prenom }}
                                @else
                                    <span class="text-muted">Non affecté</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="small text-muted">Première connexion</label>
                            <div class="fw-bold">
                                @if($user->first_connection_completed)
                                    <span class="badge bg-success">Complétée</span>
                                @else
                                    <span class="badge bg-warning">En attente</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($offre)
                            <div class="mb-3">
                                <label class="small text-muted">Offre de stage associée</label>
                                <div class="border rounded p-3 bg-light">
                                    <div class="fw-bold text-primary mb-2">
                                        <i class="fas fa-briefcase me-2"></i>{{ $offre->titre }}
                                    </div>
                                    <div class="small text-muted mb-2">
                                        <i class="fas fa-building me-1"></i>{{ $offre->entreprise->nom }}
                                    </div>
                                    @if($offre->description)
                                        <div class="small">
                                            {{ Str::limit($offre->description, 150) }}
                                        </div>
                                    @endif
                                    <div class="mt-2">
                                        <a href="{{ route('rh.offres.show', $offre) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Voir l'offre
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if($candidature)
                            <div class="mb-3">
                                <label class="small text-muted">Statut de la candidature</label>
                                <div class="fw-bold">
                                    @switch($candidature->statut)
                                        @case('accepte')
                                            <span class="badge bg-success">Acceptée</span>
                                            @break
                                        @case('refuse')
                                            <span class="badge bg-danger">Refusée</span>
                                            @break
                                        @case('en_attente')
                                            <span class="badge bg-warning">En attente</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $candidature->statut }}</span>
                                    @endswitch
                                </div>
                                @if($candidature->date_decision)
                                    <div class="small text-muted mt-1">
                                        Décision le : {{ $candidature->date_decision->format('d/m/Y') }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
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

.btn-sm {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}
</style>
@endsection
