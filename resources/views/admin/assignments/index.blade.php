@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-user-check text-primary"></i> 
                Affectations
            </h2>
            <small class="text-muted">
                {{ $stagiaires->total() }} stagiaire{{ $stagiaires->total() > 1 ? 's' : '' }}
            </small>
        </div>
        <div>
            <a href="{{ route('rh.assignments.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> Affecter
            </a>
        </div>
    </div>

    <!-- Filtres compacts -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('rh.assignments.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm" name="search" 
                           placeholder="Stagiaire..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="encadrant_id">
                        <option value="">Encadrant</option>
                        @foreach($encadrants as $encadrant)
                            <option value="{{ $encadrant->id }}" {{ request('encadrant_id') == $encadrant->id ? 'selected' : '' }}>
                                {{ $encadrant->nom }} {{ $encadrant->prenom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="assignment_status">
                        <option value="">Statut</option>
                        <option value="assigned" {{ request('assignment_status') == 'assigned' ? 'selected' : '' }}">Affectés</option>
                        <option value="unassigned" {{ request('assignment_status') == 'unassigned' ? 'selected' : '' }}">Non affectés</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('rh.assignments.index') }}" class="btn btn-outline-secondary btn-sm">
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
                        <th class="small">Stagiaire</th>
                        <th class="small">Offre</th>
                        <th class="small">Entreprise</th>
                        <th class="small">Encadrant</th>
                        <th class="small">Statut</th>
                        <th class="small">Date</th>
                        <th class="small text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stagiaires as $stagiaire)
                        <tr class="small">
                            <td>
                                <div class="fw-bold">{{ $stagiaire->nom }} {{ $stagiaire->prenom }}</div>
                                <small class="text-muted">{{ $stagiaire->email }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">{{ Str::limit($stagiaire->offre->titre ?? 'Non assigné', 30) }}</div>
                                <small class="text-muted">{{ $stagiaire->offre->entreprise->nom ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light">{{ $stagiaire->offre->entreprise->nom ?? '-' }}</span>
                            </td>
                            <td>
                                @if($stagiaire->encadrant)
                                    <span class="badge bg-info">{{ $stagiaire->encadrant->nom }} {{ $stagiaire->encadrant->prenom }}</span>
                                @else
                                    <span class="badge bg-secondary">Non assigné</span>
                                @endif
                            </td>
                            <td>
                                @if($stagiaire->offre)
                                    <span class="badge bg-success">Affecté</span>
                                @else
                                    <span class="badge bg-warning">Non affecté</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $stagiaire->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('rh.assignments.edit', $stagiaire) }}" 
                                       class="btn btn-outline-primary btn-sm" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('rh.users.show', $stagiaire) }}" 
                                       class="btn btn-outline-secondary btn-sm" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($stagiaire->encadrant_id)
                                        <form action="{{ route('rh.assignments.destroy', $stagiaire) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Supprimer l'affectation"
                                                    onclick="return confirm('Supprimer l\'affectation de {{ $stagiaire->nom }} {{ $stagiaire->prenom }} ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-user-graduate fa-2x text-muted mb-2"></i>
                                <div class="text-muted">Aucun stagiaire trouvé</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination compacte -->
        @if($stagiaires->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Affichage {{ $stagiaires->firstItem() }}-{{ $stagiaires->lastItem() }} 
                        sur {{ $stagiaires->total() }} résultats
                    </small>
                    {{ $stagiaires->links('pagination::bootstrap-4') }}
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
