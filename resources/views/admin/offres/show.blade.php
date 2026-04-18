@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">
                <i class="fas fa-eye text-primary"></i> 
                Détails de l'Offre
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-clock"></i> 
                {{ $offre->created_at->format('d/m/Y H:i') }} | 
                Créée par {{ $offre->rh->nom }} {{ $offre->rh->prenom }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.offres') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('admin.offres.edit', $offre) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-info-circle"></i> Informations de l'Offre
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-3">{{ $offre->titre }}</h3>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Secteur :</strong> 
                                <span class="badge bg-info">{{ $secteurs[$offre->secteur] ?? $offre->secteur }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Lieu :</strong> 
                                <i class="fas fa-map-marker-alt text-danger"></i> {{ $offre->lieu }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Durée :</strong> 
                                <span class="badge bg-primary">{{ $offre->duree_formatee }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Rémunération :</strong> 
                                <span class="fw-bold text-success">{{ $offre->remuneration_formatee }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Date de début :</strong> 
                                {{ $offre->date_debut ? $offre->date_debut->format('d/m/Y') : 'Immédiat' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Date de fin :</strong> 
                                {{ $offre->date_fin ? $offre->date_fin->format('d/m/Y') : 'Non définie' }}
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <strong>Statut :</strong> 
                        <span class="badge bg-{{ $offre->statut === 'publiee' ? 'success' : ($offre->statut === 'brouillon' ? 'warning' : 'danger') }}">
                            {{ $offre->statut === 'publiee' ? '✅ Publiée' : ($offre->statut === 'brouillon' ? '📝 Brouillon' : '❌ Clôturée') }}
                        </span>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="text-center">
                        @if($offre->entreprise->logo_path)
                            <img src="{{ asset('storage/' . $offre->entreprise->logo_path) }}" 
                                 alt="{{ $offre->entreprise->nom }}" 
                                 class="img-fluid mb-3" 
                                 style="max-height: 100px; object-fit: contain;">
                        @endif
                        <h5 class="fw-bold">{{ $offre->entreprise->nom }}</h5>
                        <p class="text-muted">{{ $offre->entreprise->secteur_activite }}</p>
                        <p class="text-muted small">
                            <i class="fas fa-map-marker-alt"></i> {{ $offre->entreprise->adresse_complete }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Description et missions -->
    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt"></i> Description
                    </h5>
                </div>
                <div class="card-body">
                    <div class="description-content">
                        {!! nl2br(e($offre->description)) !!}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks"></i> Missions Principales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="missions-content">
                        {!! nl2br(e($offre->missions)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des candidatures -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-chart-bar"></i> Statistiques des Candidatures
            </h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="stat-box">
                        <h3 class="text-primary">{{ $candidaturesCount }}</h3>
                        <p class="text-muted mb-0">Total candidatures</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <h3 class="text-warning">{{ $offre->candidatures()->where('statut', 'recue')->count() }}</h3>
                        <p class="text-muted mb-0">En attente</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <h3 class="text-info">{{ $offre->candidatures()->where('statut', 'en_cours')->count() }}</h3>
                        <p class="text-muted mb-0">En cours</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <h3 class="text-success">{{ $offre->candidatures()->where('statut', 'accepte')->count() }}</h3>
                        <p class="text-muted mb-0">Acceptées</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-cog"></i> Actions Rapides
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Gestion de l'offre</h6>
                    <div class="d-flex gap-2 mb-3">
                        <a href="{{ route('admin.offres.edit', $offre) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        @if($offre->statut === 'brouillon')
                            <form method="POST" action="{{ route('admin.offres.publier', $offre) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Publier
                                </button>
                            </form>
                        @elseif($offre->statut === 'publiee')
                            <form method="POST" action="{{ route('admin.offres.cloturer', $offre) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Clôturer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Candidatures</h6>
                    <div class="d-flex gap-2 mb-3">
                        @if($candidaturesCount > 0)
                            <a href="{{ route('admin.candidatures.index', ['offre_id' => $offre->id]) }}" class="btn btn-primary">
                                <i class="fas fa-list"></i> Voir les candidatures
                            </a>
                        @else
                            <span class="text-muted">Aucune candidature pour le moment</span>
                        @endif
                        <a href="{{ route('offres.show', $offre) }}" target="_blank" class="btn btn-outline-secondary">
                            <i class="fas fa-eye"></i> Voir version publique
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles professionnels -->
<style>
.card {
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 10px 10px 0 0 !important;
}

.info-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.description-content, .missions-content {
    line-height: 1.6;
    background-color: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.stat-box {
    padding: 1.5rem;
    border-radius: 8px;
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.stat-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.btn {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.text-muted {
    color: #6c757d !important;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

@media (max-width: 768px) {
    .stat-box {
        margin-bottom: 1rem;
    }
    
    .btn {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }
}
</style>
@endsection
