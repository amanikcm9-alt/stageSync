@extends('layouts.rh')

@section('title', 'Entretiens')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="fas fa-calendar-check text-primary me-2"></i>
                Entretiens
            </h2>
            <p class="text-muted mb-0">Gestion des entretiens de candidature</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <div class="form-floating form-floating-sm">
                        <select class="form-select form-select-sm" name="statut">
                            <option value="">Tous les statuts</option>
                            @foreach($statuts as $value => $label)
                                <option value="{{ $value }}" {{ (request('statut') == $value) || (!request('statut') && $value == 'en_cours') ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <label for="statut" class="small">Statut</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-sm">
                        <select class="form-select form-select-sm" name="evaluation">
                            <option value="">Tous</option>
                            <option value="non_evalue" {{ request('evaluation') == 'non_evalue' ? 'selected' : '' }}>Non évalués</option>
                            <option value="evalue" {{ request('evaluation') == 'evalue' ? 'selected' : '' }}>Évalués</option>
                        </select>
                        <label for="evaluation" class="small">Évaluation</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-sm">
                        <select class="form-select form-select-sm" name="date_filter">
                            <option value="">Toutes les dates</option>
                            <option value="commencees" {{ request('date_filter') == 'commencees' ? 'selected' : '' }}>Déjà commencées</option>
                            <option value="non_terminees" {{ request('date_filter') == 'non_terminees' ? 'selected' : '' }}>Pas encore terminées</option>
                        </select>
                        <label for="date_filter" class="small">Période</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-sm">
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="search" 
                               name="search" 
                               placeholder="Rechercher..."
                               value="{{ request('search') }}">
                        <label for="search" class="small">Rechercher</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-sm">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des entretiens -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($entretiens->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Candidat</th>
                                <th>Offre</th>
                                <th>Date/Heure</th>
                                <th>Lieu</th>
                                <th>Statut</th>
                                <th>Évaluation</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entretiens as $entretien)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 32px; height: 32px; font-size: 12px;">
                                                {{ strtoupper(substr($entretien->candidature->prenom, 0, 1)) }}{{ strtoupper(substr($entretien->candidature->nom, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $entretien->candidature->nom }} {{ $entretien->candidature->prenom }}</div>
                                                <small class="text-muted">{{ $entretien->candidature->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $entretien->candidature->offreStage?->titre ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $entretien->candidature->offreStage?->entreprise?->nom ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $entretien->date_entretien->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $entretien->heure_entretien->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $entretien->lieu_entretien }}
                                        </span>
                                    </td>
                                    <td>
                                        @switch($entretien->statut)
                                            @case('planifie')
                                                <span class="badge bg-info">
                                                    <i class="fas fa-clock me-1"></i>{{ $entretien->statut_label }}
                                                </span>
                                                @break
                                            @case('en_cours')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-spinner me-1"></i>{{ $entretien->statut_label }}
                                                </span>
                                                @break
                                            @case('termine')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>{{ $entretien->statut_label }}
                                                </span>
                                                @break
                                            @case('annule')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>{{ $entretien->statut_label }}
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($entretien->isEvalue())
                                            <div>
                                                <span class="badge bg-primary">{{ $entretien->note_evaluation }}/20</span>
                                                <small class="text-muted d-block">{{ $entretien->note_label }}</small>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Non évalué</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                    <a href="{{ route('rh.entretiens.show', $entretien) }}" 
                                       class="btn btn-outline-primary btn-sm" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $entretiens->firstItem() }} à {{ $entretiens->lastItem() }} 
                        sur {{ $entretiens->total() }} entretiens
                    </div>
                    {{ $entretiens->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun entretien trouvé</h5>
                    <p class="text-muted">Aucun entretien ne correspond à vos critères de recherche.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
