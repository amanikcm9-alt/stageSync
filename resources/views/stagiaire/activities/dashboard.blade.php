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

    <!-- Navigation rapide -->
    <div class="row mb-3">
        <div class="col-md-3">
            <a href="{{ route('stagiaire.activities.index') }}" class="card border-0 shadow-sm text-decoration-none">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-tasks fa-2x"></i>
                    </div>
                    <h5 class="card-title">Mes Activités</h5>
                    <p class="card-text text-muted small">Gérez vos activités et livrables</p>
                    <span class="badge bg-primary">{{ $stats['total'] }} activités</span>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('stagiaire.evaluations.index') }}" class="card border-0 shadow-sm text-decoration-none">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-star fa-2x"></i>
                    </div>
                    <h5 class="card-title">Mes Évaluations</h5>
                    <p class="card-text text-muted small">Consultez vos évaluations et feedbacks</p>
                    <span class="badge bg-success">{{ $stats['evaluations'] }} évaluations</span>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm cursor-pointer" onclick="toggleEncadrants()">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                    </div>
                    <h5 class="card-title">Mes Encadrants</h5>
                    <p class="card-text text-muted small">Vos encadrants et superviseurs</p>
                    <span class="badge bg-warning">{{ $encadrants->count() }} encadrants</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm cursor-pointer" onclick="toggleOffre()">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-briefcase fa-2x"></i>
                    </div>
                    <h5 class="card-title">Mon Offre</h5>
                    <p class="card-text text-muted small">Détails de votre offre de stage</p>
                    <span class="badge bg-info">{{ $offreStage ? 'Assignée' : 'Non assignée' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Notifications -->
    <div class="card border-0 shadow-sm mb-3 notifications-section">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0">
                <i class="fas fa-bell"></i> Notifications Récentes
                @if(is_array($notifications) && count($notifications) > 0)
                <span class="badge bg-danger ms-2">{{ count($notifications) }}</span>
                @endif
            </h6>
        </div>
        <div class="card-body">
            @if(is_array($notifications) && count($notifications) > 0)
                <div class="row">
                    @foreach($notifications->take(6) as $notification)
                    <div class="col-md-6 mb-2">
                        <div class="card border-0 shadow-sm h-100 notification-card" data-notification-id="{{ $notification->id }}">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-start">
                                    <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                        {{ strtoupper(substr($notification->sender->prenom, 0, 1)) }}{{ strtoupper(substr($notification->sender->nom, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold tiny">{{ $notification->sender->prenom }} {{ $notification->sender->nom }}</div>
                                        <div class="tiny text-muted mb-1">{{ $notification->message }}</div>
                                        @if($notification->activity)
                                        <div class="tiny">
                                            <span class="badge bg-primary">Activité: {{ $notification->activity->titre }}</span>
                                        </div>
                                        @endif
                                        <div class="tiny text-muted mt-1">
                                            <i class="fas fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="ms-2">
                                        @if(!$notification->read)
                                        <span class="badge bg-danger rounded-circle p-1" style="width: 8px; height: 8px;"></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($notifications->count() > 6)
                <div class="text-center mt-2">
                    <a href="#" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye"></i> Voir toutes les notifications
                    </a>
                </div>
                @endif
            @else
                <div class="text-center py-3">
                    <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                    <h6 class="text-muted small">Aucune notification</h6>
                    <p class="text-muted small">Vous n'avez pas reçu de nouvelles notifications.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Section Mes Encadrants -->
    <div id="encadrants-section" style="display: none;">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0">
                    <i class="fas fa-chalkboard-teacher"></i> Mes Encadrants
                    <button type="button" class="btn btn-sm btn-outline-light float-end" onclick="toggleEncadrants()">
                        <i class="fas fa-times"></i>
                    </button>
                </h6>
            </div>
            <div class="card-body">
                @if($encadrants->count() > 0)
                <div class="row">
                    @foreach($encadrants as $encadrant)
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        {{ strtoupper(substr($encadrant->prenom, 0, 1)) }}{{ strtoupper(substr($encadrant->nom, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">{{ $encadrant->prenom }} {{ $encadrant->nom }}</h6>
                                        <small class="text-muted">{{ $encadrant->email }}</small>
                                        @if($encadrant->role)
                                        <div class="mt-1">
                                            <small class="badge bg-info">{{ $encadrant->role->name }}</small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="discuterAvecEncadrant({{ $encadrant->id }}, '{{ $encadrant->prenom }} {{ $encadrant->nom }}')">
                                        <i class="fas fa-comments"></i> Discuter
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="envoyerMessageEncadrant({{ $encadrant->id }}, '{{ $encadrant->prenom }} {{ $encadrant->nom }}')">
                                        <i class="fas fa-envelope"></i> Message
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Aucun encadrant assigné</h6>
                    <p class="text-muted small">Vous n'avez pas encore d'encadrants assignés.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Section Mon Offre -->
    <div id="offre-section" style="display: none;">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-briefcase"></i> Mon Offre de Stage
                    <button type="button" class="btn btn-sm btn-outline-light float-end" onclick="toggleOffre()">
                        <i class="fas fa-times"></i>
                    </button>
                </h6>
            </div>
            <div class="card-body">
                <!-- Débogage visible -->
                <div class="alert alert-warning alert-sm mb-3">
                    <small><strong>Débogage Offre:</strong></small><br>
                    <small>Stagiaire: {{ auth()->user()->prenom }} {{ auth()->user()->nom }}</small><br>
                    <small>offre_stage_id: {{ auth()->user()->offre_stage_id ?? 'NULL' }}</small><br>
                    <small>OffreStage objet: {{ $offreStage ? 'EXISTS' : 'NULL' }}</small><br>
                    @if($offreStage)
                    <small class="text-success">Offre trouvée: {{ $offreStage->titre }}</small><br>
                    @endif
                </div>
                
                @if($offreStage)
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <h5 class="text-primary">{{ $offreStage->titre }}</h5>
                            @if($offreStage->entreprise)
                            <p class="text-muted"><i class="fas fa-building me-2"></i>{{ $offreStage->entreprise->nom }}</p>
                            @endif
                        </div>
                        
                        @if($offreStage->description)
                        <div class="mb-3">
                            <h6 class="text-muted">Description</h6>
                            <p class="text-muted">{{ $offreStage->description }}</p>
                        </div>
                        @endif
                        
                        @if($offreStage->missions)
                        <div class="mb-3">
                            <h6 class="text-muted">Missions</h6>
                            <p class="text-muted">{{ $offreStage->missions }}</p>
                        </div>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt text-info me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Période</small>
                                        <strong>{{ $offreStage->date_debut ? $offreStage->date_debut->format('d/m/Y') : 'Non définie' }} - {{ $offreStage->date_fin ? $offreStage->date_fin->format('d/m/Y') : 'Non définie' }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Durée</small>
                                        <strong>{{ $offreStage->duree_semaines }} semaines</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($offreStage->remuneration)
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-euro-sign text-success me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Rémunération</small>
                                    <strong>{{ number_format($offreStage->remuneration, 2) }} €</strong>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-3">Informations complémentaires</h6>
                                
                                @if($offreStage->secteur)
                                <div class="mb-2">
                                    <small class="text-muted">Secteur:</small><br>
                                    <strong>{{ $offreStage->secteur }}</strong>
                                </div>
                                @endif
                                
                                @if($offreStage->lieu)
                                <div class="mb-2">
                                    <small class="text-muted">Lieu:</small><br>
                                    <strong>{{ $offreStage->lieu }}</strong>
                                </div>
                                @endif
                                
                                @if($offreStage->type_stage)
                                <div class="mb-2">
                                    <small class="text-muted">Type:</small><br>
                                    <strong>{{ $offreStage->type_stage }}</strong>
                                </div>
                                @endif
                                
                                <div class="mt-3">
                                    <small class="badge bg-{{ $offreStage->statut === 'publié' ? 'success' : 'warning' }}">
                                        {{ ucfirst($offreStage->statut) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Aucune offre de stage assignée</h6>
                    <p class="text-muted small">Vous n'avez pas encore d'offre de stage assignée.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Notifications -->
    @if(is_array($notifications) && count($notifications) > 0)
    <div class="card border-0 shadow-sm mb-3 notifications-section">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0">
                <i class="fas fa-bell me-2"></i> Notifications
                <span class="badge bg-light text-info ms-2">{{ count($notifications) }}</span>
            </h6>
        </div>
        <div class="card-body">
            @foreach($notifications as $notification)
            <div class="alert alert-info alert-sm py-2 mb-2">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <small class="fw-bold">
                            <i class="fas fa-user me-1"></i>
                            {{ $notification->sender->prenom }} {{ $notification->sender->nom }}
                        </small>
                        @if($notification->activity)
                        <small class="text-muted ms-2">· {{ $notification->activity->titre }}</small>
                        @endif
                        <br>
                        <small>{{ $notification->message }}</small>
                        <br>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    <a href="{{ $notification->activity ? route('activities.show', $notification->activity) : '#' }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            @endforeach
            <div class="text-center mt-2">
                <a href="#" class="btn btn-sm btn-outline-info" onclick="marquerToutesLues()">
                    <i class="fas fa-check me-1"></i>Marquer toutes comme lues
                </a>
            </div>
        </div>
    </div>
    @endif

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

// Fonctions pour les actions sur les activités
function accepterActivite(activityId) {
    if (confirm('Êtes-vous sûr de vouloir accepter cette activité ?')) {
        fetch(`/activities/${activityId}/accepter`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'acceptation de l\'activité.');
        });
    }
}

function refuserActiviteProposee(activityId) {
    const raison = prompt('Veuillez indiquer la raison du refus :');
    if (raison === null) return;
    
    fetch(`/activities/${activityId}/refuser`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({raison: raison})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du refus de l\'activité.');
    });
}

function demanderInfoProposee(activityId) {
    const question = prompt('Quelle information souhaitez-vous demander ?');
    if (question === null) return;
    
    fetch(`/activities/${activityId}/demander-info`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({question: question})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'envoi de la question.');
    });
}

function soumettreLivrable(activityId) {
    const commentaire = prompt('Ajoutez un commentaire sur votre livrable :');
    if (commentaire === null) return;
    
    // Créer un input de fichier caché
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '*/*';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('commentaire', commentaire);
            formData.append('fichier', file);
            
            fetch(`/activities/${activityId}/soumettre-livrable`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Erreur: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la soumission du livrable.');
            });
        }
    };
    input.click();
}

function discuterActivite(activityId) {
    const message = prompt('Votre message pour l\'encadrant :');
    if (message === null) return;
    
    fetch(`/activities/${activityId}/discuter`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({message: message})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'envoi du message.');
    });
}

// Fonctions pour les modals
let currentActivityId = null;

function confirmerRefus() {
    const raison = document.getElementById('raisonRefus').value;
    if (!raison.trim()) {
        alert('Veuillez indiquer la raison du refus.');
        return;
    }
    
    fetch(`/activities/${currentActivityId}/refuser`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({raison: raison})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('refusModal')).hide();
            window.location.reload();
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du refus de l\'activité.');
    });
}

