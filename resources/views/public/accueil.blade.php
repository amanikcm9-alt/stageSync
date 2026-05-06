@extends('layouts.public')

@section('content')
<div class="container-fluid">
    <!-- Hero Section Moderne -->
    <section class="hero-section bg-gradient-primary text-white py-5 mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="fs-2 fw-bold mb-4">
                        Trouvez Votre Stage de Rêve
                    </h1>
                    <p class="fs-5 mb-4">
                        Découvrez des opportunités de stage dans notre entreprise. 
                        Postulez en quelques clics et suivez votre candidature en temps réel.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('offres') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-search"></i> Explorer les Offres
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

    <!-- Notre Entreprise -->
    @if($entreprise)
    <section class="entreprise-section py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="fs-3 fw-bold mb-4">{{ $entreprise->nom }}</h2>
                    <p class="fs-6 mb-3">
                        {!! $entreprise->description ?? 'Découvrez notre entreprise et nos opportunités de stage.' !!}
                    </p>
                    @if($entreprise->adresse || $entreprise->telephone || $entreprise->email)
                    <div class="row g-3 mb-4">
                        @if($entreprise->adresse)
                        <div class="col-12">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Adresse</h6>
                                    <small class="text-muted">{{ $entreprise->adresse }}</small>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($entreprise->telephone)
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-phone text-success me-2"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Téléphone</h6>
                                    <small class="text-muted">{{ $entreprise->telephone }}</small>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($entreprise->email)
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-envelope text-info me-2"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Email</h6>
                                    <small class="text-muted">{{ $entreprise->email }}</small>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($entreprise->site_web)
                        <div class="col-12">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-globe text-warning me-2"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Site Web</h6>
                                    <small class="text-muted">
                                        <a href="{{ $entreprise->site_web }}" target="_blank" class="text-decoration-none">
                                            {{ $entreprise->site_web }}
                                        </a>
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($entreprise->description)
                    <p class="fs-6 mb-4">
                        Rejoignez-nous pour une expérience de stage enrichissante où vous pourrez développer vos compétences
                        et contribuer à nos projets innovants.
                    </p>
                    @endif
                </div>
                <div class="col-lg-6 text-center">
                    <div class="entreprise-image">
                        @if($entreprise->logo_path)
                            <img src="{{ asset('storage/' . $entreprise->logo_path) }}" 
                                 alt="{{ $entreprise->nom }}" 
                                 class="img-fluid"
                                 style="max-height: 200px; object-fit: contain;">
                        @else
                            <i class="fas fa-building display-1 text-primary opacity-75"></i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
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
