@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-tasks text-success"></i> 
                Mes Activités
            </h2>
            <small class="text-muted">
                Gérez les activités de vos stagiaires
            </small>
        </div>
        <div>
            <a href="{{ route('activities.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nouvelle activité
            </a>
            <a href="{{ route('encadrant.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour au dashboard
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        <option value="proposee" {{ request('statut') == 'proposee' ? 'selected' : '' }}>Proposées</option>
                        <option value="assignee" {{ request('statut') == 'assignee' ? 'selected' : '' }}>Assignées</option>
                        <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="soumise" {{ request('statut') == 'soumise' ? 'selected' : '' }}>Soumises</option>
                        <option value="validee" {{ request('statut') == 'validee' ? 'selected' : '' }}>Validées</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="priorite" class="form-select form-select-sm">
                        <option value="">Toutes les priorités</option>
                        <option value="basse" {{ request('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                        <option value="moyenne" {{ request('priorite') == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                        <option value="haute" {{ request('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                        <option value="urgente" {{ request('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="stagiaire_id" class="form-select form-select-sm">
                        <option value="">Tous les stagiaires</option>
                        @foreach($stagiaires as $stagiaire)
                        <option value="{{ $stagiaire->id }}" {{ request('stagiaire_id') == $stagiaire->id ? 'selected' : '' }}>
                            {{ $stagiaire->prenom }} {{ $stagiaire->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>
                        <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des activités -->
    @if($activities->count() > 0)
    <div class="row">
        @foreach($activities as $activity)
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-{{ $activity->statut_color }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ $activity->titre }}</h6>
                        <div>
                            <span class="badge bg-light text-dark me-1">{{ $activity->statut_label }}</span>
                            <span class="badge bg-{{ $activity->priorite_color }} text-dark">{{ $activity->priorite_label }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text small text-muted">{{ Str::limit($activity->description, 150) }}</p>
                    
                    @if($activity->stagiaire)
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-user"></i> 
                            Stagiaire: {{ $activity->stagiaire->prenom }} {{ $activity->stagiaire->nom }}
                        </small>
                    </div>
                    @endif

                    @if($activity->date_limite)
                    <div class="mb-2">
                        <div class="alert alert-warning alert-sm py-2 mb-0">
                            <i class="fas fa-clock"></i> 
                            <small>Date limite: {{ $activity->date_limite->format('d/m/Y') }}</small>
                            @if($activity->estEnRetard())
                            <span class="badge bg-danger ms-1">En retard</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center small">
                            <span><strong>Progression:</strong></span>
                            <span>{{ $activity->progression }}%</span>
                        </div>
                        <div class="progress progress-sm" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $activity->progression }}%"></div>
                        </div>
                    </div>

                    @if($activity->submissions->count() > 0)
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-file-upload"></i> 
                            {{ $activity->submissions->count() }} livrable(s) soumis
                        </small>
                    </div>
                    @endif

                    @if($activity->discussions->count() > 0)
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-comments"></i> 
                            {{ $activity->discussions->count() }} discussion(s)
                        </small>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <div class="btn-group btn-group-sm w-100">
                        <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye"></i> Détails
                        </a>
                        @if($activity->stagiaire)
                        <a href="{{ route('activities.edit', $activity) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        @endif
                        @if(!$activity->stagiaire)
                        <a href="#" onclick="assignerActivite({{ $activity->id }})" class="btn btn-outline-success">
                            <i class="fas fa-user-plus"></i> Assigner
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-5">
        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">Aucune activité</h5>
        <p class="text-muted">Vous n'avez pas encore créé d'activités.</p>
        <a href="{{ route('activities.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Créer une activité
        </a>
    </div>
    @endif
</div>
@endsection
