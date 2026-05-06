@extends('layouts.public')

@section('content')
<!--
 * VUE REDONDANTE - NON NÉCESSAIRE
 * Raison : Peut être fusionnée avec rh/offres/index.blade.php avec conditions
 * Alternative : Utiliser rh/offres/index.blade.php avec permissions
 * Date de mise en commentaire : 16/04/2026
 -->
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h2 class="mb-1 fs-4">
                        <i class="fas fa-search text-primary"></i> 
                        Offres de Stage
                    </h2>
                    <small class="text-muted">
                        {{ $offres->total() }} offre{{ $offres->total() > 1 ? 's' : '' }} disponible{{ $offres->total() > 1 ? 's' : '' }}
                    </small>
                </div>
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('offres') }}" class="row g-2">
                        <!-- Recherche -->
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-sm">
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="search" 
                                       name="search" 
                                       placeholder="Rechercher..."
                                       value="{{ request('search') }}">
                                <label for="search" class="small">
                                    <i class="fas fa-search"></i> Mots-clés
                                </label>
                            </div>
                        </div>
                        <!-- Secteur -->
                        <div class="col-lg-2">
                            <div class="form-floating form-floating-sm">
                                <select class="form-select form-select-sm" id="secteur" name="secteur">
                                    <option value="">Secteur</option>
                                    @foreach($secteurs as $key => $secteur)
                                        <option value="{{ $key }}" {{ request('secteur') == $key ? 'selected' : '' }}>
                                            {{ $secteur }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="secteur" class="small">
                                    <i class="fas fa-industry"></i> Secteur
                                </label>
                            </div>
                        </div>
                        <!-- Type -->
                        <div class="col-lg-2">
                            <div class="form-floating form-floating-sm">
                                <select class="form-select form-select-sm" id="type" name="type">
                                    <option value="">Type</option>
                                    <option value="stage_ete" {{ request('type') == 'stage_ete' ? 'selected' : '' }}>Stage d'été</option>
                                    <option value="stage_automne" {{ request('type') == 'stage_automne' ? 'selected' : '' }}>Stage d'automne</option>
                                    <option value="stage_hiver" {{ request('type') == 'stage_hiver' ? 'selected' : '' }}>Stage d'hiver</option>
                                    <option value="stage_printemps" {{ request('type') == 'stage_printemps' ? 'selected' : '' }}>Stage de printemps</option>
                                    <option value="stage_pfe" {{ request('type') == 'stage_pfe' ? 'selected' : '' }}>Stage PFE</option>
                                </select>
                                <label for="type" class="small">
                                    <i class="fas fa-tag"></i> Type
                                </label>
                            </div>
                        </div>
                        <!-- Boutons -->
                        <div class="col-lg-4">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                                <a href="{{ route('offres') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times"></i> Effacer
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des offres -->
    <div class="row g-3">
        @forelse($offres as $offre)
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <!-- Header de l'offre -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">{{ Str::limit($offre->titre, 45) }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-building"></i> {{ $offre->entreprise?->nom ?? 'MW' }}
                                </small>
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

                        <!-- Description -->
                        <p class="small text-muted mb-3">
                            {{ Str::limit(strip_tags($offre->description), 120) }}
                        </p>

                        <!-- Détails -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> 
                                    {{ $offre->date_debut->format('d/m/Y') }}
                                </small>
                            </div>
                                                        <div class="col-6">
                                <small class="text-muted">
                                    <i class="fas fa-industry"></i> 
                                    {{ $offre->secteur }}
                                </small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    {{ $offre->lieu }}
                                </small>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('offres.show', $offre) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> Détails
                                </a>
                                <a href="{{ route('candidatures.create', $offre) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-paper-plane"></i> Postuler
                                </a>
                            </div>
                            <small class="text-muted">
                                {{ $offre->created_at->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Aucune offre trouvée</h6>
                        <small class="text-muted">Essayez de modifier vos critères de recherche</small>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($offres->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Affichage {{ $offres->firstItem() }}-{{ $offres->lastItem() }} 
                        sur {{ $offres->total() }} résultats
                    </small>
                    {{ $offres->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.fs-4 {
    font-size: 1.5rem;
    font-weight: 600;
}

.form-floating-sm > .form-control {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.form-floating-sm > label {
    font-size: 0.75rem;
}

.card-body.p-3 {
    padding: 1rem !important;
}

.card-header.py-3 {
    padding: 0.75rem 1rem !important;
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
</style>
@endsection
