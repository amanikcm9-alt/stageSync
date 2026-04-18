@extends('layouts.app')

@section('styles')
<style>
.tiny {
    font-size: 0.75rem !important;
    line-height: 1.2;
}

.btn-xs {
    padding: 0.125rem 0.25rem;
    font-size: 0.7rem;
    line-height: 1.2;
    border-radius: 0.2rem;
}

.discussion-activity {
    max-height: 200px;
    overflow-y: auto;
}

.discussion-activity::-webkit-scrollbar {
    width: 4px;
}

.discussion-activity::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.discussion-activity::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.discussion-activity::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-tachometer-alt text-success"></i> 
                Tableau de Bord Encadrant
            </h2>
            <small class="text-muted">
                Bienvenue, {{ auth()->user()->prenom }} {{ auth()->user()->nom }}
            </small>
        </div>
        <div>
            <a href="{{ route('activities.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Nouvelle activité
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-3">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-primary mb-1">{{ $stats['total_activities'] }}</h5>
                    <small class="text-muted">Total activités</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-info mb-1">{{ $stats['en_cours'] }}</h5>
                    <small class="text-muted">En cours</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-warning mb-1">{{ $stats['soumises'] }}</h5>
                    <small class="text-muted">Soumises</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-success mb-1">{{ $stats['validees'] }}</h5>
                    <small class="text-muted">Validées</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-secondary mb-1">{{ $stats['total_stagiaires'] }}</h5>
                    <small class="text-muted">Stagiaires</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-warning mb-1">{{ $stats['total_evaluations'] }}</h5>
                    <small class="text-muted">Évaluations</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Mes Stagiaires -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-users"></i> Mes Stagiaires
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($stagiaires as $stagiaire)
                <div class="col-md-3 mb-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($stagiaire->prenom, 0, 1)) }}{{ strtoupper(substr($stagiaire->nom, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $stagiaire->prenom }} {{ $stagiaire->nom }}</div>
                                    <small class="text-muted">{{ $stagiaire->email }}</small>
                                    <div class="mt-1">
                                        <span class="badge bg-info text-white">
                                            {{ $stagiaire->activities->count() }} activités
                                        </span>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('activities.create') }}?stagiaire_id={{ $stagiaire->id }}" class="dropdown-item">
                                                <i class="fas fa-plus"></i> Créer activité
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('evaluations.create') }}?stagiaire_id={{ $stagiaire->id }}" class="dropdown-item">
                                                <i class="fas fa-star"></i> Évaluer
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('documents.create') }}?stagiaire_id={{ $stagiaire->id }}" class="dropdown-item">
                                                <i class="fas fa-book"></i> Publier support
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item position-relative" onclick="openGeneralDiscussion({{ $stagiaire->id }}, '{{ $stagiaire->prenom }} {{ $stagiaire->nom }}')">
                                                <i class="fas fa-comments"></i> Discuter
                                                @php
                                                    $unreadCount = \App\Models\Discussion::where('receiver_id', auth()->id())
                                                        ->where('sender_id', $stagiaire->id)
                                                        ->where('read', false)
                                                        ->count();
                                                @endphp
                                                @if($unreadCount > 0)
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                                    {{ $unreadCount }}
                                                </span>
                                                @endif
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Section Mes Activités -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0">
                <i class="fas fa-tasks"></i> Mes Activités
            </h6>
        </div>
        <div class="card-body">
            <!-- Onglets -->
            <ul class="nav nav-tabs mb-3" id="activitiesTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#allActivities">
                        Toutes ({{ $activities->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#enCours">
                        En cours ({{ $activities->where('statut', 'en_cours')->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#soumises">
                        Soumises ({{ $activities->where('statut', 'soumise')->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#validees">
                        Validées ({{ $activities->where('statut', 'validee')->count() }})
                    </a>
                </li>
            </ul>

            <!-- Contenu des onglets -->
            <div class="tab-content">
                <!-- Toutes les activités -->
                <div class="tab-pane fade show active" id="allActivities">
                    @if($activities->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Activité</th>
                                    <th>Stagiaire</th>
                                    <th>Priorité</th>
                                    <th>Statut</th>
                                    <th>Progression</th>
                                    <th>Date limite</th>
                                    <th>Notifications</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $activity)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $activity->titre }}</div>
                                        @if($activity->date_debut)
                                        <small class="text-muted">Début: {{ $activity->date_debut->format('d/m/Y') }}</small>
                                        @endif
                                        
                                        <!-- Discussions sous l'activité -->
                                        @if($activity->discussions->count() > 0)
                                        <div class="mt-2 p-2 bg-light rounded border">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="fw-bold text-primary">
                                                    <i class="fas fa-comments"></i> 
                                                    {{ $activity->discussions->count() }} message(s)
                                                    @if($activity->discussions()->where('read', false)->where('receiver_id', auth()->id())->count() > 0)
                                                    <span class="badge bg-danger text-white ms-1">{{ $activity->discussions()->where('read', false)->where('receiver_id', auth()->id())->count() }} nouveau(x)</span>
                                                    @endif
                                                </small>
                                                <button type="button" class="btn btn-xs btn-outline-primary" onclick="toggleActivityDiscussion({{ $activity->id }})">
                                                    <i class="fas fa-chevron-down" id="chevron-{{ $activity->id }}"></i>
                                                </button>
                                            </div>
                                            <div id="discussion-{{ $activity->id }}" class="discussion-activity" style="display: none;">
                                                @foreach($activity->latestDiscussions()->take(3) as $discussion)
                                                <div class="d-flex {{ $discussion->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }} mb-1">
                                                    <div class="p-1 rounded {{ $discussion->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-white border' }} small" style="max-width: 90%;">
                                                        <div class="fw-bold tiny">{{ $discussion->sender->prenom }} {{ $discussion->sender->nom }}</div>
                                                        <div class="tiny">{{ Str::limit($discussion->message, 50) }}</div>
                                                        <div class="text-muted tiny">{{ $discussion->created_at->format('H:i') }}</div>
                                                    </div>
                                                </div>
                                                @endforeach
                                                
                                                <!-- Interface de communication rapide -->
                                                <div class="mt-2 p-2 bg-white rounded border">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control form-control-sm" id="quickMessage-{{ $activity->id }}" placeholder="Répondre rapidement..." maxlength="200">
                                                        <button type="button" class="btn btn-primary btn-sm" onclick="sendQuickReply({{ $activity->id }}, '{{ $activity->stagiaire ? $activity->stagiaire->prenom . ' ' . $activity->stagiaire->nom : '' }}')">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <div class="text-center mt-2">
                                                    <button type="button" class="btn btn-xs btn-outline-info" onclick="showAllDiscussions({{ $activity->id }})">
                                                        <i class="fas fa-comments"></i> Voir toute la discussion
                                                    </button>
                                                    <button type="button" class="btn btn-xs btn-outline-success ms-1" onclick="quickReply({{ $activity->id }}, '{{ $activity->stagiaire ? $activity->stagiaire->prenom . ' ' . $activity->stagiaire->nom : '' }}')">
                                                        <i class="fas fa-reply"></i> Discussion complète
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($activity->stagiaire)
                                            <div class="fw-bold">{{ $activity->stagiaire->prenom }} {{ $activity->stagiaire->nom }}</div>
                                            <small class="text-muted">{{ $activity->stagiaire->email }}</small>
                                        @else
                                            <span class="text-muted">Non assignée</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $activity->priorite_color }} text-white">
                                            {{ $activity->priorite_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $activity->statut_color }} text-white">
                                            {{ $activity->statut_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $activity->progression >= 80 ? 'success' : ($activity->progression >= 50 ? 'warning' : 'info') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $activity->progression }}%">
                                                {{ $activity->progression }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($activity->date_limite)
                                            <small class="{{ $activity->estEnRetard() ? 'text-danger' : 'text-muted' }}">
                                                {{ $activity->date_limite->format('d/m/Y') }}
                                                @if($activity->estEnRetard())
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                @endif
                                            </small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($activity->discussions->count() > 0)
                                            @php
                                                $unreadCount = $activity->discussions()->where('read', false)->where('receiver_id', auth()->id())->count();
                                                $latestMessage = $activity->latestDiscussions()->first();
                                            @endphp
                                            <button type="button" class="btn btn-sm position-relative" onclick="openActivityNotification({{ $activity->id }}, '{{ $latestMessage ? addslashes($latestMessage->message) : '' }}', '{{ $latestMessage ? $latestMessage->sender->prenom . ' ' . $latestMessage->sender->nom : '' }}')" title="{{ $unreadCount }} message(s) non lu(s)">
                                                <i class="fas fa-bell {{ $unreadCount > 0 ? 'text-warning' : 'text-muted' }}"></i>
                                                @if($unreadCount > 0)
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                    {{ $unreadCount }}
                                                    <span class="visually-hidden">messages non lus</span>
                                                </span>
                                                @endif
                                            </button>
                                        @else
                                            <i class="fas fa-bell-slash text-muted"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <!-- Publier support -->
                                            <button type="button" class="btn btn-outline-info" onclick="publierSupport({{ $activity->id }})" title="Publier un support">
                                                <i class="fas fa-book"></i>
                                            </button>
                                            
                                            <!-- Consulter soumission -->
                                            @if($activity->submissions->count() > 0)
                                            <a href="{{ route('submissions.show', $activity->submissions->first()) }}" class="btn btn-outline-success" title="Voir la soumission">
                                                <i class="fas fa-file-alt"></i>
                                            </a>
                                            @endif
                                            
                                            <!-- Valider/Refuser -->
                                            @if($activity->statut === 'soumise')
                                            <button type="button" class="btn btn-outline-primary" onclick="validerActivite({{ $activity->id }})" title="Valider">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="refuserActivite({{ $activity->id }})" title="Refuser">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                            
                                            <!-- Évaluer -->
                                            @if(in_array($activity->statut, ['validee', 'terminee']))
                                            <button type="button" class="btn btn-outline-warning" onclick="evaluerActivite({{ $activity->id }})" title="Évaluer">
                                                <i class="fas fa-star"></i>
                                            </button>
                                            @endif
                                            
                                            <!-- Assigner -->
                                            @if($activity->statut === 'proposee')
                                            <button type="button" class="btn btn-outline-primary" onclick="assignerActivite({{ $activity->id }})" title="Assigner à un stagiaire">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                            @endif
                                            
                                            <!-- Détails -->
                                            <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-secondary" title="Détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <!-- Modifier -->
                                            <a href="{{ route('activities.edit', $activity) }}" class="btn btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune activité</h5>
                        <p class="text-muted">Vous n'avez pas encore créé d'activités.</p>
                        <a href="{{ route('activities.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Créer une activité
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Activités en cours -->
                <div class="tab-pane fade" id="enCours">
                    @if($activities->where('statut', 'en_cours')->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Activité</th>
                                    <th>Stagiaire</th>
                                    <th>Priorité</th>
                                    <th>Statut</th>
                                    <th>Progression</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities->where('statut', 'en_cours') as $activity)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $activity->titre }}</div>
                                        <small class="text-muted">{{ $activity->stagiaire->prenom }} {{ $activity->stagiaire->nom }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $activity->priorite_color }} text-white">
                                            {{ $activity->priorite_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $activity->statut_color }} text-white">
                                            {{ $activity->statut_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $activity->progression >= 80 ? 'success' : ($activity->progression >= 50 ? 'warning' : 'info') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $activity->progression }}%">
                                                {{ $activity->progression }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('activities.edit', $activity) }}" class="btn btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <h5 class="text-muted">Aucune activité en cours</h5>
                    </div>
                    @endif
                </div>

                <!-- Activités soumises -->
                <div class="tab-pane fade" id="soumises">
                    @if($activities->where('statut', 'soumise')->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Activité</th>
                                    <th>Stagiaire</th>
                                    <th>Date soumission</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities->where('statut', 'soumise') as $activity)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $activity->titre }}</div>
                                        <small class="text-muted">{{ $activity->stagiaire->prenom }} {{ $activity->stagiaire->nom }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-white">
                                            {{ $activity->statut_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $activity->date_soumission ? $activity->date_soumission->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('submissions.show', $activity->submissions->first()) }}" class="btn btn-outline-success">
                                                <i class="fas fa-file-alt"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-primary" onclick="validerActivite({{ $activity->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="refuserActivite({{ $activity->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <h5 class="text-muted">Aucune activité soumise</h5>
                    </div>
                    @endif
                </div>

                <!-- Activités validées -->
                <div class="tab-pane fade" id="validees">
                    @if($activities->where('statut', 'validee')->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Activité</th>
                                    <th>Stagiaire</th>
                                    <th>Date validation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities->where('statut', 'validee') as $activity)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $activity->titre }}</div>
                                        <small class="text-muted">{{ $activity->stagiaire->prenom }} {{ $activity->stagiaire->nom }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success text-white">
                                            {{ $activity->statut_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $activity->date_validation ? $activity->date_validation->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-warning" onclick="evaluerActivite({{ $activity->id }})">
                                                <i class="fas fa-star"></i>
                                            </button>
                                            <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <h5 class="text-muted">Aucune activité validée</h5>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Section Évaluations -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-warning text-white">
            <h6 class="mb-0">
                <i class="fas fa-star"></i> Mes Évaluations
            </h6>
        </div>
        <div class="card-body">
            @if($evaluations->count() > 0)
            <div class="row">
                @foreach($evaluations->take(4) as $evaluation)
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">{{ $evaluation->type_label }}</h6>
                                <span class="badge bg-{{ $evaluation->statut_color }} text-white">
                                    {{ $evaluation->statut_label }}
                                </span>
                            </div>
                            @if($evaluation->note_globale)
                            <div class="text-center mb-2">
                                <div class="fs-4">{{ $evaluation->note_globale }}/20</div>
                                <div>{!! $evaluation->note_etoiles !!}</div>
                            </div>
                            @endif
                            <p class="card-text small text-muted mb-2">{{ $evaluation->appreciation_courte }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> {{ $evaluation->stagiaire->prenom }} {{ $evaluation->stagiaire->nom }}
                                </small>
                                <div class="btn-group btn-group-sm">
                                    @if($evaluation->statut === 'brouillon')
                                    <a href="{{ route('evaluations.edit', $evaluation) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="finaliserEvaluation({{ $evaluation->id }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                    @if($evaluation->statut === 'finalisee')
                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="validerEvaluation({{ $evaluation->id }})">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                    @endif
                                    <a href="{{ route('evaluations.show', $evaluation) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($evaluations->count() > 4)
                <div class="text-center mt-3">
                    <a href="{{ route('evaluations.index') }}" class="btn btn-outline-warning">
                        Voir toutes les évaluations
                    </a>
                </div>
                @endif
            @else
            <div class="text-center py-4">
                <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucune évaluation</h5>
                <p class="text-muted">Vous n'avez pas encore créé d'évaluations.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Section Documents -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-secondary text-white">
            <h6 class="mb-0">
                <i class="fas fa-book"></i> Mes Documents
            </h6>
        </div>
        <div class="card-body">
            @if($documents->count() > 0)
            <div class="row">
                @foreach($documents->take(6) as $document)
                <div class="col-md-4 mb-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-2">
                            <div class="d-flex align-items-center">
                                <i class="fas {{ $document->type_icon }} text-{{ $document->type_color }} me-2"></i>
                                <div class="flex-grow-1">
                                    <small class="fw-bold">{{ $document->titre }}</small>
                                    <br>
                                    <small class="text-muted">{{ $document->type_label }}</small>
                                    @if($document->activity)
                                    <br>
                                    <small class="text-info">Activité: {{ $document->activity->titre }}</small>
                                    @endif
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ $document->lien }}" target="_blank" class="dropdown-item">
                                                <i class="fas fa-eye"></i> Consulter
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('documents.edit', $document) }}" class="dropdown-item">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item text-danger" onclick="supprimerDocument({{ $document->id }})">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($documents->count() > 6)
                <div class="text-center mt-2">
                    <a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-secondary">
                        Voir tous les documents
                    </a>
                </div>
                @endif
            @else
            <div class="text-center py-4">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucun document</h5>
                <p class="text-muted">Vous n'avez pas encore publié de documents.</p>
                <a href="{{ route('documents.create') }}" class="btn btn-secondary">
                    <i class="fas fa-plus"></i> Publier un document
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Section Discussions -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0">
            <i class="fas fa-comments me-2"></i> Discussions avec les stagiaires
        </h6>
    </div>
    <div class="card-body">
        @if(auth()->user()->unreadDiscussions()->count() > 0)
        <div class="alert alert-info py-2">
            <i class="fas fa-bell me-1"></i> 
            {{ auth()->user()->unreadDiscussions()->count() }} message(s) non lu(s)
        </div>
        @endif
        
        @foreach($activities as $activity)
            @if($activity->discussions->count() > 0)
            <div class="border rounded p-2 mb-2">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">{{ $activity->titre }}</h6>
                    <span class="badge bg-info">{{ $activity->discussions->count() }}</span>
                </div>
                @if($activity->stagiaire)
                    <small class="text-muted">Stagiaire: {{ $activity->stagiaire->prenom }} {{ $activity->stagiaire->nom }}</small>
                @endif
                <div class="discussion-messages mt-2">
                    @foreach($activity->latestDiscussions()->take(3) as $discussion)
                    <div class="d-flex {{ $discussion->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }} mb-1">
                        <div class="p-1 rounded {{ $discussion->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }} small" style="max-width: 80%;">
                            <div class="fw-bold small">{{ $discussion->sender->prenom }} {{ $discussion->sender->nom }}</div>
                            <div class="small">{{ $discussion->message }}</div>
                            <div class="text-muted small">{{ $discussion->created_at->format('H:i') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($activity->discussions->count() > 3)
                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="showAllDiscussions({{ $activity->id }})">
                        <i class="fas fa-comments"></i> Voir toute la discussion
                    </button>
                </div>
                @endif
            </div>
            @endif
        @endforeach
        
        @if($activities->pluck('discussions')->flatten()->isEmpty())
        <div class="text-center py-4">
            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucune discussion en cours</h5>
            <p class="text-muted">Les stagiaires n'ont pas encore envoyé de messages.</p>
        </div>
        @endif
    </div>
</div>

<!-- Modal Discussion Complète -->
<div class="modal fade" id="discussionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Discussion - <span id="discussionActivityTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="discussionMessages" style="max-height: 400px; overflow-y: auto;">
                    <!-- Messages chargés dynamiquement -->
                </div>
                <div class="mt-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="newMessage" placeholder="Votre message...">
                        <button class="btn btn-primary" onclick="sendMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de notification d'activité -->
<div class="modal fade" id="activityNotificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-bell me-2"></i> Notification d'Activité
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 id="notificationActivityTitle" class="fw-bold text-primary"></h6>
                    <div id="notificationMessage" class="alert alert-info"></div>
                    <div id="notificationSender" class="small text-muted"></div>
                </div>
                
                <!-- Discussion complète -->
                <div class="border rounded p-3 mb-3" style="max-height: 300px; overflow-y: auto;">
                    <h6 class="fw-bold mb-3">Discussion complète</h6>
                    <div id="activityDiscussionMessages"></div>
                </div>
                
                <!-- Zone de réponse -->
                <div class="input-group">
                    <input type="text" class="form-control" id="activityNotificationReply" placeholder="Tapez votre réponse...">
                    <button type="button" class="btn btn-primary" onclick="sendActivityNotificationReply()">
                        <i class="fas fa-paper-plane"></i> Envoyer
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="markActivityNotificationAsRead()">
                    <i class="fas fa-check"></i> Marquer comme lu
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Publier Support -->
<div class="modal fade" id="supportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Publier un support</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" id="supportForm">
                @csrf
                <input type="hidden" name="activity_id" id="supportActivityId">
                <input type="hidden" name="type" value="support">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre du support *</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fichier" class="form-label">Fichier (PDF, Word, PPT)</label>
                        <input type="file" class="form-control" id="fichier" name="fichier" accept=".pdf,.doc,.docx,.ppt,.pptx">
                    </div>
                    <div class="mb-3">
                        <label for="url" class="form-label">URL (optionnel)</label>
                        <input type="url" class="form-control" id="url" name="url" placeholder="https://...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Publier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Assigner Activité -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assigner à un stagiaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="#" id="assignForm" onsubmit="event.preventDefault(); submitAssignForm();">
                @csrf
                <input type="hidden" name="activity_id" id="assignActivityId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="stagiaire_id" class="form-label">Stagiaire *</label>
                        <select class="form-select" id="stagiaire_id" name="stagiaire_id" required>
                            <option value="">Choisir un stagiaire</option>
                            @foreach($stagiaires as $stagiaire)
                            <option value="{{ $stagiaire->id }}">{{ $stagiaire->prenom }} {{ $stagiaire->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Assigner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Évaluer Activité -->
<div class="modal fade" id="evaluationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Évaluer l'activité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="#" id="evaluationForm" onsubmit="event.preventDefault(); submitEvaluationForm();">
                @csrf
                <input type="hidden" name="activity_id" id="evaluationActivityId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="note" class="form-label">Note (0-20) *</label>
                        <input type="number" class="form-control" id="note" name="note" min="0" max="20" required>
                    </div>
                    <div class="mb-3">
                        <label for="feedback" class="form-label">Feedback</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Évaluer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Discussion Générale -->
<div class="modal fade" id="generalDiscussionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Discussion - <span id="generalDiscussionUserName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="generalDiscussionUserId" value="">
                <div id="generalDiscussionMessages" style="max-height: 400px; overflow-y: auto;">
                    <!-- Messages chargés dynamiquement -->
                </div>
                <div class="mt-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="generalNewMessage" placeholder="Votre message...">
                        <button class="btn btn-primary" onclick="sendGeneralMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function publierSupport(activityId) {
    document.getElementById('supportActivityId').value = activityId;
    new bootstrap.Modal(document.getElementById('supportModal')).show();
}

function assignerActivite(activityId) {
    document.getElementById('assignActivityId').value = activityId;
    new bootstrap.Modal(document.getElementById('assignModal')).show();
}

function submitAssignForm() {
    const activityId = document.getElementById('assignActivityId').value;
    const form = document.getElementById('assignForm');
    const formData = new FormData(form);
    
    fetch(`/activities/${activityId}/assigner`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de l\'assignation');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'assignation');
    });
}

function submitEvaluationForm() {
    const activityId = document.getElementById('evaluationActivityId').value;
    const form = document.getElementById('evaluationForm');
    const formData = new FormData(form);
    
    fetch(`/activities/${activityId}/evaluer`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de l\'évaluation');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'évaluation');
    });
}

let currentActivityId = null;
const authUserId = {{ auth()->id() }};

function showAllDiscussions(activityId) {
    console.log('showAllDiscussions appelé avec activityId:', activityId);
    currentActivityId = activityId;
    
    // Récupérer les détails de l'activité via la nouvelle route JSON
    console.log('Tentative de fetch /activities/' + activityId + '/json');
    fetch(`/activities/${activityId}/json`)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(activity => {
            console.log('Activity data:', activity);
            const titleElement = document.getElementById('discussionActivityTitle');
            if (titleElement) {
                titleElement.textContent = activity.titre;
                console.log('Titre défini:', activity.titre);
            } else {
                console.error('Element discussionActivityTitle non trouvé');
            }
            
            // Charger les messages
            loadDiscussionMessages(activityId);
            
            // Afficher le modal
            const modalElement = document.getElementById('discussionModal');
            if (modalElement) {
                console.log('Modal trouvé, tentative d\'affichage');
                new bootstrap.Modal(modalElement).show();
            } else {
                console.error('Modal discussionModal non trouvé');
            }
        })
        .catch(error => {
            console.error('Erreur dans showAllDiscussions:', error);
            alert('Erreur lors du chargement de la discussion: ' + error.message);
        });
}

function loadDiscussionMessages(activityId) {
    console.log('loadDiscussionMessages appelé avec activityId:', activityId);
    fetch(`/activities/${activityId}/discussions`)
        .then(response => {
            console.log('Response status pour discussions:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(messages => {
            console.log('Messages reçus:', messages);
            const messagesContainer = document.getElementById('discussionMessages');
            if (messagesContainer) {
                messagesContainer.innerHTML = '';
                console.log('Container trouvé, vidage effectué');
            } else {
                console.error('Element discussionMessages non trouvé');
                return;
            }
            
            messages.forEach(message => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `d-flex ${message.sender_id === authUserId ? 'justify-content-end' : 'justify-content-start'} mb-2`;
                
                const bubbleDiv = document.createElement('div');
                bubbleDiv.className = `p-2 rounded ${message.sender_id === authUserId ? 'bg-primary text-white' : 'bg-light'}`;
                bubbleDiv.style.maxWidth = '80%';
                
                bubbleDiv.innerHTML = `
                    <div class="fw-bold small">${message.sender.prenom} ${message.sender.nom}</div>
                    <div>${message.message}</div>
                    <div class="text-muted small">${new Date(message.created_at).toLocaleString()}</div>
                `;
                
                messageDiv.appendChild(bubbleDiv);
                messagesContainer.appendChild(messageDiv);
            });
            
            // Marquer les messages comme lus
            markMessagesAsRead(activityId);
            
            // Scroller en bas
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
}

function sendMessage() {
    console.log('sendMessage appelé');
    const messageInput = document.getElementById('newMessage');
    console.log('messageInput trouvé:', !!messageInput);
    
    if (!messageInput) {
        console.error('Element newMessage non trouvé');
        return;
    }
    
    const message = messageInput.value.trim();
    console.log('message:', message);
    console.log('currentActivityId:', currentActivityId);
    
    if (!message || !currentActivityId) {
        console.error('Message vide ou currentActivityId non défini');
        return;
    }
    
    // Désactiver le bouton pendant l'envoi
    const sendButton = event.target;
    const originalContent = sendButton.innerHTML;
    sendButton.disabled = true;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    console.log('Tentative d\'envoi à /activities/' + currentActivityId + '/discussions');
    
    // Utiliser FormData au lieu de JSON pour éviter les problèmes de validation
    const formData = new FormData();
    formData.append('message', message);
    formData.append('type', 'message');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    const xhr = new XMLHttpRequest();
    xhr.timeout = 10000; // 10 secondes timeout
    
    xhr.onload = function() {
        console.log('XHR status:', xhr.status);
        console.log('XHR response:', xhr.responseText);
        
        // Réactiver le bouton
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
        
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                const data = JSON.parse(xhr.responseText);
                console.log('Response data:', data);
                if (data.success) {
                    messageInput.value = '';
                    loadDiscussionMessages(currentActivityId);
                    console.log('Message envoyé avec succès');
                } else {
                    console.error('Erreur dans la réponse:', data.error);
                    alert('Erreur: ' + (data.error || 'Erreur inconnue'));
                }
            } catch (e) {
                console.error('Erreur parsing JSON:', e);
                messageInput.value = '';
                loadDiscussionMessages(currentActivityId);
                alert('Message envoyé (vérifiez dans la discussion)');
            }
        } else {
            alert('Erreur HTTP: ' + xhr.status);
        }
    };
    
    xhr.onerror = function() {
        console.error('Erreur XHR');
        alert('Erreur de connexion');
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
    };
    
    xhr.ontimeout = function() {
        console.error('Timeout XHR');
        alert('Délai d\'attente dépassé');
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
    };
    
    xhr.open('POST', `/activities/${currentActivityId}/discussions`);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.send(formData);
}

function markMessagesAsRead(activityId) {
    fetch(`/activities/${activityId}/discussions/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
}

// Gérer l'envoi avec Entrée
document.getElementById('newMessage')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

let currentGeneralUserId = null;

function openGeneralDiscussion(userId, userName) {
    console.log('openGeneralDiscussion appelé avec userId:', userId, 'userName:', userName);
    currentGeneralUserId = userId;
    
    const userIdElement = document.getElementById('generalDiscussionUserId');
    const userNameElement = document.getElementById('generalDiscussionUserName');
    
    if (userIdElement) {
        userIdElement.value = userId;
        console.log('generalDiscussionUserId défini:', userId);
    } else {
        console.error('Element generalDiscussionUserId non trouvé');
    }
    
    if (userNameElement) {
        userNameElement.textContent = userName;
        console.log('generalDiscussionUserName défini:', userName);
    } else {
        console.error('Element generalDiscussionUserName non trouvé');
    }
    
    // Charger les messages
    loadGeneralDiscussionMessages(userId);
    
    // Afficher le modal
    const modalElement = document.getElementById('generalDiscussionModal');
    if (modalElement) {
        console.log('Modal général trouvé, tentative d\'affichage');
        new bootstrap.Modal(modalElement).show();
    } else {
        console.error('Modal generalDiscussionModal non trouvé');
    }
}

function loadGeneralDiscussionMessages(userId) {
    fetch(`/discussions/${userId}`)
        .then(response => response.json())
        .then(messages => {
            const messagesContainer = document.getElementById('generalDiscussionMessages');
            messagesContainer.innerHTML = '';
            
            messages.forEach(message => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `d-flex ${message.sender_id === authUserId ? 'justify-content-end' : 'justify-content-start'} mb-2`;
                
                const bubbleDiv = document.createElement('div');
                bubbleDiv.className = `p-2 rounded ${message.sender_id === authUserId ? 'bg-primary text-white' : 'bg-light'}`;
                bubbleDiv.style.maxWidth = '80%';
                
                bubbleDiv.innerHTML = `
                    <div class="fw-bold small">${message.sender.prenom} ${message.sender.nom}</div>
                    <div>${message.message}</div>
                    <div class="text-muted small">${new Date(message.created_at).toLocaleString()}</div>
                `;
                
                messageDiv.appendChild(bubbleDiv);
                messagesContainer.appendChild(messageDiv);
            });
            
            // Marquer les messages comme lus
            markGeneralMessagesAsRead(userId);
            
            // Scroller en bas
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
}

function sendGeneralMessage() {
    const messageInput = document.getElementById('generalNewMessage');
    const message = messageInput.value.trim();
    
    console.log('Tentative d\'envoi de message:', message);
    console.log('ID utilisateur actuel:', currentGeneralUserId);
    
    if (!message || !currentGeneralUserId) {
        console.log('Message vide ou ID utilisateur manquant');
        alert('Veuillez entrer un message');
        return;
    }
    
    // Désactiver le bouton pendant l'envoi
    const sendButton = document.querySelector('#generalDiscussionModal .btn-primary');
    const originalContent = sendButton.innerHTML;
    sendButton.disabled = true;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Créer un FormData au lieu de JSON
    const formData = new FormData();
    formData.append('message', message);
    formData.append('type', 'message');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Utiliser XMLHttpRequest avec timeout
    const xhr = new XMLHttpRequest();
    xhr.timeout = 10000; // 10 secondes timeout
    
    xhr.onload = function() {
        console.log('XHR status:', xhr.status);
        console.log('XHR response:', xhr.responseText);
        
        // Réactiver le bouton
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
        
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data.success) {
                    messageInput.value = '';
                    loadGeneralDiscussionMessages(currentGeneralUserId);
                } else {
                    alert('Erreur: ' + (data.error || 'Erreur inconnue'));
                }
            } catch (e) {
                console.error('Erreur parsing JSON:', e);
                alert('Erreur de réponse du serveur');
            }
        } else {
            alert('Erreur HTTP: ' + xhr.status);
        }
    };
    
    xhr.onerror = function() {
        console.error('Erreur XHR');
        alert('Erreur de connexion');
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
    };
    
    xhr.ontimeout = function() {
        console.error('Timeout XHR');
        alert('Délai d\'attente dépassé');
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
    };
    
    xhr.open('POST', `/discussions/${currentGeneralUserId}`);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.send(formData);
}

function markGeneralMessagesAsRead(userId) {
    fetch(`/discussions/${userId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
}

// Gérer l'envoi avec Entrée pour la discussion générale
document.getElementById('generalNewMessage')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendGeneralMessage();
    }
});

// Fonctions pour le modal de notification d'activité
let currentNotificationActivityId = null;

function openActivityNotification(activityId, message, sender) {
    currentNotificationActivityId = activityId;
    
    // Récupérer les détails de l'activité
    fetch(`/activities/${activityId}`)
        .then(response => response.json())
        .then(activity => {
            document.getElementById('notificationActivityTitle').textContent = activity.titre;
            document.getElementById('notificationMessage').textContent = message || 'Pas de message';
            document.getElementById('notificationSender').textContent = sender ? `De: ${sender}` : '';
            
            // Charger la discussion complète
            loadActivityDiscussionMessages(activityId);
            
            // Ouvrir le modal
            const modal = new bootstrap.Modal(document.getElementById('activityNotificationModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement de l\'activité');
        });
}

function loadActivityDiscussionMessages(activityId) {
    fetch(`/activities/${activityId}/discussions`)
        .then(response => response.json())
        .then(messages => {
            const messagesContainer = document.getElementById('activityDiscussionMessages');
            messagesContainer.innerHTML = '';
            
            messages.forEach((message, index) => {
                console.log(`Traitement message ${index}:`, message);
                const messageDiv = document.createElement('div');
                messageDiv.className = `d-flex ${message.sender_id === authUserId ? 'justify-content-end' : 'justify-content-start'} mb-2`;
                
                const bubbleDiv = document.createElement('div');
                bubbleDiv.className = `p-2 rounded ${message.sender_id === authUserId ? 'bg-primary text-white' : 'bg-light'}`;
                bubbleDiv.style.maxWidth = '80%';
                
                bubbleDiv.innerHTML = `
                    <div class="fw-bold small">${message.sender.prenom} ${message.sender.nom}</div>
                    <div>${message.message}</div>
                    <div class="text-muted tiny">${message.created_at}</div>
                `;
                
                messageDiv.appendChild(bubbleDiv);
                messagesContainer.appendChild(messageDiv);
            });
            
            // Scroller en bas
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            console.log('Messages chargés et scroll effectué');
        })
        .catch(error => {
            console.error('Erreur dans loadActivityDiscussionMessages:', error);
            alert('Erreur lors du chargement des messages: ' + error.message);
        });
}

function sendActivityNotificationReply() {
    const messageInput = document.getElementById('activityNotificationReply');
    const message = messageInput.value.trim();
    
    if (!message) {
        alert('Veuillez entrer un message');
        return;
    }
    
    // Désactiver le bouton pendant l'envoi
    const sendButton = event.target;
    const originalContent = sendButton.innerHTML;
    sendButton.disabled = true;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
    
    // Utiliser la route de secours ultra-simple
    const formData = new FormData();
    formData.append('message', message);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    const xhr = new XMLHttpRequest();
    xhr.timeout = 5000;
    
    xhr.onload = function() {
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
        
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data.success) {
                    messageInput.value = '';
                    // Recharger les messages
                    loadActivityDiscussionMessages(currentNotificationActivityId);
                    alert('Message envoyé avec succès!');
                } else {
                    alert('Erreur: ' + (data.error || 'Erreur inconnue'));
                }
            } catch (e) {
                console.error('Erreur parsing JSON:', e);
                messageInput.value = '';
                loadActivityDiscussionMessages(currentNotificationActivityId);
                alert('Message envoyé (vérifiez dans la discussion)');
            }
        } else {
            alert('Erreur HTTP: ' + xhr.status);
        }
    };
    
    xhr.onerror = function() {
        console.error('Erreur XHR');
        alert('Erreur de connexion');
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
    };
    
    xhr.ontimeout = function() {
        console.error('Timeout XHR');
        alert('Délai d\'attente dépassé');
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
    };
    
    xhr.open('POST', `/activities/${currentNotificationActivityId}/discussions`);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.send(formData);
}

