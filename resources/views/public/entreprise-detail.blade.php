@extends('layouts.public')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('accueil') }}" class="text-decoration-none">
                    <i class="fas fa-home"></i> Accueil
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('entreprises.index') }}" class="text-decoration-none">
                    Entreprises
                </a>
            </li>
            <li class="breadcrumb-item active">{{ $entreprise->nom }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Détails de l'entreprise -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            @if($entreprise->logo_path)
                                <img src="{{ asset('storage/' . $entreprise->logo_path) }}" 
                                     alt="{{ $entreprise->nom }}" 
                                     class="entreprise-logo" 
                                     style="max-height: 80px; max-width: 150px; object-fit: contain;">
                            @else
                                <div class="entreprise-placeholder bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px; font-size: 24px; font-weight: bold;">
                                    {{ strtoupper(substr($entreprise->nom, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="fw-bold mb-1">{{ $entreprise->nom }}</h1>
                            @if($entreprise->secteur_activite)
                                <p class="text-muted mb-0">
                                    <i class="fas fa-briefcase"></i> {{ $entreprise->secteur_activite }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Description -->
                    @if($entreprise->description)
                        <div class="mb-4">
                            <h4 class="fw-bold mb-3">
                                <i class="fas fa-info-circle text-primary"></i> À Propos de l'Entreprise
                            </h4>
                            <div class="description-content">
                                {!! nl2br(e($entreprise->description)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Informations de contact -->
                    <div class="mb-4">
                        <h4 class="fw-bold mb-3">
                            <i class="fas fa-address-card text-success"></i> Informations de Contact
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    <strong>Adresse :</strong> {{ $entreprise->adresse_complete }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-phone text-primary"></i>
                                    <strong>Téléphone :</strong> {{ $entreprise->telephone }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-envelope text-warning"></i>
                                    <strong>Email :</strong> 
                                    @if($entreprise->email)
                                        <a href="mailto:{{ $entreprise->email }}" class="text-decoration-none">
                                            {{ $entreprise->email }}
                                        </a>
                                    @else
                                        Non spécifié
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-globe text-info"></i>
                                    <strong>Site web :</strong> 
                                    @if($entreprise->site_web)
                                        <a href="{{ $entreprise->site_web_url }}" target="_blank" class="text-decoration-none">
                                            {{ $entreprise->site_web }}
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @else
                                        Non spécifié
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conditions de stage -->
                    @if($entreprise->conditions_stage)
                        <div class="mb-4">
                            <h4 class="fw-bold mb-3">
                                <i class="fas fa-file-contract text-warning"></i> Conditions de Stage
                            </h4>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                {!! nl2br(e($entreprise->conditions_stage)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Carte d'actions rapides -->
            <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-rocket"></i> Actions Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-briefcase text-success fa-2x mb-2"></i>
                        <p class="text-muted small mb-0">
                            {{ $entreprise->nombre_offres_actives }} offre{{ $entreprise->nombre_offres_actives > 1 ? 's' : '' }} active{{ $entreprise->nombre_offres_actives > 1 ? 's' : '' }}
                        </p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('offres', ['entreprise' => $entreprise->id]) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les Offres
                        </a>
                        @if($entreprise->site_web)
                            <a href="{{ $entreprise->site_web_url }}" 
                               target="_blank" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-globe"></i> Site Web
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Offres de l'entreprise -->
    @if($entreprise->offres->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="fw-bold mb-0">
                            <i class="fas fa-briefcase text-primary"></i> 
                            Offres de Stage chez {{ $entreprise->nom }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @foreach($entreprise->offres as $offre)
                                <div class="col-lg-6">
                                    <div class="offre-card h-100">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body">
                                                <div class="mb-2">
                                                    <span class="badge bg-success">{{ $offre->duree_formatee }}</span>
                                                </div>
                                                
                                                <h6 class="fw-bold mb-2">
                                                    <a href="{{ route('offres.show', $offre) }}" class="text-decoration-none text-dark">
                                                        {{ $offre->titre }}
                                                    </a>
                                                </h6>
                                                
                                                <p class="card-text text-muted small mb-3">
                                                    {{ Str::limit(strip_tags($offre->description), 100) }}
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
                                                    <a href="{{ route('offres.show', $offre) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye"></i> Détails
                                                    </a>
                                                    <a href="{{ route('candidatures.create', $offre) }}" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-paper-plane"></i> Postuler
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Styles personnalisés -->
<style>
.breadcrumb {
    background-color: transparent;
    padding: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
    color: #6c757d;
}

.entreprise-logo {
    transition: transform 0.3s ease;
}

.entreprise-logo:hover {
    transform: scale(1.1);
}

.entreprise-placeholder {
    transition: all 0.3s ease;
}

.entreprise-placeholder:hover {
    transform: scale(1.1);
}

.info-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.description-content {
    line-height: 1.6;
    background-color: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    border-left: 4px solid #007bff;
}

.sticky-top {
    z-index: 100;
}

.offre-card {
    transition: all 0.3s ease;
}

.offre-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.alert {
    border-radius: 10px;
    border: none;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

@media (max-width: 768px) {
    .sticky-top {
        position: static;
    }
    
    .description-content {
        padding: 1rem;
    }
}
</style>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection
