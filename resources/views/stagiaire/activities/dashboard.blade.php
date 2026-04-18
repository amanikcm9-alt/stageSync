@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-tachometer-alt text-primary"></i> 
                Tableau de Bord Stagiaire
            </h2>
            <small class="text-muted">
                Bienvenue, {{ auth()->user()->prenom }} {{ auth()->user()->nom }}
            </small>
        </div>
        <div>
            <a href="{{ route('activities.proposer') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Proposer une activité
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-3">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-primary mb-1">{{ $stats['total'] }}</h5>
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
                    <h5 class="text-danger mb-1">{{ $stats['en_retard'] }}</h5>
                    <small class="text-muted">En retard</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-secondary mb-1">{{ $stats['evaluations'] }}</h5>
                    <small class="text-muted">Évaluations</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Mes Activités -->
    <div class="card border-0 shadow-sm mb-1">
        <div class="card-header bg-info text-white py-1">
            <h6 class="mb-0 small">
                <i class="fas fa-tasks me-1"></i> Mes Activités
            </h6>
        </div>
        <div class="card-body py-2">
            <!-- Onglets -->
            <ul class="nav nav-tabs mb-2" id="activitiesTabs">
                <li class="nav-item">
                    <a class="nav-link active small" data-bs-toggle="tab" href="#assignees">
                        Activités assignées ({{ $activities->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link small" data-bs-toggle="tab" href="#proposed">
                        Activités proposées ({{ $proposedActivities->count() }})
                    </a>
                </li>
            </ul>

            <!-- Contenu des onglets -->
            <div class="tab-content">
                <!-- Activités assignées -->
                <div class="tab-pane fade show active" id="assignees">
                    @if($activities->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="small">Activité</th>
                                    <th class="small">Priorité</th>
                                    <th class="small">Statut</th>
                                    <th class="small">Progression</th>
                                    <th class="small">Date limite</th>
                                    <th class="small">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $activity)
                                <tr>
                                    <td class="small">
                                        <div class="fw-bold small">{{ $activity->titre }}</div>
                                        <small class="text-muted">{{ $activity->encadrant->nom }} {{ $activity->encadrant->prenom }}</small>
                                    </td>
                                    <td class="small">
                                        <span class="badge bg-{{ $activity->priorite_color }} text-white small">
                                            {{ $activity->priorite_label }}
                                        </span>
                                    </td>
                                    <td class="small">
                                        <span class="badge bg-{{ $activity->statut_color }} text-white small">
                                            {{ $activity->statut_label }}
                                        </span>
                                    </td>
                                    <td class="small">
                                        <div class="progress" style="height: 15px;">
                                            <div class="progress-bar bg-{{ $activity->progression >= 80 ? 'success' : ($activity->progression >= 50 ? 'warning' : 'info') }} small" 
                                                 role="progressbar" 
                                                 style="width: {{ $activity->progression }}%">
                                                {{ $activity->progression }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small">
                                        @if($activity->date_limite)
                                            <small class="{{ $activity->estEnRetard() ? 'text-danger' : 'text-muted' }}">
                                                {{ $activity->date_limite->format('d/m/Y') }}
                                                @if($activity->estEnRetard())
                                                    <i class="fas fa-exclamation-triangle small"></i>
                                                @endif
                                            </small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td class="small">
                                        <div class="btn-group btn-group-sm">
                                            <!-- Consulter les supports -->
                                            @if($activity->documents->count() > 0)
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="showSupports({{ $activity->id }})">
                                                <i class="fas fa-book small"></i>
                                            </button>
                                            @endif
                                            
                                            <!-- Réaliser activité -->
                                            @if($activity->statut === 'assignee')
                                            <a href="{{ route('activities.realiser', $activity) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-play small"></i>
                                            </a>
                                            @endif
                                            
                                            <!-- Soumettre -->
                                            @if($activity->statut === 'en_cours')
                                            <a href="{{ route('submissions.create', $activity) }}" class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-upload small"></i>
                                            </a>
                                            @endif
                                            
                                            <!-- Refuser -->
                                            @if(in_array($activity->statut, ['assignee', 'en_cours']))
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="refuserActivite({{ $activity->id }})">
                                                <i class="fas fa-times small"></i>
                                            </button>
                                            @endif
                                            
                                            <!-- Demander info -->
                                            @if(in_array($activity->statut, ['assignee', 'en_cours']))
                                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="demanderInfo({{ $activity->id }})">
                                                <i class="fas fa-question small"></i>
                                            </button>
                                            @endif
                                            
                                            <!-- Détails -->
                                            <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-eye small"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <h6 class="text-muted small">Aucune activité assignée</h6>
                        <p class="text-muted small">Vous n'avez pas encore d'activités à réaliser.</p>
                    </div>
                    @endif
                </div>

                <!-- Activités proposées -->
                <div class="tab-pane fade" id="proposed">
                    @if($proposedActivities->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="small">Activité</th>
                                    <th class="small">Encadrant</th>
                                    <th class="small">Priorité</th>
                                    <th class="small">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proposedActivities as $activity)
                                <tr class="small">
                                    <td class="small">
                                        <div class="fw-bold small">{{ $activity->titre }}</div>
                                        <small class="text-muted">{{ Str::limit($activity->description, 80) }}</small>
                                    </td>
                                    <td class="small">
                                        <span class="badge bg-primary text-white small">
                                            {{ $activity->encadrant->prenom }} {{ $activity->encadrant->nom }}
                                        </span>
                                    </td>
                                    <td class="small">
                                        <span class="badge bg-{{ $activity->priorite_color }} text-white small">
                                            {{ $activity->priorite_label }}
                                        </span>
                                    </td>
                                    <td class="small">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-success btn-sm small" onclick="accepterActivite({{ $activity->id }})">
                                                <i class="fas fa-check small"></i> Accepter
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm small" onclick="demanderInfoProposee({{ $activity->id }})">
                                                <i class="fas fa-question small"></i> Info
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm small" onclick="refuserActiviteProposee({{ $activity->id }})">
                                                <i class="fas fa-times small"></i> Refuser
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="fas fa-lightbulb fa-2x text-muted mb-2"></i>
                        <h6 class="text-muted small">Aucune activité proposée</h6>
                        <p class="text-muted small">Vos encadrants n'ont pas encore proposé d'activités.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Section Évaluations -->
    <div class="card border-0 shadow-sm mb-1">
        <div class="card-header bg-success text-white py-1">
            <h6 class="mb-0 small">
                <i class="fas fa-star me-1"></i> Mes Évaluations
            </h6>
        </div>
        <div class="card-body py-2">
            @if($evaluations->count() > 0)
            <div class="row">
                @foreach($evaluations->take(3) as $evaluation)
                <div class="col-md-4 mb-2">
                    <div class="card border-0 shadow-sm h-100 small">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="card-title mb-0 small">{{ $evaluation->type_label }}</h6>
                                <span class="badge bg-{{ $evaluation->statut_color }} text-white small">
                                    {{ $evaluation->statut_label }}
                                </span>
                            </div>
                            @if($evaluation->note_globale)
                            <div class="text-center mb-1">
                                <div class="fs-5 small">{{ $evaluation->note_globale }}/20</div>
                                <div class="small">{!! $evaluation->note_etoiles !!}</div>
                            </div>
                            @endif
                            <p class="card-text small text-muted mb-1">{{ $evaluation->appreciation_courte }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted small">
                                    <i class="fas fa-user small"></i> {{ $evaluation->encadrant->prenom }} {{ $evaluation->encadrant->nom }}
                                </small>
                                <a href="{{ route('evaluations.show', $evaluation) }}" class="btn btn-sm btn-outline-primary btn-sm small">
                                    <i class="fas fa-eye small"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($evaluations->count() > 3)
            <div class="text-center mt-2">
                <a href="{{ route('evaluations.index') }}" class="btn btn-outline-success btn-sm small">
                    Voir toutes les évaluations
                </a>
            </div>
            @endif
            @else
            <div class="text-center py-3">
                <i class="fas fa-clipboard-check fa-2x text-muted mb-2"></i>
                <h6 class="text-muted small">Aucune évaluation</h6>
                <p class="text-muted small">Vous n'avez pas encore reçu d'évaluations.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Documents et Supports -->
    @if($documents->count() > 0)
    <div class="card border-0 shadow-sm mb-1">
        <div class="card-header bg-warning text-white py-1">
            <h6 class="mb-0 small">
                <i class="fas fa-book me-1"></i> Documents et Supports
            </h6>
        </div>
        <div class="card-body py-2">
            <div class="row">
                @foreach($documents->take(6) as $document)
                <div class="col-md-4 mb-1">
                    <div class="card border-0 shadow-sm h-100 small">
                        <div class="card-body p-1">
                            <div class="d-flex align-items-center">
                                <i class="fas {{ $document->type_icon }} text-{{ $document->type_color }} me-1 small"></i>
                                <div class="flex-grow-1">
                                    <small class="fw-bold small">{{ $document->titre }}</small>
                                    <br>
                                    <small class="text-muted small">{{ $document->type_label }}</small>
                                </div>
                                <a href="{{ $document->lien }}" target="_blank" class="btn btn-sm btn-outline-primary btn-sm small">
                                    <i class="fas fa-eye small"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($documents->count() > 6)
            <div class="text-center mt-1">
                <a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-primary btn-sm small">
                    Voir tous les documents
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- Section Encadrant -->
@if(auth()->user()->encadrant)
<div class="card border-0 shadow-sm mb-1">
    <div class="card-header bg-primary text-white py-1">
        <h6 class="mb-0 small">
            <i class="fas fa-user-tie me-1"></i> Mon Encadrant
        </h6>
    </div>
    <div class="card-body py-2">
        <div class="d-flex align-items-center">
            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                {{ strtoupper(substr(auth()->user()->encadrant->prenom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->encadrant->nom, 0, 1)) }}
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold">{{ auth()->user()->encadrant->prenom }} {{ auth()->user()->encadrant->nom }}</div>
                <small class="text-muted">{{ auth()->user()->encadrant->email }}</small>
                <div class="mt-1">
                    <span class="badge bg-info text-white small">
                        {{ auth()->user()->activities->count() }} activités suivies
                    </span>
                </div>
            </div>
            <div class="ms-3">
                <button type="button" class="btn btn-primary btn-sm" onclick="openGeneralDiscussion({{ auth()->user()->encadrant->id }}, '{{ auth()->user()->encadrant->prenom }} {{ auth()->user()->encadrant->nom }}')" title="Discuter avec mon encadrant">
                    <i class="fas fa-comments"></i>
                    @php
                        $unreadCount = \App\Models\Discussion::where('receiver_id', auth()->id())
                            ->where('sender_id', auth()->user()->encadrant_id)
                            ->where('read', false)
                            ->count();
                    @endphp
                    @if($unreadCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $unreadCount }}
                        <span class="visually-hidden">messages non lus</span>
                    </span>
                    @endif
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Section Discussions -->
<div class="card border-0 shadow-sm mb-1">
    <div class="card-header bg-info text-white py-1">
        <h6 class="mb-0 small">
            <i class="fas fa-comments me-1"></i> Discussions avec l'encadrant
        </h6>
    </div>
    <div class="card-body py-2">
        @if(auth()->user()->unreadDiscussions()->count() > 0)
        <div class="alert alert-info small py-2">
            <i class="fas fa-bell me-1"></i> 
            {{ auth()->user()->unreadDiscussions()->count() }} message(s) non lu(s)
        </div>
        @endif
        
        @foreach($activities as $activity)
            @if($activity->discussions->count() > 0)
            <div class="border rounded p-2 mb-2 small">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="mb-0 small">{{ $activity->titre }}</h6>
                    <span class="badge bg-info small">{{ $activity->discussions->count() }}</span>
                </div>
                <div class="discussion-messages">
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
                <div class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-info small" onclick="showAllDiscussions({{ $activity->id }})">
                        <i class="fas fa-comments small"></i> Voir toute la discussion
                    </button>
                </div>
                @endif
            </div>
            @endif
        @endforeach
        
        @if($activities->pluck('discussions')->flatten()->isEmpty())
        <div class="text-center py-2">
            <i class="fas fa-comments fa-2x text-muted mb-1"></i>
            <p class="text-muted small">Aucune discussion en cours</p>
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

<!-- Modal Supports -->
<div class="modal fade" id="supportsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supports de l'activité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="supportsContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Refus -->
<div class="modal fade" id="refusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Refuser l'activité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="refusForm">
                @csrf
                <input type="hidden" name="activity_id" id="refusActivityId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="justification" class="form-label">Justification du refus *</label>
                        <textarea class="form-control" id="justification" name="justification" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Refuser l'activité</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Demande d'information -->
<div class="modal fade" id="infoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Demander des informations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="infoForm">
                @csrf
                <input type="hidden" name="activity_id" id="infoActivityId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="message" class="form-label">Votre question *</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Envoyer la demande</button>
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
function showSupports(activityId) {
    fetch(`/activities/${activityId}/supports`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('supportsContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('supportsModal')).show();
        });
}

let currentActivityId = null;

function showAllDiscussions(activityId) {
    currentActivityId = activityId;
    
    // Récupérer les détails de l'activité
    fetch(`/activities/${activityId}`)
        .then(response => response.json())
        .then(activity => {
            document.getElementById('discussionActivityTitle').textContent = activity.titre;
            
            // Charger les messages
            loadDiscussionMessages(activityId);
            
            // Afficher le modal
            new bootstrap.Modal(document.getElementById('discussionModal')).show();
        });
}

function loadDiscussionMessages(activityId) {
    fetch(`/activities/${activityId}/discussions`)
        .then(response => response.json())
        .then(messages => {
            const messagesContainer = document.getElementById('discussionMessages');
            messagesContainer.innerHTML = '';
            
            messages.forEach(message => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `d-flex ${message.sender_id === {{ auth()->id() }} ? 'justify-content-end' : 'justify-content-start'} mb-2`;
                
                const bubbleDiv = document.createElement('div');
                bubbleDiv.className = `p-2 rounded ${message.sender_id === {{ auth()->id() }} ? 'bg-primary text-white' : 'bg-light'}`;
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
    const messageInput = document.getElementById('newMessage');
    const message = messageInput.value.trim();
    
    if (!message || !currentActivityId) return;
    
    fetch(`/activities/${currentActivityId}/discussions`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            message: message,
            type: 'message'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            loadDiscussionMessages(currentActivityId);
        }
    });
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
    currentGeneralUserId = userId;
    document.getElementById('generalDiscussionUserId').value = userId;
    document.getElementById('generalDiscussionUserName').textContent = userName;
    
    // Charger les messages
    loadGeneralDiscussionMessages(userId);
    
    // Afficher le modal
    new bootstrap.Modal(document.getElementById('generalDiscussionModal')).show();
}

function loadGeneralDiscussionMessages(userId) {
    fetch(`/discussions/${userId}`)
        .then(response => response.json())
        .then(messages => {
            const messagesContainer = document.getElementById('generalDiscussionMessages');
            messagesContainer.innerHTML = '';
            
            messages.forEach(message => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `d-flex ${message.sender_id === {{ auth()->id() }} ? 'justify-content-end' : 'justify-content-start'} mb-2`;
                
                const bubbleDiv = document.createElement('div');
                bubbleDiv.className = `p-2 rounded ${message.sender_id === {{ auth()->id() }} ? 'bg-primary text-white' : 'bg-light'}`;
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
    
    console.log('Tentative d\'envoi de message (simple):', message);
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
    
    // Utiliser la route de secours ultra-simple
    const formData = new FormData();
    formData.append('message', message);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    const xhr = new XMLHttpRequest();
    xhr.timeout = 5000; // 5 secondes timeout
    
    xhr.onload = function() {
        console.log('XHR status (simple):', xhr.status);
        console.log('XHR response (simple):', xhr.responseText);
        
        // Réactiver le bouton
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
        
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data.success) {
                    messageInput.value = '';
                    loadGeneralDiscussionMessages(currentGeneralUserId);
                    alert('Message envoyé avec succès!');
                } else {
                    alert('Erreur: ' + (data.error || 'Erreur inconnue'));
                }
            } catch (e) {
                console.error('Erreur parsing JSON:', e);
                alert('Message envoyé (vérifiez dans la discussion)');
                loadGeneralDiscussionMessages(currentGeneralUserId);
            }
        } else {
            alert('Erreur HTTP: ' + xhr.status);
        }
    };
    
    xhr.onerror = function() {
        console.error('Erreur XHR (simple)');
        alert('Erreur de connexion');
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
    };
    
    xhr.ontimeout = function() {
        console.error('Timeout XHR (simple)');
        alert('Délai d\'attente dépassé');
        sendButton.disabled = false;
        sendButton.innerHTML = originalContent;
    };
    
    xhr.open('POST', `/simple-discussion/${currentGeneralUserId}`);
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

function refuserActivite(activityId) {
    document.getElementById('refusActivityId').value = activityId;
    document.getElementById('refusForm').action = `/activities/${activityId}/refuser`;
    new bootstrap.Modal(document.getElementById('refusModal')).show();
}

function demanderInfo(activityId) {
    document.getElementById('infoActivityId').value = activityId;
    document.getElementById('infoForm').action = `/activities/${activityId}/demander-info`;
    new bootstrap.Modal(document.getElementById('infoModal')).show();
}

function accepterActivite(activityId) {
    if (confirm('Voulez-vous accepter cette activité ?')) {
        fetch(`/activities/${activityId}/assigner`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({stagiaire_id: {{ auth()->user()->id }}})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function refuserActiviteProposee(activityId) {
    const justification = prompt('Pourquoi refusez-vous cette activité ?');
    if (justification) {
        fetch(`/activities/${activityId}/refuser`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
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

function demanderInfoProposee(activityId) {
    const message = prompt('Quelle information souhaitez-vous demander ?');
    if (message) {
        fetch(`/activities/${activityId}/demander-info`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({message})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Votre demande a été envoyée à l\'encadrant');
            }
        });
    }
}

function sauvegarderPlanning() {
    const planning = document.getElementById('planning').value;
    
    fetch('/stagiaire/planning', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({planning})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Votre planning a été sauvegardé avec succès !');
        } else {
            alert('Erreur lors de la sauvegarde du planning.');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la sauvegarde du planning.');
    });
}
</script>
@endsection
