@extends('layouts.app')

@section('title', 'Gestion des Types de Stage')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-graduation-cap text-primary me-2"></i>
                Gestion des Types de Stage
            </h4>
            <small class="text-muted">Administration des types de stage pour les offres</small>
        </div>
        <div>
            <a href="{{ route('rh.type-stages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nouveau Type
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">{{ $typeStages->where('actif', true)->whereNull('archived_at')->count() }}</h5>
                    <p class="card-text">Types Actifs</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h5 class="card-title text-warning">{{ $typeStages->where('actif', false)->whereNull('archived_at')->count() }}</h5>
                    <p class="card-text">Types Inactifs</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <h5 class="card-title text-secondary">{{ $typeStages->whereNotNull('archived_at')->count() }}</h5>
                    <p class="card-text">Types Archivés</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des types de stage -->
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#actifs">
                        <i class="fas fa-check-circle me-1"></i> Actifs ({{ $typeStages->where('actif', true)->whereNull('archived_at')->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#inactifs">
                        <i class="fas fa-pause-circle me-1"></i> Inactifs ({{ $typeStages->where('actif', false)->whereNull('archived_at')->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#archives">
                        <i class="fas fa-archive me-1"></i> Archivés ({{ $typeStages->whereNotNull('archived_at')->count() }})
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Types Actifs -->
                <div class="tab-pane fade show active" id="actifs">
                    @if($typesActifs = $typeStages->where('actif', true)->whereNull('archived_at'))
                        @if($typesActifs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Code</th>
                                            <th>Description</th>
                                            <th>Offres associées</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($typesActifs as $typeStage)
                                            <tr>
                                                <td>
                                                    <strong>{{ $typeStage->nom }}</strong>
                                                    <span class="badge bg-success ms-2">Actif</span>
                                                </td>
                                                <td>
                                                    <code class="bg-light px-2 py-1 rounded">{{ $typeStage->code }}</code>
                                                </td>
                                                <td>{{ Str::limit($typeStage->description, 50) ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $typeStage->offres_count }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('rh.type-stages.edit', $typeStage) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('rh.type-stages.archive', $typeStage) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Archiver ce type de stage ?')">
                                                                <i class="fas fa-archive"></i>
                                                            </button>
                                                        </form>
                                                        @if($typeStage->offres_count == 0)
                                                            <form action="{{ route('rh.type-stages.destroy', $typeStage) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer définitivement ce type de stage ?')">
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
                                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun type de stage actif</h5>
                                <p class="text-muted">Commencez par ajouter un nouveau type de stage.</p>
                                <a href="{{ route('rh.type-stages.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Ajouter un type
                                </a>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Types Inactifs -->
                <div class="tab-pane fade" id="inactifs">
                    @if($typesInactifs = $typeStages->where('actif', false)->whereNull('archived_at'))
                        @if($typesInactifs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Code</th>
                                            <th>Description</th>
                                            <th>Offres associées</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($typesInactifs as $typeStage)
                                            <tr>
                                                <td>
                                                    <strong>{{ $typeStage->nom }}</strong>
                                                    <span class="badge bg-warning ms-2">Inactif</span>
                                                </td>
                                                <td>
                                                    <code class="bg-light px-2 py-1 rounded">{{ $typeStage->code }}</code>
                                                </td>
                                                <td>{{ Str::limit($typeStage->description, 50) ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $typeStage->offres_count }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('rh.type-stages.edit', $typeStage) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('rh.type-stages.archive', $typeStage) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Archiver ce type de stage ?')">
                                                                <i class="fas fa-archive"></i>
                                                            </button>
                                                        </form>
                                                        @if($typeStage->offres_count == 0)
                                                            <form action="{{ route('rh.type-stages.destroy', $typeStage) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer définitivement ce type de stage ?')">
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
                                <h5 class="text-muted">Aucun type de stage inactif</h5>
                                <p class="text-muted">Tous les types de stage sont actuellement actifs.</p>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Types Archivés -->
                <div class="tab-pane fade" id="archives">
                    @if($typesArchives = $typeStages->whereNotNull('archived_at'))
                        @if($typesArchives->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Code</th>
                                            <th>Description</th>
                                            <th>Date d'archivage</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($typesArchives as $typeStage)
                                            <tr class="text-muted">
                                                <td>
                                                    <strong>{{ $typeStage->nom }}</strong>
                                                    <span class="badge bg-secondary ms-2">Archivé</span>
                                                </td>
                                                <td>
                                                    <code class="bg-light px-2 py-1 rounded">{{ $typeStage->code }}</code>
                                                </td>
                                                <td>{{ Str::limit($typeStage->description, 50) ?? '-' }}</td>
                                                <td>{{ $typeStage->archived_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <form action="{{ route('rh.type-stages.restore', $typeStage) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Restaurer ce type de stage ?')">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                        @if($typeStage->offres_count == 0)
                                                            <form action="{{ route('rh.type-stages.destroy', $typeStage) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer définitivement ce type de stage ?')">
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
                                <h5 class="text-muted">Aucun type de stage archivé</h5>
                                <p class="text-muted">Aucun type de stage n'a été archivé pour le moment.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
