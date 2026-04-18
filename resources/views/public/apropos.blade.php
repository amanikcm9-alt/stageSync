@extends('layouts.public')

@section('title', 'À Propos - Notre Entreprise')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white text-center py-4">
                    <h1 class="fw-bold mb-0">
                        <i class="fas fa-building me-2"></i>À Propos de Notre Entreprise
                    </h1>
                    <p class="mb-0 mt-2">Découvrez qui nous sommes et ce que nous faisons</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="row g-4">
        <!-- Informations entreprise -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    @if($entreprise)
                        <!-- Logo et nom -->
                        <div class="text-center mb-4">
                            @if($entreprise->logo_path)
                                <img src="{{ asset('storage/' . $entreprise->logo_path) }}" 
                                     alt="{{ $entreprise->nom }}" 
                                     class="img-fluid mb-3" 
                                     style="max-height: 120px; max-width: 200px; object-fit: contain;">
                            @else
                                <div class="entreprise-placeholder bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 120px; height: 120px; font-size: 36px; font-weight: bold;">
                                    {{ strtoupper(substr($entreprise->nom, 0, 2)) }}
                                </div>
                            @endif
                            <h2 class="fw-bold text-primary">{{ $entreprise->nom }}</h2>
                            <p class="text-muted">{{ $entreprise->secteur_activite }}</p>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <h4 class="fw-bold mb-3">
                                <i class="fas fa-info-circle text-primary me-2"></i>Qui Sommes-Nous ?
                            </h4>
                            <p class="lead">{{ $entreprise->description ?? 'Nous sommes une entreprise innovante spécialisée dans le développement technologique et l\'accompagnement de jeunes talents.' }}</p>
                        </div>

                        <!-- Valeurs -->
                        <div class="mb-4">
                            <h4 class="fw-bold mb-3">
                                <i class="fas fa-heart text-primary me-2"></i>Nos Valeurs
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                        <div>
                                            <h6 class="fw-bold">Excellence</h6>
                                            <p class="text-muted small mb-0">Nous visons l'excellence dans tout ce que nous entreprenons.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-users text-success me-3 mt-1"></i>
                                        <div>
                                            <h6 class="fw-bold">Collaboration</h6>
                                            <p class="text-muted small mb-0">Nous croyons au pouvoir de la collaboration et du travail d'équipe.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-lightbulb text-success me-3 mt-1"></i>
                                        <div>
                                            <h6 class="fw-bold">Innovation</h6>
                                            <p class="text-muted small mb-0">Nous encourageons l'innovation et la créativité.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-graduation-cap text-success me-3 mt-1"></i>
                                        <div>
                                            <h6 class="fw-bold">Formation</h6>
                                            <p class="text-muted small mb-0">Nous investissons dans la formation et le développement des talents.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact -->
                        <div class="mb-4">
                            <h4 class="fw-bold mb-3">
                                <i class="fas fa-envelope text-primary me-2"></i>Contactez-Nous
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-danger me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">Adresse</h6>
                                            <p class="text-muted mb-0">{{ $entreprise->adresse ?? '123 Avenue de l\'Innovation, 75000 Paris' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone text-primary me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">Téléphone</h6>
                                            <p class="text-muted mb-0">{{ $entreprise->telephone ?? '+33 1 23 45 67 89' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope text-primary me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">Email</h6>
                                            <p class="text-muted mb-0">{{ $entreprise->email ?? 'contact@entreprise.fr' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-globe text-primary me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">Site Web</h6>
                                            <p class="text-muted mb-0">{{ $entreprise->site_web ?? 'www.entreprise.fr' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Informations de l'entreprise en cours de préparation</h4>
                            <p class="text-muted">Revenez bientôt pour découvrir notre entreprise !</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>Nos Chiffres Clés
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="stat-icon primary mx-auto mb-3">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stat-number">{{ $stats['total_offres'] }}</div>
                        <div class="stat-label">Offres de Stage</div>
                    </div>
                    
                    <div class="text-center mb-4">
                        <div class="stat-icon success mx-auto mb-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number">{{ $stats['total_candidatures'] }}</div>
                        <div class="stat-label">Candidatures Reçues</div>
                    </div>
                    
                    <div class="text-center mb-4">
                        <div class="stat-icon warning mx-auto mb-3">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-number">{{ $stats['total_stagiaires'] }}</div>
                        <div class="stat-label">Stagiaires Accueillis</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="stat-icon info mx-auto mb-3">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-number">{{ $stats['annee_creation'] }}</div>
                        <div class="stat-label">Année de Création</div>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-rocket text-primary me-2"></i>Rejoignez-Nous !
                    </h5>
                    <p class="text-muted mb-4">
                        Découvrez nos opportunités de stage et lancez votre carrière avec nous.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('offres') }}" class="btn btn-primary">
                            <i class="fas fa-briefcase me-2"></i>Voir nos Offres
                        </a>
                        <a href="{{ route('candidatures.create', 1) }}" class="btn btn-outline-primary">
                            <i class="fas fa-paper-plane me-2"></i>Postuler Maintenant
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.stat-icon.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-icon.success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.stat-icon.warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: white;
}

.stat-icon.info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
    color: white;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    line-height: 1;
}

.stat-label {
    color: #6c757d;
    font-weight: 500;
    margin-top: 0.5rem;
}

.entreprise-placeholder {
    transition: all 0.3s ease;
}

.entreprise-placeholder:hover {
    transform: scale(1.05);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
