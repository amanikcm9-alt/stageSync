@extends('layouts.public')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h1 class="fw-bold mb-0">
                        <i class="fas fa-building text-primary"></i> 
                        Entreprises Partenaires
                    </h1>
                </div>
                <div class="card-body bg-light">
                    <p class="text-muted mb-0">
                        Découvrez les entreprises qui proposent des stages de qualité. 
                        Chaque partenaire est sélectionné pour son engagement dans l'encadrement des stagiaires.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des entreprises -->
    <div class="row g-4">
        @forelse($entreprises as $entreprise)
            <div class="col-lg-4 col-md-6">
                <div class="entreprise-card h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <!-- Header avec logo -->
                        <div class="card-header bg-white border-0 text-center py-4">
                            @if($entreprise->logo_path)
                                <img src="{{ asset('storage/' . $entreprise->logo_path) }}" 
                                     alt="{{ $entreprise->nom }}" 
                                     class="entreprise-logo" 
                                     style="max-height: 80px; max-width: 150px; object-fit: contain;">
                            @else
                                <div class="entreprise-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                     style="width: 80px; height: 80px; font-size: 24px; font-weight: bold;">
                                    {{ strtoupper(substr($entreprise->nom, 0, 2)) }}
                                </div>
                            @endif
                        </div>

                        <div class="card-body">
                            <!-- Nom et secteur -->
                            <div class="text-center mb-3">
                                <h5 class="fw-bold mb-2">{{ $entreprise->nom }}</h5>
                                @if($entreprise->secteur_activite)
                                    <span class="badge bg-info">{{ $entreprise->secteur_activite }}</span>
                                @endif
                            </div>

                            <!-- Description -->
                            @if($entreprise->description)
                                <p class="card-text text-muted small mb-3" style="min-height: 60px;">
                                    {{ Str::limit(strip_tags($entreprise->description), 120) }}
                                </p>
                            @endif

                            <!-- Informations -->
                            <div class="row g-2 mb-3">
                                <div class="col-12">
                                    <div class="text-muted small">
                                        <i class="fas fa-map-marker-alt text-danger"></i> 
                                        {{ $entreprise->adresse_complete }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">
                                        <i class="fas fa-phone text-primary"></i> 
                                        {{ $entreprise->telephone }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">
                                        <i class="fas fa-briefcase text-success"></i> 
                                        {{ $entreprise->nombre_offres_actives }} offre{{ $entreprise->nombre_offres_actives > 1 ? 's' : '' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('entreprises.show', $entreprise) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> Voir Détails
                                </a>
                                <a href="{{ route('offres', ['entreprise' => $entreprise->id]) }}" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-briefcase"></i> Voir les Offres
                                </a>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="card-footer bg-light border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-check-circle text-success"></i> 
                                    Partenaire vérifié
                                </small>
                                @if($entreprise->site_web)
                                    <a href="{{ $entreprise->site_web_url }}" 
                                       target="_blank" 
                                       class="text-decoration-none text-muted small">
                                        <i class="fas fa-globe"></i> Site web
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h4 class="text-muted mb-3">Aucune entreprise partenaire</h4>
                <p class="text-muted">
                    Les entreprises partenaires apparaîtront ici dès qu'elles proposeront des offres.
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($entreprises->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $entreprises->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Styles personnalisés -->
<style>
.entreprise-card {
    transition: all 0.3s ease;
}

.entreprise-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.entreprise-card .card {
    border-radius: 15px;
    overflow: hidden;
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

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.card {
    border-radius: 15px;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

.pagination .page-link {
    border-radius: 8px;
    margin: 0 2px;
    border: none;
    color: #007bff;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

@media (max-width: 768px) {
    .entreprise-card {
        margin-bottom: 1rem;
    }
    
    .btn {
        font-size: 0.875rem;
    }
}
</style>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection
