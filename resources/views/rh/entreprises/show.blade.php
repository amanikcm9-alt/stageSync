@extends('layouts.app')

@section('title', 'Détails Entreprise')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-building text-success"></i> 
                {{ $entreprise->nom }}
            </h2>
            <small class="text-muted">{{ $entreprise->secteur }}</small>
        </div>
        <div>
            <a href="{{ route('rh.entreprises.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('rh.entreprises.edit', $entreprise) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        @if($entreprise->logo_path)
                            <img src="{{ asset('storage/' . $entreprise->logo_path) }}" 
                                 alt="Logo {{ $entreprise->nom }}" 
                                 class="me-3 rounded" 
                                 style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="me-3 rounded bg-success d-flex align-items-center justify-content-center text-white fw-bold" 
                                 style="width: 60px; height: 60px;">
                                {{ strtoupper(substr($entreprise->nom, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-1 fs-5">{{ $entreprise->nom }}</h5>
                            <small class="text-muted">{{ $entreprise->secteur }}</small>
                            <div>
                                <span class="badge bg-{{ $entreprise->actif ? 'success' : 'secondary' }} small">
                                    {{ $entreprise->actif ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Informations détaillées -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="fs-6 fw-bold mb-2">Informations</h6>
                            <div class="small">
                                <div class="mb-2">
                                    <strong class="text-muted">Adresse:</strong><br>
                                    <span class="text-dark">{{ $entreprise->adresse ?: 'Non renseignée' }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-muted">Site web:</strong><br>
                                    @if($entreprise->site_web)
                                        <a href="{{ $entreprise->site_web }}" target="_blank" class="text-success">
                                            {{ $entreprise->site_web }}
                                        </a>
                                    @else
                                        <span class="text-dark">Non renseigné</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fs-6 fw-bold mb-2">Contact</h6>
                            <div class="small">
                                <div class="mb-2">
                                    <strong class="text-muted">Email:</strong><br>
                                    <span class="text-dark">{{ $entreprise->email ?: 'Non renseigné' }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-muted">Téléphone:</strong><br>
                                    <span class="text-dark">{{ $entreprise->telephone ?: 'Non renseigné' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body p-3">
                            <h6 class="fs-6 fw-bold mb-3">Actions</h6>
                            <div class="d-grid gap-2">
                                <a href="{{ route('rh.offres.index') }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-briefcase"></i> Voir les offres
                                </a>
                                    <i class="fas fa-list"></i> Liste des entreprises
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents et règlements -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <h6 class="fs-6 fw-bold mb-3">
                <i class="fas fa-file-contract me-2"></i>
                Documents & Règlements
            </h6>
            
            <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <div class="document-card">
                        <div class="document-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div class="document-info">
                            <h6 class="fs-6">Règlement Interne</h6>
                            <p class="text-muted small">Document officiel définissant les règles internes</p>
                            @if($entreprise->reglement_interne)
                                <a href="{{ asset('storage/' . $entreprise->reglement_interne) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            @else
                                <span class="badge bg-secondary small">Non disponible</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="document-card">
                        <div class="document-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="document-info">
                            <h6 class="fs-6">Charte Graphique</h6>
                            <p class="text-muted small">Charte définissant l'identité visuelle</p>
                            @if($entreprise->charte_graphique)
                                <a href="{{ asset('storage/' . $entreprise->charte_graphique) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            @else
                                <span class="badge bg-secondary small">Non disponible</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="document-card">
                        <div class="document-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="document-info">
                            <h6 class="fs-6">Procédures Sécurité</h6>
                            <p class="text-muted small">Procédures et consignes de sécurité</p>
                            @if($entreprise->procedures_securite)
                                <a href="{{ asset('storage/' . $entreprise->procedures_securite) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            @else
                                <span class="badge bg-secondary small">Non disponible</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="document-card">
                        <div class="document-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <div class="document-info">
                            <h6 class="fs-6">Guide Informatique</h6>
                            <p class="text-muted small">Guide d'utilisation des systèmes informatiques</p>
                            @if($entreprise->guide_informatique)
                                <a href="{{ asset('storage/' . $entreprise->guide_informatique) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            @else
                                <span class="badge bg-secondary small">Non disponible</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.fs-4 {
    font-size: 1.3rem;
    font-weight: 600;
}

.fs-5 {
    font-size: 1.1rem;
    font-weight: 600;
}

.fs-6 {
    font-size: 0.8rem;
    font-weight: 600;
}

.small {
    font-size: 0.7rem;
}

.btn-sm {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}

.card-body.p-3 {
    padding: 0.8rem !important;
}

.badge.small {
    font-size: 0.65rem;
    padding: 0.2rem 0.4rem;
}

.document-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 0.8rem;
    height: 100%;
    transition: transform 0.2s;
}

.document-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.document-icon {
    width: 35px;
    height: 35px;
    background: #f8f9fa;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.6rem;
    color: #6c757d;
}
</style>
@endsection
