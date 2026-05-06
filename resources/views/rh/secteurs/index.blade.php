@extends('layouts.app')

@section('title', 'Gestion des Secteurs')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-industry text-primary me-2"></i>
                Gestion des Secteurs
            </h4>
            <small class="text-muted">Administration des secteurs d'activité pour les offres de stage</small>
        </div>
        <div>
            <a href="{{ route('rh.secteurs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nouveau Secteur
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">{{ $secteurs->where('actif', true)->whereNull('archived_at')->count() }}</h5>
                    <p class="card-text">Secteurs Actifs</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h5 class="card-title text-warning">{{ $secteurs->where('actif', false)->whereNull('archived_at')->count() }}</h5>
                    <p class="card-text">Secteurs Inactifs</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <h5 class="card-title text-secondary">{{ $secteurs->whereNotNull('archived_at')->count() }}</h5>
                    <p class="card-text">Secteurs Archivés</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des secteurs -->
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#actifs">
                        <i class="fas fa-check-circle me-1"></i> Actifs ({{ $secteurs->where('actif', true)->whereNull('archived_at')->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#inactifs">
                        <i class="fas fa-pause-circle me-1"></i> Inactifs ({{ $secteurs->where('actif', false)->whereNull('archived_at')->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#archives">
                        <i class="fas fa-archive me-1"></i> Archivés ({{ $secteurs->whereNotNull('archived_at')->count() }})
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Secteurs Actifs -->
                <div class="tab-pane fade show active" id="actifs">
                    @if($secteursActifs = $secteurs->where('actif', true)->whereNull('archived_at'))
                        @if($secteursActifs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th>Offres associées</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($secteursActifs as $secteur)
                                            <tr>
                                                <td>
                                                    <strong>{{ $secteur->nom }}</strong>
                                                    <span class="badge bg-success ms-2">Actif</span>
                                                </td>
                                                <td>{{ Str::limit($secteur->description, 50) ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $secteur->offres_count }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('rh.secteurs.edit', $secteur) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('rh.secteurs.archive', $secteur) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Archiver ce secteur ?')">
                                                                <i class="fas fa-archive"></i>
                                                            </button>
                                                        </form>
                                                        @if($secteur->offres_count == 0)
                                                            <form action="{{ route('rh.secteurs.destroy', $secteur) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer définitivement ce secteur ?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-industry fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun secteur actif</h5>
                                <p class="text-muted">Commencez par ajouter un nouveau secteur.</p>
                                <a href="{{ route('rh.secteurs.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Ajouter un secteur
                                </a>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Secteurs Inactifs -->
                <div class="tab-pane fade" id="inactifs">
                    @if($secteursInactifs = $secteurs->where('actif', false)->whereNull('archived_at'))
                        @if($secteursInactifs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th>Offres associées</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($secteursInactifs as $secteur)
                                            <tr>
                                                <td>
                                                    <strong>{{ $secteur->nom }}</strong>
                                                    <span class="badge bg-warning ms-2">Inactif</span>
                                                </td>
                                                <td>{{ Str::limit($secteur->description, 50) ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $secteur->offres_count }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('rh.secteurs.edit', $secteur) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('rh.secteurs.archive', $secteur) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Archiver ce secteur ?')">
                                                                <i class="fas fa-archive"></i>
                                                            </button>
                                                        </form>
                                                        @if($secteur->offres_count == 0)
                                                            <form action="{{ route('rh.secteurs.destroy', $secteur) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer définitivement ce secteur ?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-pause-circle fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun secteur inactif</h5>
                                <p class="text-muted">Tous les secteurs sont actuellement actifs.</p>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Secteurs Archivés -->
                <div class="tab-pane fade" id="archives">
                    @if($secteursArchives = $secteurs->whereNotNull('archived_at'))
                        @if($secteursArchives->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th>Date d'archivage</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($secteursArchives as $secteur)
                                            <tr class="text-muted">
                                                <td>
                                                    <strong>{{ $secteur->nom }}</strong>
                                                    <span class="badge bg-secondary ms-2">Archivé</span>
                                                </td>
                                                <td>{{ Str::limit($secteur->description, 50) ?? '-' }}</td>
                                                <td>{{ $secteur->archived_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <form action="{{ route('rh.secteurs.restore', $secteur) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Restaurer ce secteur ?')">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                        @if($secteur->offres_count == 0)
                                                            <form action="{{ route('rh.secteurs.destroy', $secteur) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer définitivement ce secteur ?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun secteur archivé</h5>
                                <p class="text-muted">Aucun secteur n'a été archivé pour le moment.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