function envoyerQuestion() {
    const question = document.getElementById('questionInfo').value;
    if (!question.trim()) {
        alert('Veuillez poser votre question.');
        return;
    }
    
    fetch(`/activities/${currentActivityId}/demander-info`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({question: question})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('infoModal')).hide();
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'envoi de la question.');
    });
}

function soumettreLivrableModal() {
    const commentaire = document.getElementById('commentaireLivrable').value;
    const fichier = document.getElementById('fichierLivrable').files[0];
    
    const formData = new FormData();
    formData.append('commentaire', commentaire);
    if (fichier) {
        formData.append('fichier', fichier);
    }
    
    fetch(`/activities/${currentActivityId}/soumettre-livrable`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('livrableModal')).hide();
            window.location.reload();
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la soumission du livrable.');
    });
}

function envoyerMessageDiscussion() {
    const message = document.getElementById('messageDiscussion').value;
    if (!message.trim()) {
        alert('Veuillez écrire un message.');
        return;
    }
    
    fetch(`/activities/${currentActivityId}/discuter`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({message: message})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('discussionModal')).hide();
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'envoi du message.');
    });
}

// Mettre à jour les fonctions pour utiliser les modals
function refuserActiviteProposee(activityId) {
    currentActivityId = activityId;
    document.getElementById('raisonRefus').value = '';
    new bootstrap.Modal(document.getElementById('refusModal')).show();
}

