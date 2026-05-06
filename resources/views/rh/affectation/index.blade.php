@extends('layouts.app')

@section('title', 'Affectation de RH')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="fas fa-user-tie text-primary me-2"></i>
                Affectation de RH
            </h2>
            <p class="text-muted mb-0">
                <i class="fas fa-filter me-1"></i>
                Stagiaires acceptés sans encadrant
            </p>
        </div>
    </div>

    <!-- Filtre de recherche -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('rh.affectation.index') }}">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Rechercher un stagiaire..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>
                            Rechercher
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des stagiaires -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-graduation-cap me-2"></i>
                Stagiaires à affecter
                <span class="badge bg-primary ms-2">{{ $stagiaires->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($stagiaires->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Stagiaire</th>
                                <th>Offre</th>
                                <th>Secteur</th>
                                <th>Type de stage</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stagiaires as $stagiaire)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                                {{ strtoupper(substr($stagiaire->prenom, 0, 1)) . strtoupper(substr($stagiaire->nom, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $stagiaire->nom }} {{ $stagiaire->prenom }}</div>
                                                <small class="text-muted">{{ $stagiaire->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $stagiaire->candidature->offreStage->titre ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $stagiaire->candidature->offreStage->secteur->nom ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $stagiaire->candidature->offreStage->typeStage->nom ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('rh.affectation.encadrants', $stagiaire->id) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-user-plus me-1"></i>
                                            Choisir un encadrant
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center p-3 border-top">
                    <div class="text-muted">
                        Affichage de {{ $stagiaires->firstItem() }} à {{ $stagiaires->lastItem() }} 
                        sur {{ $stagiaires->total() }} stagiaires
                    </div>
                    {{ $stagiaires->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun stagiaire à affecter</h5>
                    <p class="text-muted">
                        Tous les stagiaires acceptés ont déjà un encadrant.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    font-size: 0.8rem;
    font-weight: bold;
}
</style>
@endsection
