@extends('layouts.public')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb compact -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item">
                <a href="{{ route('accueil') }}" class="text-decoration-none">
                    <i class="fas fa-home"></i> Accueil
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('offres') }}" class="text-decoration-none">
                    Offres
                </a>
            </li>
            <li class="breadcrumb-item active">{{ Str::limit($offre->titre, 40) }}</li>
        </ol>
    </nav>

    <div class="row g-3">
        <!-- Détails de l'offre -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            @if($offre->entreprise?->logo_path)
                                <img src="{{ asset('storage/' . $offre->entreprise->logo_path) }}" 
                                     alt="{{ $offre->entreprise?->nom ?? 'Entreprise' }}" 
                                     class="me-3" 
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">
                            @else
                                <div class="me-3 bg-primary text-white d-flex align-items-center justify-content-center rounded" 
                                     style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                                    {{ strtoupper(substr($offre->entreprise?->nom ?? 'E', 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h5 class="mb-1 fs-5 fw-bold">{{ $offre->titre }}</h5>
                                <small class="text-muted">{{ $offre->entreprise?->nom ?? 'MW' }}</small>
                            </div>
                        </div>
                        <div>
                            @switch($offre->statut)
                                @case('publiee')
                                    <span class="badge bg-success small">Publiée</span>
                                    @break
                                @case('brouillon')
                                    <span class="badge bg-secondary small">Brouillon</span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <!-- Informations principales -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-calendar text-primary mb-2"></i>
                                <div class="small text-muted">Date début</div>
                                <div class="fw-bold small">{{ $offre->date_debut->format('d/m/Y') }}</div>
                            </div>
                        </div>
                                                <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-map-marker-alt text-warning mb-2"></i>
                                <div class="small text-muted">Lieu</div>
                                <div class="fw-bold small">{{ $offre->lieu }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-industry text-info mb-2"></i>
                                <div class="small text-muted">Secteur</div>
                                <div class="fw-bold small">
    @if($offre->secteur)
        {{ $offre->secteur->nom }}
    @elseif($offre->secteur_id)
        {{ \App\Models\Secteur::find($offre->secteur_id)?->nom ?? 'Non spécifié' }}
    @else
        {{ $offre->secteur ?? 'Non spécifié' }}
    @endif
</div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h6 class="fs-6 fw-bold mb-2">Description</h6>
                        <div class="small text-muted">
                            {!! nl2br(e($offre->description)) !!}
                        </div>
                    </div>

                    <!-- Missions -->
                    <div class="mb-4">
                        <h6 class="fs-6 fw-bold mb-2">Missions</h6>
                        <div class="small text-muted">
                            {!! nl2br(e($offre->missions)) !!}
                        </div>
                    </div>

                    
                    <!-- Informations complémentaires -->
                    @if($offre->remuneration || $offre->avantages)
                        <div class="mb-4">
                            <h6 class="fs-6 fw-bold mb-2">Informations complémentaires</h6>
                            <div class="row g-3">
                                @if($offre->remuneration)
                                    <div class="col-md-6">
                                        <div class="small">
                                            <strong>Rémunération :</strong><br>
                                            <span class="text-muted">{{ $offre->remuneration }}</span>
                                        </div>
                                    </div>
                                @endif
                                @if($offre->avantages)
                                    <div class="col-md-6">
                                        <div class="small">
                                            <strong>Avantages :</strong><br>
                                            <span class="text-muted">{{ $offre->avantages }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Bouton d'action -->
                    <div class="text-center">
                        <a href="{{ route('candidatures.create', $offre) }}" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-paper-plane"></i> Postuler à cette offre
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Carte entreprise -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fs-6 fw-bold mb-0">
                        <i class="fas fa-building"></i> Entreprise
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="text-center mb-3">
                        @if($offre->entreprise?->logo_path)
                            <img src="{{ asset('storage/' . $offre->entreprise->logo_path) }}" 
                                 alt="{{ $offre->entreprise?->nom ?? 'Entreprise' }}" 
                                 class="mb-2" 
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                        @else
                            <div class="bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2 rounded" 
                                 style="width: 80px; height: 80px; font-size: 28px; font-weight: bold;">
                                {{ strtoupper(substr($offre->entreprise?->nom ?? 'E', 0, 1)) }}
                            </div>
                        @endif
                        <h6 class="fw-bold small">{{ $offre->entreprise?->nom ?? 'MW' }}</h6>
                        @if($offre->entreprise?->secteur)
                            <small class="text-muted">{{ $offre->entreprise->secteur }}</small>
                        @endif
                    </div>
                    
                    @if($offre->entreprise?->description)
                        <div class="small text-muted">
                            {{ Str::limit(strip_tags($offre->entreprise->description), 150) }}
                        </div>
                    @endif
                    
                    @if($offre->entreprise?->site_web || $offre->entreprise?->email)
                        <div class="mt-3 pt-3 border-top">
                            @if($offre->entreprise?->site_web)
                                <div class="mb-2">
                                    <a href="{{ $offre->entreprise->site_web }}" 
                                       target="_blank" 
                                       class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-globe"></i> Site web
                                    </a>
                                </div>
                            @endif
                            @if($offre->entreprise->email)
                                <div>
                                    <a href="mailto:{{ $offre->entreprise->email }}" 
                                       class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="fas fa-envelope"></i> Contacter
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Carte infos pratiques -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fs-6 fw-bold mb-0">
                        <i class="fas fa-info-circle"></i> Infos pratiques
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="small">
                        <div class="mb-2">
                            <i class="fas fa-calendar text-primary"></i>
                            <strong>Publié le :</strong> {{ $offre->created_at->format('d/m/Y') }}
                        </div>
                        @if($offre->date_limite)
                            <div class="mb-2">
                                <i class="fas fa-clock text-warning"></i>
                                <strong>Date limite :</strong> {{ $offre->date_limite->format('d/m/Y') }}
                            </div>
                        @endif
                        <div>
                            <i class="fas fa-map-marker-alt text-success"></i>
                            <strong>Lieu :</strong> {{ $offre->lieu }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Autres offres similaires -->
    @if($autresOffres->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fs-6 fw-bold mb-0">
                            <i class="fas fa-briefcase"></i> Autres offres similaires
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            @foreach($autresOffres as $autreOffre)
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body p-3">
                                            <h6 class="fw-bold small mb-1">
                                                <a href="{{ route('offres.show', $autreOffre) }}" 
                                                   class="text-decoration-none">
                                                    {{ Str::limit($autreOffre->titre, 35) }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                {{ $autreOffre->entreprise?->nom ?? 'MW' }}<br>
                                                <i class="fas fa-map-marker-alt"></i> {{ $autreOffre->lieu }}
                                            </small>
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

<style>
.fs-5 {
    font-size: 1.25rem;
    font-weight: 600;
}

.fs-6 {
    font-size: 0.875rem;
    font-weight: 600;
}

.small {
    font-size: 0.75rem;
}

.btn-sm {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.badge.small {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
}

.card-body.p-3 {
    padding: 1rem !important;
}

.card-header.py-3 {
    padding: 0.75rem 1rem !important;
}

.breadcrumb.small {
    font-size: 0.75rem;
}
</style>
@endsection