function demanderInfoProposee(activityId) {
    currentActivityId = activityId;
    document.getElementById('questionInfo').value = '';
    new bootstrap.Modal(document.getElementById('infoModal')).show();
}

function soumettreLivrableModal(activityId) {
    currentActivityId = activityId;
    document.getElementById('commentaireLivrable').value = '';
    document.getElementById('fichierLivrable').value = '';
    new bootstrap.Modal(document.getElementById('livrableModal')).show();
}

function discuterActiviteModal(activityId) {
    currentActivityId = activityId;
    document.getElementById('messageDiscussion').value = '';
    new bootstrap.Modal(document.getElementById('discussionModal')).show();
}
</script>

<!-- Modals pour les actions -->
<div class="modal fade" id="refusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Refuser l'activité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="refusForm">
                    <div class="mb-3">
                        <label for="raisonRefus" class="form-label">Raison du refus</label>
                        <textarea class="form-control" id="raisonRefus" name="raison" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" onclick="confirmerRefus()">Refuser</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="infoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Demander des informations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="infoForm">
                    <div class="mb-3">
                        <label for="questionInfo" class="form-label">Votre question</label>
                        <textarea class="form-control" id="questionInfo" name="question" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-warning" onclick="envoyerQuestion()">Envoyer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="livrableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Soumettre un livrable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="livrableForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="commentaireLivrable" class="form-label">Commentaire</label>
                        <textarea class="form-control" id="commentaireLivrable" name="commentaire" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fichierLivrable" class="form-label">Fichier (optionnel)</label>
                        <input type="file" class="form-control" id="fichierLivrable" name="fichier">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" onclick="soumettreLivrableModal()">Soumettre</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="discussionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Discuter avec l'encadrant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="discussionForm">
                    <div class="mb-3">
                        <label for="messageDiscussion" class="form-label">Votre message</label>
                        <textarea class="form-control" id="messageDiscussion" name="message" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="envoyerMessageDiscussion()">Envoyer</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Fonction pour afficher/masquer les notifications
function toggleNotifications() {
    const notificationsSection = document.querySelector('.notifications-section');
    if (notificationsSection) {
        notificationsSection.scrollIntoView({ behavior: 'smooth' });
        // Ajouter un effet de surbrillance temporaire
        notificationsSection.classList.add('border-info', 'border-2');
        setTimeout(() => {
            notificationsSection.classList.remove('border-info', 'border-2');
        }, 2000);
    }
}

// Fonction pour afficher/masquer la section des encadrants
function toggleEncadrants() {
    const encadrantsSection = document.getElementById('encadrants-section');
    if (encadrantsSection) {
        if (encadrantsSection.style.display === 'none') {
            encadrantsSection.style.display = 'block';
            // Scroller vers la section
            setTimeout(() => {
                encadrantsSection.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        } else {
            encadrantsSection.style.display = 'none';
        }
    }
}

// Fonction pour afficher/masquer la section de l'offre
function toggleOffre() {
    const offreSection = document.getElementById('offre-section');
    if (offreSection) {
        if (offreSection.style.display === 'none') {
            offreSection.style.display = 'block';
            // Scroller vers la section
            setTimeout(() => {
                offreSection.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        } else {
            offreSection.style.display = 'none';
        }
    }
}

// Fonction pour discuter avec un encadrant
function discuterAvecEncadrant(encadrantId, encadrantNom) {
    // Créer une modale dynamique pour la discussion
    const modalHtml = `
        <div class="modal fade" id="discussionEncadrantModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-comments text-primary me-2"></i>
                            Discussion avec ${encadrantNom}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="discussionMessages" style="max-height: 400px; overflow-y: auto;">
                            <!-- Messages chargés dynamiquement -->
                        </div>
                        <div class="mt-3">
                            <div class="input-group">
                                <input type="text" class="form-control" id="messageInput" placeholder="Tapez votre message..." maxlength="500">
                                <button class="btn btn-primary" onclick="sendMessageEncadrant(${encadrantId})">
                                    <i class="fas fa-paper-plane"></i> Envoyer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter la modale au body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Afficher la modale
    const modal = new bootstrap.Modal(document.getElementById('discussionEncadrantModal'));
    modal.show();
    
    // Charger les messages existants
    loadDiscussionMessagesEncadrant(encadrantId);
    
    // Nettoyer la modale quand elle est fermée
    document.getElementById('discussionEncadrantModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Fonction pour envoyer un message à un encadrant
function envoyerMessageEncadrant(encadrantId, encadrantNom) {
    // Créer une modale pour composer un message
    const modalHtml = `
        <div class="modal fade" id="messageEncadrantModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-envelope text-success me-2"></i>
                            Message pour ${encadrantNom}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="messageSubject" class="form-label">Sujet</label>
                            <input type="text" class="form-control" id="messageSubject" placeholder="Sujet du message">
                        </div>
                        <div class="mb-3">
                            <label for="messageContent" class="form-label">Message</label>
                            <textarea class="form-control" id="messageContent" rows="5" placeholder="Contenu de votre message..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-success" onclick="sendMessageToEncadrant(${encadrantId})">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter la modale au body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Afficher la modale
    const modal = new bootstrap.Modal(document.getElementById('messageEncadrantModal'));
    modal.show();
    
    // Nettoyer la modale quand elle est fermée
    document.getElementById('messageEncadrantModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Fonction pour charger les messages de discussion avec l'encadrant
function loadDiscussionMessagesEncadrant(encadrantId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/discussions/${encadrantId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const messagesContainer = document.getElementById('discussionMessages');
        messagesContainer.innerHTML = '';
        
        if (data.messages && data.messages.length > 0) {
            data.messages.forEach(message => {
                const messageHtml = `
                    <div class="d-flex ${message.sender_id === {{ auth()->id() }} ? 'justify-content-end' : 'justify-content-start'} mb-3">
                        <div class="p-2 rounded ${message.sender_id === {{ auth()->id() }} ? 'bg-primary text-white' : 'bg-light'}" style="max-width: 70%;">
                            <div class="small fw-bold">${message.sender.prenom} ${message.sender.nom}</div>
                            <div>${message.message}</div>
                            <div class="small text-muted">${message.created_at}</div>
                        </div>
                    </div>
                `;
                messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
            });
        } else {
            messagesContainer.innerHTML = '<div class="text-center text-muted">Aucun message pour le moment</div>';
        }
        
        // Scroller en bas
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    })
    .catch(error => {
        console.error('Erreur lors du chargement des messages:', error);
    });
}

// Fonction pour envoyer un message à l'encadrant
function sendMessageEncadrant(encadrantId) {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/discussions/${encadrantId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            loadDiscussionMessagesEncadrant(encadrantId);
        } else {
            alert('Erreur lors de l\'envoi du message');
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'envoi du message:', error);
        alert('Erreur lors de l\'envoi du message');
    });
}

// Fonction pour envoyer un message à l'encadrant (version email)
function sendMessageToEncadrant(encadrantId) {
    const subject = document.getElementById('messageSubject').value.trim();
    const content = document.getElementById('messageContent').value.trim();
    
    if (!subject || !content) {
        alert('Veuillez remplir tous les champs');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/messages/send/${encadrantId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ subject: subject, content: content })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Message envoyé avec succès');
            bootstrap.Modal.getInstance(document.getElementById('messageEncadrantModal')).hide();
        } else {
            alert('Erreur lors de l\'envoi du message');
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'envoi du message:', error);
        alert('Erreur lors de l\'envoi du message');
    });
}

// Fonction pour marquer toutes les notifications comme lues
function marquerToutesLues() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('ERREUR: Meta tag CSRF token non trouvé !');
        return;
    }
    
    const token = csrfToken.getAttribute('content');
    
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recharger la page pour voir les notifications mises à jour
        } else {
            alert('Erreur: ' + (data.error || 'Erreur lors de la mise à jour des notifications'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour des notifications.');
    });
}
</script>
@endsection
