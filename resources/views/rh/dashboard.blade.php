@extends('layouts.app')

@section('title', 'Tableau de Bord RH')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-tachometer-alt text-success"></i> 
                Dashboard RH
            </h2>
            <small class="text-muted">
                <i class="fas fa-user-tie"></i> {{ auth()->user()->nom }} {{ auth()->user()->prenom }} | 
                <i class="fas fa-clock"></i> {{ now()->format('d/m/Y H:i') }}
            </small>
        </div>
        <div>
            <div class="avatar-circle bg-success text-white d-flex align-items-center justify-content-center" 
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
                            <div class="stat-number-compact">{{ \App\Models\User::whereHas('role', function($query) { $query->where('name', 'stagiaire'); })->count() }}</div>
                            <div class="stat-label-compact text-muted">Stagiaires</div>
                        </div>
                        <div class="stat-icon-compact text-primary">
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
                        <div class="stat-icon-compact text-success">
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
                            <div class="stat-number-compact">{{ \App\Models\OffreStage::where('statut', 'publiee')->count() }}</div>
                            <div class="stat-label-compact text-muted">Offres en cours</div>
                        </div>
                        <div class="stat-icon-compact text-info">
                            <i class="fas fa-briefcase"></i>
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
                            <div class="stat-number-compact">{{ \App\Models\Candidature::where('statut', 'en_cours')->count() }}</div>
                            <div class="stat-label-compact text-muted">En Cours</div>
                        </div>
                        <div class="stat-icon-compact text-info">
                            <i class="fas fa-spinner"></i>
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
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card-compact">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number-compact">{{ \App\Models\Candidature::where('statut', 'refuse')->count() }}</div>
                            <div class="stat-label-compact text-muted">Refusées</div>
                        </div>
                        <div class="stat-icon-compact text-danger">
                            <i class="fas fa-times"></i>
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
                            <div class="stat-number-compact">{{ \App\Models\OffreStage::where('statut', 'affectée')->count() }}</div>
                            <div class="stat-label-compact text-muted">Offres affectées</div>
                        </div>
                        <div class="stat-icon-compact text-warning">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Évaluations -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="fas fa-clipboard-check text-primary"></i> 
                Évaluations
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Consulter les évaluations -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-list-alt fa-3x text-primary"></i>
                            </div>
                            <h6 class="card-title">Consulter les évaluations</h6>
                            <p class="card-text small text-muted">
                                Consultez toutes les évaluations des stagiaires et encadrants
                            </p>
                            <a href="#" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-2"></i>Voir toutes les évaluations
                            </a>
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
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.stat-card-compact {
    transition: all 0.3s ease;
}

.stat-card-compact:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-number-compact {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
}

.stat-label-compact {
    font-size: 0.75rem;
    font-weight: 500;
}

.stat-icon-compact {
    font-size: 1.2rem;
    opacity: 0.8;
}

/* Réduction de la taille des polices */
.container-fluid {
    font-size: 0.9rem;
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
</style>
@endsection