function markActivityNotificationAsRead() {
    if (currentNotificationActivityId) {
        markActivityMessagesAsRead(currentNotificationActivityId);
        
        // Fermer le modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('activityNotificationModal'));
        modal.hide();
    }
}

// Fonctions pour les discussions sous les activités
function toggleActivityDiscussion(activityId) {
    const discussionDiv = document.getElementById(`discussion-${activityId}`);
    const chevron = document.getElementById(`chevron-${activityId}`);
    
    if (discussionDiv.style.display === 'none') {
        discussionDiv.style.display = 'block';
        chevron.classList.remove('fa-chevron-down');
        chevron.classList.add('fa-chevron-up');
        
        // Marquer les messages comme lus
        markActivityMessagesAsRead(activityId);
    } else {
        discussionDiv.style.display = 'none';
        chevron.classList.remove('fa-chevron-up');
        chevron.classList.add('fa-chevron-down');
    }
}

function openActivityWithDiscussion(activityId, activityTitle) {
    // Fermer la notification si elle est dans une alerte
    const alert = event.target.closest('.alert');
    if (alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }
    
    // Scroller vers l'activité dans le tableau
    const activityRow = document.querySelector(`tr:has(#discussion-${activityId})`);
    if (activityRow) {
        activityRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Mettre en surbrillance temporairement
        activityRow.style.backgroundColor = '#fff3cd';
        setTimeout(() => {
            activityRow.style.backgroundColor = '';
        }, 2000);
    }
    
    // Ouvrir la discussion de l'activité
    setTimeout(() => {
        toggleActivityDiscussion(activityId);
    }, 500);
    
    // Marquer les messages comme lus
    markActivityMessagesAsRead(activityId);
}

function sendQuickReply(activityId, stagiaireName) {
    const messageInput = document.getElementById(`quickMessage-${activityId}`);
    const message = messageInput.value.trim();
    
    if (!message) {
        alert('Veuillez entrer un message');
        return;
    }
    
    // Désactiver le bouton pendant l'envoi
    const sendButton = event.target;
    const originalContent = sendButton.innerHTML;
    sendButton.disabled = true;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Utiliser la route de secours ultra-simple
    const formData = new FormData();
    formData.append('message', message);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    const xhr = new XMLHttpRequest();
    xhr.timeout = 5000;
    
    xhr.onload = function() {
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
        
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data.success) {
                    messageInput.value = '';
                    // Recharger les discussions de cette activité
                    reloadActivityDiscussion(activityId);
                    alert('Message envoyé avec succès!');
                } else {
                    alert('Erreur: ' + (data.error || 'Erreur inconnue'));
                }
            } catch (e) {
                console.error('Erreur parsing JSON:', e);
                messageInput.value = '';
                reloadActivityDiscussion(activityId);
                alert('Message envoyé (vérifiez dans la discussion)');
            }
        } else {
            alert('Erreur HTTP: ' + xhr.status);
        }
    };
    
    xhr.onerror = function() {
        console.error('Erreur XHR');
        alert('Erreur de connexion');
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
    };
    
    xhr.ontimeout = function() {
        console.error('Timeout XHR');
        alert('Délai d\'attente dépassé');
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
    };
    
    xhr.open('POST', `/activities/${activityId}/discussions`);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.send(formData);
}

