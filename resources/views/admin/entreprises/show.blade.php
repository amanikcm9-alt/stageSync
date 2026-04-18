@extends('layouts.app')

@section('title', 'Détails Entreprise')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-building text-primary"></i> 
                {{ $entreprise->nom }}
            </h2>
            <small class="text-muted">{{ $entreprise->secteur }}</small>
        </div>
        <div>
            <a href="{{ route('admin.entreprises.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('admin.entreprises.edit', $entreprise) }}" class="btn btn-primary btn-sm">
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
                            <div class="me-3 rounded bg-primary d-flex align-items-center justify-content-center text-white fw-bold" 
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
                                        <a href="{{ $entreprise->site_web }}" target="_blank" class="text-primary">
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

                    @if($entreprise->description)
                        <div class="mt-3">
                            <h6 class="fs-6 fw-bold mb-2">Description</h6>
                            <p class="small text-muted mb-0">{{ $entreprise->description }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.entreprises.activer', $entreprise) }}">
                            @csrf
                            <button type="submit" class="btn btn-modern btn-success w-100">
                                <i class="fas fa-check me-2"></i>Activer
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.offres.create') }}?entreprise={{ $entreprise->id }}" class="btn btn-modern btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>Nouvelle Offre
                    </a>
                    
                    <form method="POST" action="{{ route('admin.entreprises.destroy', $entreprise) }}" onsubmit="return confirm('Supprimer cette entreprise ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-modern btn-outline-danger w-100">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    </form>
                </div>
            </div>

            
            <!-- Recent Offers -->
            <div class="content-card">
                <h5 class="mb-3">Offres Récentes</h5>
                @php
                    $recentOffers = $entreprise->offres()->latest()->take(3)->get();
                @endphp
                @if($recentOffers->count() > 0)
                    <div class="list-group">
                        @foreach($recentOffers as $offer)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $offer->titre }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $offer->created_at->format('d/m/Y') }}</small>
                                </div>
                                <span class="badge bg-{{ 
                                    $offer->statut === 'publie' ? 'success' : 
                                    ($offer->statut === 'brouillon' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($offer->statut) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.offres') }}?entreprise={{ $entreprise->id }}" class="btn btn-sm btn-outline-primary w-100">
                            Voir toutes les offres
                        </a>
                    </div>
                @else
                    <p class="text-muted">Aucune offre pour cette entreprise</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Règlement Interne -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <h6 class="fs-6 fw-bold mb-3">
                <i class="fas fa-file-contract me-2"></i>
                Règlement Interne
            </h6>
            
            @if($entreprise->reglement_interne)
                <div class="alert alert-info">
                    <div class="small">
                        <strong>Règlement interne disponible</strong><br>
                        <p>{{ Str::limit($entreprise->reglement_interne, 300) }}</p>
                        
                        @if($entreprise->partager_reglement_stagiaires)
                            <div class="mt-2">
                                <span class="badge bg-success small">
                                    <i class="fas fa-share-alt me-1"></i>
                                    Partagé avec les stagiaires
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-secondary">
                    <div class="small text-muted">
                        <i class="fas fa-info-circle me-2"></i>
                        Aucun règlement interne n'a été ajouté pour cette entreprise.
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Styles -->
<style>
.entreprise-avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content-center;
    font-weight: bold;
    font-size: 2rem;
}

.document-card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    transition: all 0.3s ease;
    height: 100%;
}

.document-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #007bff;
}

.document-icon {
    font-size: 2rem;
    color: #667eea;
    margin-bottom: 1rem;
    text-align: center;
}

.document-info h6 {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.document-info p {
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.table-borderless td {
    padding: 0.5rem 0;
    border: none;
}

.table-borderless td:first-child {
    font-weight: 600;
    color: #495057;
}

.regles-content {
    white-space: pre-wrap;
    font-family: inherit;
    line-height: 1.6;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #e9ecef;
    padding: 0.75rem 1rem;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>
@endsection
