@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-briefcase text-success"></i> 
                Offres
            </h2>
            <small class="text-muted">
                {{ $offres->total() }} offre{{ $offres->total() > 1 ? 's' : '' }}
            </small>
        </div>
        <div>
            <a href="{{ route('rh.offres.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Nouvelle
            </a>
        </div>
    </div>

    <!-- Filtres compacts -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('rh.offres') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm" name="search" 
                           placeholder="Rechercher..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="secteur">
                        <option value="">Secteur</option>
                        @foreach($secteurs as $key => $secteur)
                            <option value="{{ $key }}" {{ request('secteur') == $key ? 'selected' : '' }}>
                                {{ $secteur }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="type_stage">
                        <option value="">Type</option>
                        <option value="stage_ete" {{ request('type_stage') == 'stage_ete' ? 'selected' : '' }}>Stage d'été</option>
                        <option value="stage_automne" {{ request('type_stage') == 'stage_automne' ? 'selected' : '' }}>Stage d'automne</option>
                        <option value="stage_hiver" {{ request('type_stage') == 'stage_hiver' ? 'selected' : '' }}>Stage d'hiver</option>
                        <option value="stage_printemps" {{ request('type_stage') == 'stage_printemps' ? 'selected' : '' }}>Stage de printemps</option>
                        <option value="stage_pfe" {{ request('type_stage') == 'stage_pfe' ? 'selected' : '' }}>Stage PFE</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="statut">
                        <option value="">Statut</option>
                        <option value="publiee" {{ request('statut') == 'publiee' ? 'selected' : '' }}">Publiée</option>
                        <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}">Brouillon</option>
                        <option value="cloturee" {{ request('statut') == 'cloturee' ? 'selected' : '' }}">Clôturée</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('rh.offres') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> Effacer
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau compact -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th class="small">Titre</th>
                        <th class="small">Entreprise</th>
                        <th class="small">Type</th>
                        <th class="small">Secteur</th>
                        <th class="small">Date début</th>
                        <th class="small">Durée</th>
                        <th class="small">Lieu</th>
                        <th class="small">Statut</th>
                        <th class="small text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offres as $offre)
                        <tr class="small">
                            <td>
                                <div class="fw-bold">{{ Str::limit($offre->titre, 40) }}</div>
                                <small class="text-muted">{{ $offre->entreprise->nom }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light">{{ $offre->entreprise->nom }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $offre->type_stage }}</span>
                            </td>
                            <td>
                                <span class="badge bg-warning">{{ $offre->secteur }}</span>
                            </td>
                            <td>
                                <small>{{ $offre->date_debut->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                <small>{{ $offre->duree }} sem</small>
                            </td>
                            <td>
                                <small>{{ $offre->lieu }}</small>
                            </td>
                            <td>
                                @switch($offre->statut)
                                    @case('publiee')
                                        <span class="badge bg-success">Publiée</span>
                                        @break
                                    @case('brouillon')
                                        <span class="badge bg-secondary">Brouillon</span>
                                        @break
                                    @case('cloturee')
                                        <span class="badge bg-danger">Clôturée</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('rh.offres.show', $offre) }}" 
                                       class="btn btn-outline-primary btn-sm" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('rh.offres.destroy', $offre) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-outline-danger btn-sm" 
                                                title="Supprimer"
                                                onclick="return confirm('Supprimer l\'offre « {{ $offre->titre }} » ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-briefcase fa-2x text-muted mb-2"></i>
                                <div class="text-muted">Aucune offre trouvée</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination compacte -->
        @if($offres->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Affichage {{ $offres->firstItem() }}-{{ $offres->lastItem() }} 
                        sur {{ $offres->total() }} résultats
                    </small>
                    {{ $offres->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.75rem;
    padding: 0.5rem;
}

.table td {
    padding: 0.5rem;
    vertical-align: middle;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
}

.card-body.py-2 {
    padding: 0.5rem 1rem;
}
</style>
@endsection
