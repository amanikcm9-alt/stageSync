@extends('layouts.app')

@section('title', 'Affectations')

@section('content')
<div class="container-fluid py-4">
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
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Stagiaire</th>
                            <th>Email</th>
                            <th>Encadrant</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stagiaires as $stagiaire)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar-sm me-2">
                                            {{ strtoupper(substr($stagiaire->prenom, 0, 1)) }}{{ strtoupper(substr($stagiaire->nom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $stagiaire->nom }} {{ $stagiaire->prenom }}</strong>
                                            @if($stagiaire->offreStage)
                                                <br><small class="text-muted">{{ $stagiaire->offreStage->titre }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <small>{{ $stagiaire->email }}</small>
                                </td>
                                <td>
                                    @if($stagiaire->encadrant)
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-xs me-2">
                                                {{ strtoupper(substr($stagiaire->encadrant->prenom, 0, 1)) }}{{ strtoupper(substr($stagiaire->encadrant->nom, 0, 1)) }}
                                            </div>
                                            <small>{{ $stagiaire->encadrant->nom }} {{ $stagiaire->encadrant->prenom }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($stagiaire->encadrant_id)
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
                                    <div class="text-muted">
                                        <i class="fas fa-user-check fa-2x mb-2"></i>
                                        <p>Aucun stagiaire trouvé</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination compacte -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">
            Affichage de {{ $stagiaires->firstItem() }} à {{ $stagiaires->lastItem() }} 
            sur {{ $stagiaires->total() }} stagiaires
        </small>
        {{ $stagiaires->links() }}
    </div>
</div>

<!-- Styles -->
<style>
.user-avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.75rem;
}

.user-avatar-xs {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.6rem;
}

.table th {
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #f8f9fa;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection
