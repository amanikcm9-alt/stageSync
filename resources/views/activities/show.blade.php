@extends('layouts.app')

@section('title', 'Détails de l\'activité')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title">{{ $activity->titre }}</h1>
        <p class="page-subtitle">Détails et suivi de l'activité</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <!-- Contenu principal -->
        <div class="col-lg-8">
            <!-- Informations de l'activité -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-tasks me-2"></i>Informations de l'activité
                        </h5>
                        <span class="badge bg-{{ $activity->priorite_color }} text-white">
                            {{ $activity->priorite_label }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Statut:</strong> 
                                <span class="badge bg-{{ $activity->statut_color }} text-white">
                                    {{ $activity->statut_label }}
                                </span>
                            </p>
                            @if($activity->stagiaire)
                                <p><strong>Stagiaire:</strong> {{ $activity->stagiaire->prenom }} {{ $activity->stagiaire->nom }}</p>
                            @endif
                            <p><strong>Encadrant:</strong> {{ $activity->encadrant->prenom }} {{ $activity->encadrant->nom }}</p>
                        </div>
                        <div class="col-md-6">
                            @if($activity->date_debut)
                                <p><strong>Date de début:</strong> {{ $activity->date_debut->format('d/m/Y') }}</p>
                            @endif
                            @if($activity->date_fin)
                                <p><strong>Date de fin:</strong> {{ $activity->date_fin->format('d/m/Y') }}</p>
                            @endif
                            @if($activity->date_limite)
                                <p><strong>Date limite:</strong> 
                                    <span class="{{ $activity->estEnRetard() ? 'text-danger' : 'text-muted' }}">
                                        {{ $activity->date_limite->format('d/m/Y') }}
                                        @if($activity->estEnRetard())
                                            <i class="fas fa-exclamation-triangle"></i>
                                        @endif
                                    </span>
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="text-primary">Description</h6>
                        <p>{{ $activity->description }}</p>
                    </div>
                    
                    @if($activity->objectifs)
                    <div class="mt-3">
                        <h6 class="text-primary">Objectifs pédagogiques</h6>
                        <p>{{ $activity->objectifs }}</p>
                    </div>
                    @endif
                    
                    @if($activity->livrables_attendus)
                    <div class="mt-3">
                        <h6 class="text-primary">Livrables attendus</h6>
                        <p>{{ $activity->livrables_attendus }}</p>
                    </div>
                    @endif
                    
                    <!-- Progression -->
                    @if($activity->progression !== null)
                    <div class="mt-3">
                        <h6 class="text-primary">Progression</h6>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-{{ $activity->progression >= 80 ? 'success' : ($activity->progression >= 50 ? 'warning' : 'info') }}" 
                                 role="progressbar" 
                                 style="width: {{ $activity->progression }}%">
                                {{ $activity->progression }}%
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Soumissions -->
            @if($activity->submissions->count() > 0)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Soumissions
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($activity->submissions as $submission)
                    <div class="border rounded p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Soumission du {{ $submission->created_at->format('d/m/Y H:i') }}</h6>
                                @if($submission->commentaire)
                                    <p class="mb-1">{{ $submission->commentaire }}</p>
                                @endif
                                @if($submission->fichier)
                                    <a href="{{ asset('storage/' . $submission->fichier) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Télécharger le fichier
                                    </a>
                                @endif
                            </div>
                            <span class="badge bg-{{ $submission->statut_color }} text-white">
                                {{ $submission->statut_label }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Documents -->
            @if($activity->documents->count() > 0)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>Documents et supports
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($activity->documents as $document)
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center p-2 border rounded">
                                <i class="fas {{ $document->type_icon }} text-{{ $document->type_color }} me-2"></i>
                                <div class="flex-grow-1">
                                    <small class="fw-bold">{{ $document->titre }}</small>
                                    <br>
                                    <small class="text-muted">{{ $document->type_label }}</small>
                                </div>
                                <a href="{{ $document->lien }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Actions -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h5>
                </div>
                <div class="card-body">
                    @if(auth()->user()->role->name === 'stagiaire')
                        <!-- Actions stagiaire -->
                        <a href="{{ route('stagiaire.activities.index') }}" class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="fas fa-list me-2"></i>Mes Activités
                        </a>
                        <a href="{{ route('activities.create') }}" class="btn btn-success btn-sm w-100 mb-2">
                            <i class="fas fa-plus me-2"></i>Proposer une activité
                        </a>
                        @if($activity->statut === 'assignee')
                            <button type="button" class="btn btn-primary btn-sm w-100 mb-2" onclick="demarrerActivite({{ $activity->id }})">
                                <i class="fas fa-play me-2"></i>Démarrer l'activité
                            </button>
                        @endif
                        
                        @if($activity->statut === 'en_cours')
                            <a href="{{ route('submissions.create', $activity) }}" class="btn btn-success btn-sm w-100 mb-2">
                                <i class="fas fa-upload me-2"></i>Soumettre un travail
                            </a>
                        @endif
                        
                        @if(in_array($activity->statut, ['assignee', 'en_cours']))
                            <button type="button" class="btn btn-danger btn-sm w-100 mb-2" onclick="refuserActivite({{ $activity->id }})">
                                <i class="fas fa-times me-2"></i>Refuser l'activité
                            </button>
                            <button type="button" class="btn btn-warning btn-sm w-100 mb-2" onclick="demanderInfo({{ $activity->id }})">
                                <i class="fas fa-question me-2"></i>Demander info
                            </button>
                            <button type="button" class="btn btn-info btn-sm w-100 mb-2" onclick="discuterActivite({{ $activity->id }})">
                                <i class="fas fa-comments me-2"></i>Discuter
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 mb-2" onclick="supprimerActiviteStagiaire({{ $activity->id }})">
                                <i class="fas fa-trash me-2"></i>Supprimer l'activité
                            </button>
                        @endif
                    @endif
                    
                    @if(auth()->user()->role->name === 'encadrant')
                        <!-- Actions encadrant -->
                        @if($activity->statut === 'proposee')
                            <button type="button" class="btn btn-success btn-sm w-100 mb-2" onclick="assignerActivite({{ $activity->id }})">
                                <i class="fas fa-user-plus me-2"></i>Assigner à un stagiaire
                            </button>
                        @endif
                        
                        @if(in_array($activity->statut, ['assignee', 'en_cours']))
                            <button type="button" class="btn btn-warning btn-sm w-100 mb-2" onclick="validerActivite({{ $activity->id }})">
                                <i class="fas fa-check me-2"></i>Valider l'activité
                            </button>
                            <button type="button" class="btn btn-info btn-sm w-100 mb-2" onclick="evaluerActivite({{ $activity->id }})">
                                <i class="fas fa-star me-2"></i>Évaluer l'activité
                            </button>
                        @endif
                        
                        <a href="{{ route('activities.edit', $activity) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </a>
                        
                        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="supprimerActivite({{ $activity->id }})">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- Historique -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Historique
                    </h5>
                </div>
                <div class="card-body">
                    <div class="small">
                        <p><strong>Créée le:</strong> {{ $activity->created_at->format('d/m/Y H:i') }}</p>
                        @if($activity->updated_at != $activity->created_at)
                            <p><strong>Modifiée le:</strong> {{ $activity->updated_at->format('d/m/Y H:i') }}</p>
                        @endif
                        @if($activity->date_debut && $activity->statut === 'en_cours')
                            <p><strong>Démarrée le:</strong> {{ $activity->date_debut->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour assigner à un stagiaire -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assigner à un stagiaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="#" id="assignForm" onsubmit="event.preventDefault(); submitAssignForm();">
                @csrf
                <input type="hidden" name="activity_id" id="assignActivityId" value="{{ $activity->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="stagiaire_id" class="form-label">Stagiaire *</label>
                        <select class="form-select" id="stagiaire_id" name="stagiaire_id" required>
                            <option value="">Choisir un stagiaire</option>
                            @php
                                $user = auth()->user();
                                
                                // Version simple et directe - récupérer tous les stagiaires de l'encadrant
                                $stagiaires = \App\Models\User::where('role_id', 4)
                                    ->where('encadrant_id', $user->id)
                                    ->orderBy('prenom')
                                    ->orderBy('nom')
                                    ->get();
                            @endphp
                            @foreach($stagiaires as $stagiaire)
                                @php
                                    $hasActivity = \App\Models\Activity::where('stagiaire_id', $stagiaire->id)->exists();
                                @endphp
                                <option value="{{ $stagiaire->id }}">
                                    {{ $stagiaire->prenom }} {{ $stagiaire->nom }}
                                    @if($hasActivity)
                                        (déjà assigné)
                                    @endif
                                </option>
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

<!-- Modal pour évaluer -->
<div class="modal fade" id="evaluationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Évaluer l'activité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="#" id="evaluationForm" onsubmit="event.preventDefault(); submitEvaluationForm();">
                @csrf
                <input type="hidden" name="activity_id" id="evaluationActivityId" value="{{ $activity->id }}">
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

<script>

function assignerActivite(activityId) {
    new bootstrap.Modal(document.getElementById('assignModal')).show();
}

function evaluerActivite(activityId) {
    new bootstrap.Modal(document.getElementById('evaluationModal')).show();
}

function submitAssignForm() {
    const activityId = document.getElementById('assignActivityId').value;
    const stagiaireId = document.getElementById('stagiaire_id').value;
    const form = document.getElementById('assignForm');
    const formData = new FormData(form);
    
    // Vérifier si un stagiaire est sélectionné
    if (!stagiaireId) {
        alert('Veuillez sélectionner un stagiaire');
        return;
    }
    
    // Vérifier le CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('ERREUR: Meta tag CSRF token non trouvé !');
        return;
    }
    
    fetch(`/activities/${activityId}/assigner`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer la modale
            const modal = bootstrap.Modal.getInstance(document.getElementById('assignModal'));
            modal.hide();
            
            // Afficher un message de succès
            alert(data.message);
            
            // Recharger la page pour voir les changements
            location.reload();
        } else {
            alert('Erreur lors de l\'assignation: ' + (data.error || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'assignation');
    });
}

function submitEvaluationForm() {
    const activityId = document.getElementById('evaluationActivityId').value;
    const form = document.getElementById('evaluationForm');
    const formData = new FormData(form);
    
    // Vérifier le CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('ERREUR: Meta tag CSRF token non trouvé !');
        return;
    }
    
    fetch(`/activities/${activityId}/evaluer`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer la modale
            const modal = bootstrap.Modal.getInstance(document.getElementById('evaluationModal'));
            modal.hide();
            
            // Afficher un message de succès
            alert(data.message);
            
            // Recharger la page pour voir les changements
            location.reload();
        } else {
            alert('Erreur lors de l\'évaluation: ' + (data.error || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'évaluation');
    });
}

function demarrerActivite(activityId) {
    if (confirm('Êtes-vous sûr de vouloir démarrer cette activité ?')) {
        // Vérifier le CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('ERREUR: Meta tag CSRF token non trouvé !');
            return;
        }
        
        const token = csrfToken.getAttribute('content');
        if (!token) {
            alert('ERREUR: CSRF token vide !');
            return;
        }
        
        fetch(`/activities/${activityId}/realiser`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Activité démarrée avec succès !');
                location.reload();
            } else {
                alert('Erreur: ' + (data.error || 'Erreur lors du démarrage de l\'activité'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du démarrage de l\'activité.');
        });
    }
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
    const raison = prompt('Raison du refus :');
    if (raison) {
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
                location.reload();
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du refus de l\'activité.');
        });
    }
}

function demanderInfo(activityId) {
    const question = prompt('Votre question :');
    if (question) {
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
}

// Variables pour la bulle de discussion
let currentDiscussionActivity = null;
let discussionMessages = {};

// Fonction pour ouvrir la bulle de discussion
function openDiscussionBubble(activityId, activityTitle, encadrantName) {
    console.log('Ouverture de la discussion pour activité:', activityId);
    
    currentDiscussionActivity = activityId;
    
    // Mettre à jour les informations de la discussion
    document.getElementById('discussionTitle').textContent = `Discuter avec ${encadrantName}`;
    document.getElementById('discussionActivity').textContent = activityTitle;
    
    // Charger les messages existants
    loadDiscussionMessages(activityId);
    
    // Afficher la bulle
    const bubble = document.getElementById('discussionBubble');
    bubble.style.display = 'flex';
    bubble.classList.remove('minimized');
    
    // Focus sur l'input
    setTimeout(() => {
        document.getElementById('discussionMessageInput').focus();
    }, 300);
}

// Fonction pour charger les messages existants
function loadDiscussionMessages(activityId) {
    console.log('Chargement des messages pour activité:', activityId);
    
    // Vérifier si on a déjà les messages en cache
    if (discussionMessages[activityId]) {
        displayMessages(discussionMessages[activityId]);
        return;
    }
    
    // Charger depuis le serveur
    fetch(`/activities/${activityId}/discussions`)
        .then(response => response.json())
        .then(messages => {
            console.log('Messages reçus:', messages);
            discussionMessages[activityId] = messages;
            displayMessages(messages);
        })
        .catch(error => {
            console.error('Erreur de chargement des messages:', error);
            // Afficher un message d'erreur dans la discussion
            displayMessages([{
                message: 'Impossible de charger les messages précédents',
                sender: { prenom: 'Système', nom: '' },
                created_at: new Date(),
                is_system: true
            }]);
        });
}

// Fonction pour afficher les messages dans la bulle
function displayMessages(messages) {
    const messagesContainer = document.getElementById('discussionMessages');
    messagesContainer.innerHTML = '';
    
    messages.forEach(message => {
        addMessageToBubble(message);
    });
    
    // Scroller vers le bas
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Fonction pour ajouter un message à la bulle
function addMessageToBubble(message) {
    const messagesContainer = document.getElementById('discussionMessages');
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'message';
    messageDiv.setAttribute('data-message-id', message.id);
    
    // Déterminer si c'est un message envoyé ou reçu
    const isSent = message.sender_id === {{ auth()->id() }};
    messageDiv.classList.add(isSent ? 'message-sent' : 'message-received');
    
    // Créer le contenu du message
    let messageContent = '';
    if (message.is_system) {
        messageContent = `<div class="text-muted small text-center">${message.message}</div>`;
    } else {
        const time = new Date(message.created_at).toLocaleTimeString('fr-FR', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        const editedBadge = message.edited_at ? '<span class="badge bg-warning ms-2">modifié</span>' : '';
        
        messageContent = `
            <div class="message-content">
                <div class="message-text">${message.message}</div>
                <div class="message-meta">
                    <span class="message-time">${time}</span>
                    ${editedBadge}
                    ${isSent ? `
                        <div class="message-actions">
                            <button class="btn btn-sm btn-outline-light" onclick="editMessage(${message.id}, '${message.message.replace(/'/g, "\\'")}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-light" onclick="deleteMessage(${message.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }
    
    messageDiv.innerHTML = messageContent;
    messagesContainer.appendChild(messageDiv);
    
    // Scroller vers le bas
    setTimeout(() => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 100);
}

// Fonction pour éditer un message
function editMessage(messageId, currentMessage) {
    const newMessage = prompt('Modifier votre message:', currentMessage);
    if (newMessage && newMessage !== currentMessage) {
        fetch(`/discussions/${messageId}/edit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: newMessage })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recharger les messages
                loadDiscussionMessages(currentDiscussionActivity);
            } else {
                alert('Erreur: ' + (data.error || 'Erreur lors de la modification'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la modification du message.');
        });
    }
}

// Fonction pour supprimer un message
function deleteMessage(messageId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
        fetch(`/discussions/${messageId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Retirer le message de l'affichage
                const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                if (messageElement) {
                    messageElement.remove();
                }
                
                // Retirer du cache
                if (discussionMessages[currentDiscussionActivity]) {
                    discussionMessages[currentDiscussionActivity] = discussionMessages[currentDiscussionActivity].filter(m => m.id !== messageId);
                }
            } else {
                alert('Erreur: ' + (data.error || 'Erreur lors de la suppression'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression du message.');
        });
    }
}

// Fonction pour envoyer un message depuis la bulle
function sendMessageFromBubble() {
    const input = document.getElementById('discussionMessageInput');
    const message = input.value.trim();
    
    if (!message) {
        return;
    }
    
    if (!currentDiscussionActivity) {
        alert('Aucune discussion active');
        return;
    }
    
    // Vérifier le CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('ERREUR: Meta tag CSRF token non trouvé !');
        return;
    }
    
    const token = csrfToken.getAttribute('content');
    
    // Envoyer le message
    fetch(`/activities/${currentDiscussionActivity}/discuter`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ajouter le message à l'affichage
            const newMessage = {
                message: message,
                sender_id: {{ auth()->id() }},
                created_at: new Date(),
                sender: { prenom: '{{ auth()->user()->prenom }}', nom: '{{ auth()->user()->nom }}' }
            };
            
            addMessageToBubble(newMessage);
            
            // Ajouter au cache
            if (!discussionMessages[currentDiscussionActivity]) {
                discussionMessages[currentDiscussionActivity] = [];
            }
            discussionMessages[currentDiscussionActivity].push(newMessage);
            
            // Vider l'input
            input.value = '';
            input.focus();
            
        } else {
            alert('Erreur: ' + (data.error || 'Erreur lors de l\'envoi du message'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'envoi du message.');
    });
}

// Fonction pour minimiser la discussion
function minimizeDiscussion() {
    const bubble = document.getElementById('discussionBubble');
    bubble.classList.toggle('minimized');
}

// Fonction pour fermer la discussion
function closeDiscussion() {
    const bubble = document.getElementById('discussionBubble');
    bubble.style.display = 'none';
    currentDiscussionActivity = null;
}

// Fonction principale pour discuter
function discuterActivite(activityId) {
    console.log('discuterActivite appelé avec activityId:', activityId);
    
    // Trouver les informations de l'activité et de l'encadrant
    fetch(`/activities/${activityId}/json`)
        .then(response => response.json())
        .then(activity => {
            console.log('Activité chargée:', activity);
            const encadrantName = `${activity.encadrant.prenom} ${activity.encadrant.nom}`;
            openDiscussionBubble(activityId, activity.titre, encadrantName);
        })
        .catch(error => {
            console.error('Erreur:', error);
            // Fallback avec des informations génériques
            openDiscussionBubble(activityId, 'Activité', 'Encadrant');
        });
}

function supprimerActivite(activityId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')) {
        // Vérifier le CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('ERREUR: Meta tag CSRF token non trouvé !');
            return;
        }
        
        fetch(`/activities/${activityId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Activité supprimée avec succès');
                    // Utiliser l'URL de redirection fournie par le serveur
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert('Erreur: ' + (data.error || 'Erreur lors de la suppression'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la suppression de l\'activité.');
            });
    }
}

function supprimerActiviteStagiaire(activityId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette activité ? Cette action est irréversible.')) {
        // Vérifier le CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('ERREUR: Meta tag CSRF token non trouvé !');
            return;
        }
        
        const token = csrfToken.getAttribute('content');
        
        fetch(`/activities/${activityId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Activité supprimée avec succès');
                // Utiliser l'URL de redirection fournie par le serveur
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                alert('Erreur: ' + (data.error || 'Erreur lors de la suppression'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression de l\'activité.');
        });
    }
}
</script>

<!-- Bulle de Discussion Style WhatsApp -->
<div id="discussionBubble" class="discussion-bubble" style="display: none;">
    <div class="discussion-header">
        <div class="discussion-info">
            <div class="discussion-avatar">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="discussion-details">
                <h6 id="discussionTitle">Discuter avec l'encadrant</h6>
                <small id="discussionActivity" class="text-muted">Activité</small>
            </div>
        </div>
        <div class="discussion-actions">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="minimizeDiscussion()">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="closeDiscussion()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    <div class="discussion-messages" id="discussionMessages">
        <!-- Messages chargés dynamiquement -->
    </div>
    
    <div class="discussion-input">
        <div class="input-group">
            <input type="text" class="form-control" id="discussionMessageInput" 
                   placeholder="Écrivez votre message..." 
                   onkeypress="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); sendMessageFromBubble(); }">
            <button class="btn btn-primary" type="button" onclick="sendMessageFromBubble()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
.discussion-bubble {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    border: 1px solid #e0e0e0;
}

.discussion-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    border-radius: 10px 10px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.discussion-info {
    display: flex;
    align-items: center;
}

.discussion-avatar {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}

.discussion-details h6 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
}

.discussion-details small {
    font-size: 12px;
}

.discussion-actions {
    display: flex;
    gap: 5px;
}

.discussion-actions .btn {
    padding: 4px 8px;
    font-size: 12px;
}

.discussion-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.message {
    max-width: 70%;
    padding: 8px 12px;
    border-radius: 15px;
    font-size: 14px;
    line-height: 1.4;
    word-wrap: break-word;
}

.message-sent {
    align-self: flex-end;
    background: #007bff;
    color: white;
    border-bottom-right-radius: 5px;
}

.message-received {
    align-self: flex-start;
    background: white;
    color: #333;
    border: 1px solid #e0e0e0;
    border-bottom-left-radius: 5px;
}

.message-time {
    font-size: 11px;
    opacity: 0.7;
    margin-top: 4px;
}

.discussion-input {
    padding: 15px;
    border-top: 1px solid #e0e0e0;
    background: white;
    border-radius: 0 0 10px 10px;
}

.discussion-input .input-group {
    border-radius: 20px;
    overflow: hidden;
}

.discussion-input .form-control {
    border: none;
    border-radius: 20px;
    padding: 8px 15px;
}

.discussion-input .btn {
    border: none;
    border-radius: 20px;
    padding: 8px 15px;
}

.discussion-bubble.minimized {
    height: 60px;
}

.discussion-bubble.minimized .discussion-messages,
.discussion-bubble.minimized .discussion-input {
    display: none;
}

/* Animation d'apparition */
@keyframes slideInUp {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.discussion-bubble {
    animation: slideInUp 0.3s ease-out;
}

/* Scrollbar personnalisée */
.discussion-messages::-webkit-scrollbar {
    width: 6px;
}

.discussion-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.discussion-messages::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.discussion-messages::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection
