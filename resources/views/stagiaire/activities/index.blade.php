@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-tasks text-primary"></i> 
                Mes Activités
            </h2>
            <small class="text-muted">
                Gérez vos activités et livrables
            </small>
        </div>
        <div>
            <a href="{{ route('activities.propose') }}" class="btn btn-success btn-sm me-2">
                <i class="fas fa-plus me-2"></i>Proposer une activité
            </a>
            <a href="{{ route('stagiaire.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour au dashboard
            </a>
        </div>
    </div>

    <!-- Filtres et navigation -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-sm" id="activitiesTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="assigned-tab" data-bs-toggle="tab" data-bs-target="#assigned" type="button" role="tab">
                        <i class="fas fa-clipboard-check"></i> Activités Assignées
                        @if($activities->count() > 0)
                        <span class="badge bg-primary ms-1">{{ $activities->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="proposed-tab" data-bs-toggle="tab" data-bs-target="#proposed" type="button" role="tab">
                        <i class="fas fa-lightbulb"></i> Activités Proposées
                        @if($proposedActivities->count() > 0)
                        <span class="badge bg-warning ms-1">{{ $proposedActivities->count() }}</span>
                        @endif
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Contenu des tabs -->
    <div class="tab-content" id="activitiesTabsContent">
        <!-- Activités assignées -->
        <div class="tab-pane fade show active" id="assigned">
            @if($activities->count() > 0)
            <div class="row">
                @foreach($activities as $activity)
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <h6 class="mb-3">{{ $activity->titre }}</h6>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-{{ $activity->statut_color }} text-white">{{ $activity->statut_label }}</span>
                                <span class="badge bg-{{ $activity->priorite_color }} text-dark">{{ $activity->priorite_label }}</span>
                            </div>
                            <p class="text-muted small mb-3">{{ Str::limit($activity->description, 100) }}</p>
                            @if($activity->date_limite)
                            <div class="alert alert-warning alert-sm py-2 mb-3">
                                <i class="fas fa-clock"></i> 
                                <small>{{ $activity->date_limite->format('d/m/Y') }}</small>
                                @if($activity->estEnRetard())
                                <span class="badge bg-danger ms-1">En retard</span>
                                @endif
                            </div>
                            @endif
                            <div class="d-flex gap-2">
                                <a href="{{ route('activities.show', $activity) }}" class="btn btn-primary flex-fill">
                                    <i class="fas fa-eye me-2"></i>Voir les détails
                                </a>
                                                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucune activité assignée</h5>
                <p class="text-muted">Vous n'avez pas encore d'activités à réaliser.</p>
            </div>
            @endif
        </div>

        <!-- Activités proposées -->
        <div class="tab-pane fade" id="proposed">
            @if($proposedActivities->count() > 0)
            <div class="row">
                @foreach($proposedActivities as $activity)
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <h6 class="mb-3">{{ $activity->titre }}</h6>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-warning text-dark">Proposée</span>
                                <span class="badge bg-{{ $activity->priorite_color }} text-dark">{{ $activity->priorite_label }}</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i> 
                                    {{ $activity->encadrant->prenom }} {{ $activity->encadrant->nom }}
                                </small>
                            </div>
                            <p class="text-muted small mb-3">{{ Str::limit($activity->description, 100) }}</p>
                            @if($activity->date_limite)
                            <div class="alert alert-warning alert-sm py-2 mb-3">
                                <i class="fas fa-clock"></i> 
                                <small>{{ $activity->date_limite->format('d/m/Y') }}</small>
                            </div>
                            @endif
                            <div class="d-flex gap-2">
                                <a href="{{ route('activities.show', $activity) }}" class="btn btn-primary flex-fill">
                                    <i class="fas fa-eye me-2"></i>Voir les détails
                                </a>
                                                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucune activité proposée</h5>
                <p class="text-muted">Vos encadrants n'ont pas encore proposé d'activités.</p>
            </div>
            @endif
        </div>
    </div>
</div>

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
                <button type="button" class="btn btn-primary" onclick="soumettreLivrable()">Soumettre</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Test de chargement du JavaScript
console.log('JavaScript chargé - stagiaire/activities/index.blade.php');

// Variables globales
let currentActivityId = null;

// Test simple pour vérifier si les fonctions sont accessibles
window.testButtons = function() {
    console.log('Test des boutons - JavaScript fonctionne !');
    alert('JavaScript fonctionne ! Les boutons devraient être cliquables.');
};

// Test spécifique pour la fonction de discussion
window.testDiscussion = function() {
    console.log('Test de la fonction envoyerMessageDiscussion');
    if(typeof envoyerMessageDiscussion === 'function') {
        console.log('Fonction envoyerMessageDiscussion trouvée');
        alert('Fonction de discussion trouvée ! Le bouton Envoyer devrait fonctionner.');
    } else {
        console.error('Fonction envoyerMessageDiscussion NON trouvée');
        alert('ERREUR: fonction envoyerMessageDiscussion non trouvée !');
    }
};


// Variables pour la bulle de discussion
let currentDiscussionActivity = null;
let discussionMessages = {};

// Fonction pour ouvrir la bulle de discussion
function openDiscussionBubble(activityId, activityTitle, encadrantName) {
    console.log('Ouverture de la discussion pour activité:', activityId);
    
    currentDiscussionActivity = activityId;
    currentActivityId = activityId;
    
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
        
        messageContent = `
            <div>${message.message}</div>
            <div class="message-time">${time}</div>
        `;
    }
    
    messageDiv.innerHTML = messageContent;
    messagesContainer.appendChild(messageDiv);
    
    // Scroller vers le bas
    setTimeout(() => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 100);
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
                sender: { prenom: auth()->user()->prenom, nom: auth()->user()->nom }
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

// Modifier la fonction discuterActiviteModal pour utiliser la bulle
function discuterActiviteModal(activityId) {
    console.log('discuterActiviteModal appelé avec activityId:', activityId);
    
    // Trouver les informations de l'activité et de l'encadrant
    fetch(`/activities/${activityId}`)
        .then(response => response.json())
        .then(activity => {
            const encadrantName = `${activity.encadrant.prenom} ${activity.encadrant.nom}`;
            openDiscussionBubble(activityId, activity.titre, encadrantName);
        })
        .catch(error => {
            console.error('Erreur:', error);
            // Fallback avec des informations génériques
            openDiscussionBubble(activityId, 'Activité', 'Encadrant');
        });
}

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

function soumettreLivrable() {
    console.log('soumettreLivrable appelé avec currentActivityId:', currentActivityId);
    const commentaire = document.getElementById('commentaireLivrable').value;
    const fichier = document.getElementById('fichierLivrable').files[0];
    
    // Vérifier le CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('ERREUR: Meta tag CSRF token non trouvé !');
        console.error('Meta tag CSRF token non trouvé');
        return;
    }
    
    const token = csrfToken.getAttribute('content');
    if (!token) {
        alert('ERREUR: CSRF token vide !');
        console.error('CSRF token vide');
        return;
    }
    
    const formData = new FormData();
    formData.append('commentaire', commentaire);
    if (fichier) {
        formData.append('fichier', fichier);
    }
    
    const url = `/activities/${currentActivityId}/soumettre-livrable`;
    console.log('URL appelée:', url);
    console.log('CSRF Token:', token.substring(0, 10) + '...');
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
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
    console.log('envoyerMessageDiscussion appelé avec currentActivityId:', currentActivityId);
    const message = document.getElementById('messageDiscussion').value;
    if (!message.trim()) {
        alert('Veuillez écrire un message.');
        return;
    }
    
    // Vérifier le CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('ERREUR: Meta tag CSRF token non trouvé !');
        console.error('Meta tag CSRF token non trouvé');
        return;
    }
    
    const token = csrfToken.getAttribute('content');
    if (!token) {
        alert('ERREUR: CSRF token vide !');
        console.error('CSRF token vide');
        return;
    }
    
    const url = `/activities/${currentActivityId}/discuter`;
    console.log('URL appelée:', url);
    console.log('Message:', message);
    console.log('CSRF Token:', token.substring(0, 10) + '...');
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({message: message})
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
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

// Fonctions pour ouvrir les modals
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
    console.log('soumettreLivrableModal appelé avec activityId:', activityId);
    currentActivityId = activityId;
    document.getElementById('commentaireLivrable').value = '';
    document.getElementById('fichierLivrable').value = '';
    const modal = new bootstrap.Modal(document.getElementById('livrableModal'));
    console.log('Modal livrable:', modal);
    modal.show();
}

function discuterActiviteModal(activityId) {
    console.log('discuterActiviteModal appelé avec activityId:', activityId);
    
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
