@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-users text-success"></i> 
                Utilisateurs
            </h2>
            <small class="text-muted">
                {{ $users->total() }} utilisateur{{ $users->total() > 1 ? 's' : '' }}
            </small>
        </div>
        <div>
            <a href="{{ route('rh.users.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-user-plus"></i> Ajouter
            </a>
        </div>
    </div>

    <!-- Filtres compacts -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('rh.users.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm" name="search" 
                           placeholder="Nom, email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="role">
                        <option value="">Tous les rôles</option>
                        <option value="stagiaire" {{ request('role') == 'stagiaire' ? 'selected' : '' }}>Stagiaire</option>
                        <option value="encadrant" {{ request('role') == 'encadrant' ? 'selected' : '' }}>Encadrant</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status">
                        <option value="">Statut</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}">Actif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}">Inactif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('rh.users.index') }}" class="btn btn-outline-secondary btn-sm">
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
                        <th class="small">Utilisateur</th>
                        <th class="small">Email</th>
                        <th class="small">Rôle</th>
                        <th class="small">Statut</th>
                        <th class="small">Date</th>
                        <th class="small text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="small">
                            <td>
                                <div class="fw-bold">{{ $user->nom }} {{ $user->prenom }}</div>
                                <small class="text-muted">{{ $user->telephone ?? '-' }}</small>
                            </td>
                            <td>
                                <small>{{ $user->email }}</small>
                            </td>
                            <td>
                                @switch($user->role->name)
                                    @case('stagiaire')
                                        <span class="badge bg-primary">Stagiaire</span>
                                        @break
                                    @case('encadrant')
                                        <span class="badge bg-success">Encadrant</span>
                                        @break
                                    @case('rh')
                                        <span class="badge bg-warning">RH</span>
                                        @break
                                    @case('admin')
                                        <span class="badge bg-danger">Admin</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($user->active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $user->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('rh.users.show', $user) }}" 
                                       class="btn btn-outline-primary btn-sm" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('rh.users.edit', $user) }}" 
                                       class="btn btn-outline-secondary btn-sm" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('rh.users.destroy', $user) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Supprimer"
                                                    onclick="return confirm('Supprimer {{ $user->nom }} {{ $user->prenom }} ?')">
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
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <div class="text-muted">Aucun utilisateur trouvé</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination compacte -->
        @if($users->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Affichage {{ $users->firstItem() }}-{{ $users->lastItem() }} 
                        sur {{ $users->total() }} résultats
                    </small>
                    {{ $users->links('pagination::bootstrap-4') }}
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
