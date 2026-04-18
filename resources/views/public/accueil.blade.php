@extends('layouts.public')

@section('content')
<div class="container-fluid">
    <!-- Hero Section Moderne -->
    <section class="hero-section bg-gradient-primary text-white py-5 mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        Trouvez Votre Stage de Rêve
                    </h1>
                    <p class="lead mb-4">
                        Découvrez des opportunités de stage dans les meilleures entreprises. 
                        Postulez en quelques clics et suivez votre candidature en temps réel.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('offres') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-search"></i> Explorer les Offres
                        </a>
                        <a href="{{ route('entreprises.index') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-building"></i> Entreprises Partenaires
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image">
                        <i class="fas fa-briefcase display-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistiques -->
    <section class="stats-section py-4 mb-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <div class="stat-card text-center">
                        <div class="stat-icon bg-primary text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-briefcase fa-2x"></i>
                        </div>
                        <h3 class="fw-bold text-primary">{{ $stats['total_offres'] }}</h3>
                        <p class="text-muted mb-0">Offres Actives</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card text-center">
                        <div class="stat-icon bg-success text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                        <h3 class="fw-bold text-success">{{ $stats['total_entreprises'] }}</h3>
                        <p class="text-muted mb-0">Entreprises Partenaires</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card text-center">
                        <div class="stat-icon bg-warning text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h3 class="fw-bold text-warning">{{ $stats['total_candidatures'] }}</h3>
                        <p class="text-muted mb-0">Candidatures</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card text-center">
                        <div class="stat-icon bg-info text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h3 class="fw-bold text-info">{{ count($stats['secteurs']) }}</h3>
                        <p class="text-muted mb-0">Secteurs d'Activité</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dernières Offres -->
    <section class="latest-offers-section py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="fw-bold">
                            <i class="fas fa-star text-warning"></i> 
                            Dernières Offres Publiées
                        </h2>
                        <a href="{{ route('offres') }}" class="btn btn-outline-primary">
                            Voir Toutes <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                @forelse($dernieresOffres as $offre)
                    <div class="col-lg-4 col-md-6">
                        <div class="offre-card h-100">
                            <div class="card border-0 shadow-sm h-100">
                                @if($offre->entreprise->logo_path)
                                    <div class="card-header bg-white border-0 text-center py-3">
                                        <img src="{{ asset('storage/' . $offre->entreprise->logo_path) }}" 
                                             alt="{{ $offre->entreprise->nom }}" 
                                             class="entreprise-logo" 
                                             style="max-height: 60px; max-width: 120px; object-fit: contain;">
                                    </div>
                                @endif
                                
                                <div class="card-body">
                                    <div class="mb-2">
                                        <span class="badge bg-primary">{{ $offre->entreprise->nom }}</span>
                                        <span class="badge bg-success float-end">{{ $offre->duree_formatee }}</span>
                                    </div>
                                    
                                    <h5 class="card-title fw-bold mb-3">
                                        <a href="{{ route('offres.show', $offre) }}" class="text-decoration-none text-dark">
                                            {{ $offre->titre }}
                                        </a>
                                    </h5>
                                    
                                    <p class="card-text text-muted small mb-3">
                                        {{ Str::limit($offre->description, 100) }}
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="text-muted small">
                                            <i class="fas fa-map-marker-alt"></i> {{ $offre->lieu }}
                                        </div>
                                        <div class="fw-bold text-success">
                                            {{ $offre->remuneration_formatee }}
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('offres.show', $offre) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> Détails
                                        </a>
                                        <a href="{{ route('candidatures.create', $offre) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-paper-plane"></i> Postuler
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Aucune offre disponible pour le moment</h4>
                        <p class="text-muted">Revenez bientôt pour découvrir de nouvelles opportunités !</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Secteurs Populaires -->
    <section class="secteurs-section py-5 bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="fw-bold text-center mb-2">
                        <i class="fas fa-th-large text-primary"></i> 
                        Choisissez Votre Secteur d'Activité
                    </h2>
                    <p class="text-center text-muted mb-4">
                        Cliquez sur votre secteur pour voir toutes les offres disponibles
                    </p>
                </div>
            </div>

            <div class="row g-3">
                @foreach(array_slice($stats['secteurs'], 0, 12) as $key => $secteur)
                    <div class="col-lg-3 col-md-4 col-6">
                        <a href="{{ route('offres', ['secteur' => $key]) }}" 
                           class="secteur-card text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 text-center p-4">
                                <div class="secteur-icon mb-3">
                                    <i class="fas fa-{{ $key === 'banque' ? 'university' : ($key === 'full-stack' ? 'code' : ($key === 'digital-marketing' ? 'bullhorn' : ($key === 'data-science' ? 'brain' : ($key === 'design' ? 'palette' : 'briefcase')))) }} fa-3x text-primary"></i>
                                </div>
                                <h6 class="fw-bold mb-2">{{ $secteur }}</h6>
                                <div class="text-muted small">
                                    <i class="fas fa-briefcase"></i> 
                                    {{ $stats['offres_par_secteur'][$key] ?? 0 }} offre{{ ($stats['offres_par_secteur'][$key] ?? 0) > 1 ? 's' : '' }}
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Bouton voir tous les secteurs -->
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <a href="{{ route('offres') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-th"></i> Voir Tous les Secteurs et Offres
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section py-5 bg-gradient-primary text-white">
        <div class="container text-center">
            <h2 class="fw-bold mb-3">Prêt à Commencer Votre Carrière ?</h2>
            <p class="lead mb-4">
                Rejoignez des milliers d'étudiants qui ont trouvé leur stage idéal avec notre plateforme.
            </p>
            <a href="{{ route('offres') }}" class="btn btn-light btn-lg">
                <i class="fas fa-rocket"></i> Commencer Ma Recherche
            </a>
        </div>
    </section>
</div>

<!-- Styles personnalisés -->
<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 0 0 50px 50px / 20px;
}

.stat-card {
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.offre-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.offre-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.secteur-card {
    transition: all 0.3s ease;
    color: inherit;
}

.secteur-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    color: inherit;
}

.secteur-card .card {
    transition: all 0.3s ease;
}

.secteur-card:hover .card {
    background-color: #f8f9fa;
    border-color: #007bff;
}

.entreprise-logo {
    transition: transform 0.3s ease;
}

.entreprise-logo:hover {
    transform: scale(1.1);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.cta-section {
    border-radius: 50px 50px 0 0 / 20px;
}

@media (max-width: 768px) {
    .hero-section {
        border-radius: 0;
        text-align: center;
    }
    
    .display-4 {
        font-size: 2rem;
    }
}
</style>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection
