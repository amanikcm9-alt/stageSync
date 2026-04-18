@extends('layouts.app')

@section('title', 'Tableau de Bord Administrateur')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-tachometer-alt text-primary"></i> 
                Dashboard Admin
            </h2>
            <small class="text-muted">
                <i class="fas fa-user-shield"></i> {{ auth()->user()->nom }} {{ auth()->user()->prenom }} | 
                <i class="fas fa-clock"></i> {{ now()->format('d/m/Y H:i') }}
            </small>
        </div>
        <div>
            <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                 style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques compactes -->
    <div class="row g-2 mb-3">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card-compact">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number-compact">{{ \App\Models\User::count() }}</div>
                            <div class="stat-label-compact text-muted">Utilisateurs</div>
                        </div>
                        <div class="stat-icon-compact text-primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card-compact">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number-compact">{{ \App\Models\User::whereHas('role', function($query) { $query->where('name', 'stagiaire'); })->count() }}</div>
                            <div class="stat-label-compact text-muted">Stagiaires</div>
                        </div>
                        <div class="stat-icon-compact text-success">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card-compact">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number-compact">{{ \App\Models\User::whereHas('role', function($query) { $query->where('name', 'encadrant'); })->count() }}</div>
                            <div class="stat-label-compact text-muted">Encadrants</div>
                        </div>
                        <div class="stat-icon-compact text-warning">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card-compact">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number-compact">{{ \App\Models\User::whereHas('role', function($query) { $query->where('name', 'rh'); })->count() }}</div>
                            <div class="stat-label-compact text-muted">RH</div>
                        </div>
                        <div class="stat-icon-compact text-info">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième ligne de statistiques -->
    <div class="row g-2 mb-3">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card-compact">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number-compact">{{ \App\Models\OffreStage::count() }}</div>
                            <div class="stat-label-compact text-muted">Offres</div>
                        </div>
                        <div class="stat-icon-compact text-primary">
                            <i class="fas fa-briefcase"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card-compact">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number-compact">{{ \App\Models\Candidature::count() }}</div>
                            <div class="stat-label-compact text-muted">Candidatures</div>
                        </div>
                        <div class="stat-icon-compact text-info">
                            <i class="fas fa-inbox"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card-compact">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number-compact">{{ \App\Models\Candidature::where('statut', 'recue')->count() }}</div>
                            <div class="stat-label-compact text-muted">Reçues</div>
                        </div>
                        <div class="stat-icon-compact text-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card-compact">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number-compact">{{ \App\Models\Candidature::where('statut', 'accepte')->count() }}</div>
                            <div class="stat-label-compact text-muted">Acceptées</div>
                        </div>
                        <div class="stat-icon-compact text-success">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row g-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="mb-3 fs-6">Actions Rapides</h6>
                    <div class="row g-2">
                        <div class="col-md-4 col-6">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-users me-1"></i> Gérer les Utilisateurs
                            </a>
                        </div>
                        <div class="col-md-4 col-6">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fas fa-cog me-1"></i> Paramètres
                            </a>
                        </div>
                        <div class="col-md-4 col-6">
                            <a href="{{ route('admin.entreprises.index') }}" class="btn btn-outline-success btn-sm w-100">
                                <i class="fas fa-building me-1"></i> Entreprises
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    border-radius: 50%;
    font-weight: bold;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.stat-card-compact {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card-compact:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-number-compact {
    font-size: 1.5rem;
    font-weight: 600;
    line-height: 1;
}

.stat-label-compact {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.25rem;
}

.stat-icon-compact {
    font-size: 1.25rem;
    opacity: 0.8;
}

.fs-6 {
    font-size: 0.875rem;
    font-weight: 600;
}

.card-body.p-3 {
    padding: 1rem !important;
}

.row.g-2 > * {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}

.btn-sm {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

/* Réduction de la taille des polices pour le dashboard admin */
.container-fluid {
    font-size: 0.9rem;
}

.card-header h5 {
    font-size: 0.95rem;
    font-weight: 600;
}

.card-body {
    font-size: 0.85rem;
}

.table th {
    font-size: 0.8rem;
    font-weight: 600;
}

.table td {
    font-size: 0.8rem;
}

h1 {
    font-size: 1.5rem;
}

h6 {
    font-size: 0.85rem;
}

small {
    font-size: 0.75rem;
}

.btn {
    font-size: 0.8rem;
}

.badge {
    font-size: 0.7rem;
}

/* Réduction supplémentaire pour les statistiques */
.stat-number-compact {
    font-size: 1.3rem;
    font-weight: 600;
    line-height: 1;
}

.stat-label-compact {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.25rem;
}

.stat-icon-compact {
    font-size: 1.1rem;
    opacity: 0.8;
}

.fs-4 {
    font-size: 1.25rem !important;
}

.fs-6 {
    font-size: 0.8rem !important;
    font-weight: 600;
}
</style>
@endsection
