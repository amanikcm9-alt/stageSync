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
            <a href="{{ route('rh.offres') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('rh.offres.edit', $offre) }}" class="btn btn-warning">
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
                                <span class="badge bg-info">{{ \App\Models\Secteur::find($offre->secteur_id)?->nom ?? 'Non spécifié' }}</span>
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
                        <span class="badge bg-{{ $offre->statut === 'publiee' ? 'success' : ($offre->statut === 'brouillon' ? 'warning' : ($offre->statut === 'affectee' ? 'info' : 'danger')) }}">
                            {{ $offre->statut === 'publiee' ? '✅ Publiée' : ($offre->statut === 'brouillon' ? '📝 Brouillon' : ($offre->statut === 'affectee' ? '🎯 Affectée' : '❌ Clôturée')) }}
                        </span>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="text-center">
                        @if($offre->entreprise?->logo_path)
                            <img src="{{ asset('storage/' . $offre->entreprise?->logo_path) }}" 
                                 alt="{{ $offre->entreprise?->nom }}" 
                                 class="img-fluid mb-3" 
                                 style="max-height: 100px; object-fit: contain;">
                        @endif
                        <h5 class="fw-bold">{{ $offre->entreprise?->nom ?? 'Entreprise non spécifiée' }}</h5>
                        <p class="text-muted">{{ $offre->entreprise?->secteur_activite ?? 'Secteur non spécifié' }}</p>
                        <p class="text-muted small">
                            <i class="fas fa-map-marker-alt"></i> {{ $offre->entreprise?->adresse ?? 'Adresse non spécifiée' }}
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
                        <a href="{{ route('rh.offres.edit', $offre) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        @if($offre->statut === 'brouillon')
                            <form method="POST" action="{{ route('rh.offres.publier', $offre) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Publier
                                </button>
                            </form>
                        @elseif($offre->statut === 'publiee')
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cloturerOffreModal">
                                <i class="fas fa-times"></i> Clôturer
                            </button>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Candidatures</h6>
                    <div class="d-flex gap-2 mb-3">
                        @if($candidaturesCount > 0)
                            <a href="{{ route('rh.candidatures.index', ['offre_id' => $offre->id]) }}" class="btn btn-primary">
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

/* Styles pour réduire la taille des polices */
.container-fluid {
    font-size: 0.9rem;
}

.card-header h5 {
    font-size: 1rem;
    font-weight: 600;
}

.card-body {
    font-size: 0.85rem;
}

h1 {
    font-size: 1.5rem !important;
    font-weight: 600;
}

h3 {
    font-size: 1.2rem !important;
    font-weight: 600;
}

.info-item {
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
}

.info-item strong {
    font-size: 0.85rem;
    font-weight: 600;
}

.badge {
    font-size: 0.65rem;
    padding: 0.25em 0.5em;
}

.btn {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

.text-muted {
    font-size: 0.8rem;
}

.list-group-item {
    font-size: 0.85rem;
    padding: 0.75rem 1rem;
}

.table {
    font-size: 0.85rem;
}

.table th {
    font-size: 0.8rem;
    font-weight: 600;
}

.table td {
    font-size: 0.8rem;
}

.stat-box {
    padding: 1rem;
    border-radius: 8px;
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
    font-size: 0.85rem;
}

.stat-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-box h6 {
    font-size: 0.85rem;
    font-weight: 600;
}

.stat-box .stat-number {
    font-size: 1.1rem;
    font-weight: 700;
}

.description-box {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #007bff;
    font-size: 0.85rem;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .container-fluid {
        font-size: 0.85rem;
    }
    
    .btn {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
    }
    
    h1 {
        font-size: 1.3rem !important;
    }
    
    h3 {
        font-size: 1.1rem !important;
    }
}
</style>

<!-- Modal de clôture d'offre -->
<div class="modal fade" id="cloturerOffreModal" tabindex="-1" aria-labelledby="cloturerOffreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cloturerOffreModalLabel">
                    <i class="fas fa-times-circle text-danger"></i> Clôturer l'offre
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('rh.offres.cloturer', $offre) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Attention :</strong> Vous êtes sur le point de clôturer cette offre. Plus aucune candidature ne pourra être soumise.
                    </div>
                    
                    <div class="mb-3">
                        <label for="notification_cloture" class="form-label">
                            <i class="fas fa-bell"></i> Notification de clôture *
                        </label>
                        <textarea class="form-control" 
                                  id="notification_cloture" 
                                  name="notification_cloture" 
                                  rows="4" 
                                  placeholder="Précisez la raison de la clôture de cette offre..."
                                  required></textarea>
                        <small class="text-muted">
                            Cette notification sera visible par les candidats et les utilisateurs concernés.
                        </small>
                    </div>
                    
                                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-check"></i> Confirmer la clôture
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