function reloadActivityDiscussion(activityId) {
    // Recharger la page pour mettre à jour les discussions
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function quickReplyFromNotification(activityId, stagiaireName) {
    // Fermer la notification
    const alert = event.target.closest('.alert');
    if (alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }
    
    // Ouvrir l'activité avec discussion
    openActivityWithDiscussion(activityId, '');
    
    // Focus sur le champ de réponse rapide
    setTimeout(() => {
        const messageInput = document.getElementById(`quickMessage-${activityId}`);
        if (messageInput) {
            messageInput.focus();
        }
    }, 1000);
}

function markActivityMessagesAsRead(activityId) {
    fetch(`/activities/${activityId}/discussions/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour le compteur de notifications
            updateNotificationCounter();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

function quickReply(activityId, stagiaireName) {
    // Ouvrir le modal de discussion générale avec le stagiaire
    if (stagiaireName) {
        // Trouver l'ID du stagiaire depuis l'activité
        fetch(`/activities/${activityId}`)
            .then(response => response.json())
            .then(activity => {
                if (activity.stagiaire) {
                    openGeneralDiscussion(activity.stagiaire.id, stagiaireName);
                }
            });
    }
}

// Fonctions pour les notifications
function showActivityDiscussion(activityId) {
    // Fermer d'abord la notification si elle est dans une alerte
    const alert = event.target.closest('.alert');
    if (alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }
    
    // Ouvrir la discussion de l'activité
    showAllDiscussions(activityId);
}

function markNotificationAsRead(notificationId, buttonElement) {
    fetch(`/discussions/mark-read/${notificationId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer l'alerte
            const alert = buttonElement.closest('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
            
            // Mettre à jour le compteur de notifications
            updateNotificationCounter();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du marquage comme lu');
    });
}

function updateNotificationCounter() {
    // Recharger la page pour mettre à jour le compteur
    setTimeout(() => {
        location.reload();
    }, 500);
}

function validerActivite(activityId) {
    if (confirm('Êtes-vous sûr de vouloir valider cette activité ?')) {
        fetch(`/activities/${activityId}/valider`, {method: 'POST'})
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
    }
}

function refuserActivite(activityId) {
    const justification = prompt('Justification du refus :');
    if (justification) {
        fetch(`/activities/${activityId}/refuser`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({justification})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function evaluerActivite(activityId) {
    document.getElementById('evaluationActivityId').value = activityId;
    new bootstrap.Modal(document.getElementById('evaluationModal')).show();
}

function finaliserEvaluation(evaluationId) {
    if (confirm('Finaliser cette évaluation ?')) {
        fetch(`/evaluations/${evaluationId}/finaliser`, {method: 'POST'})
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
    }
}

function validerEvaluation(evaluationId) {
    if (confirm('Valider cette évaluation ?')) {
        fetch(`/evaluations/${evaluationId}/valider`, {method: 'POST'})
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
    }
}

function supprimerDocument(documentId) {
    if (confirm('Supprimer ce document ?')) {
        fetch(`/documents/${documentId}`, {
            method: 'DELETE',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
@endsection
