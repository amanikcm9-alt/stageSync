@extends('layouts.app')
@section('title', 'Mes activités')
@push('styles')
<style>
    /* Optimisation Bootstrap 5 */
    .btn {
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .nav-tabs .nav-link:hover {
        border-bottom-color: #0d6efd;
        color: #0d6efd;
    }
    
    .nav-tabs .nav-link.active {
        border-bottom-color: #0d6efd;
        color: #0d6efd;
        background: none;
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    /* Styles spécifiques pour la modal améliorée */
    #infoModal .modal-content {
        border-radius: 1rem;
        overflow: hidden;
    }
    
    #infoModal .modal-header {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        padding: 1.5rem;
    }
    
    #infoModal .modal-body {
        padding: 2rem;
    }
    
    #infoModal .form-control-lg {
        border-radius: 0.5rem;
        border-color: #dee2e6;
        font-size: 1rem;
    }
    
    #infoModal .form-control-lg:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
    }
    
    #infoModal .btn-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border: none;
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    #infoModal .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
    }
    
    #infoModal .btn-outline-secondary {
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
    }
    
    #infoModal .alert-info {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-left: 4px solid #17a2b8;
        border-radius: 0.5rem;
    }
    
    /* Animation d'entrée pour la modal */
    #infoModal .modal-dialog {
        animation: modalSlideIn 0.3s ease-out;
    }
    
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-50px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
        padding: 0.375em 0.75em;
        border-radius: 0.375rem;
    }
    
    .alert {
        border: none;
        border-radius: 0.5rem;
    }
    
    .form-control {
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .modal-content {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .modal-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        border-radius: 0.75rem 0.75rem 0 0;
    }
    
    .modal-footer {
        border-top: 1px solid rgba(0,0,0,0.05);
        border-radius: 0 0 0.75rem 0.75rem;
    }
</style>
@endpush

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
            <a href="{{ route('stagiaire.activities.propose') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-plus me-2"></i> Proposer une activité
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
                        @if($allProposedActivities->count() > 0)
                        <span class="badge bg-warning ms-1">{{ $allProposedActivities->count() }}</span>
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
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column text-center">
                            <div class="mb-3">
                                <h5 class="card-title fs-6 mb-2">{{ $activity->titre }}</h5>
                                <div class="d-flex justify-content-center gap-2 mb-2">
                                    <span class="badge bg-{{ $activity->statut_color }} text-white">{{ $activity->statut_label }}</span>
                                    <span class="badge bg-{{ $activity->priorite_color }} text-dark">{{ $activity->priorite_label }}</span>
                                </div>
                            </div>
                            
                            <div class="mb-3 flex-grow-1">
                                <p class="card-text text-muted small">{{ Str::limit($activity->description, 100) }}</p>
                            </div>
                            
                            @if($activity->date_limite)
                            <div class="alert alert-{{ $activity->estEnRetard() ? 'danger' : 'warning' }} d-flex align-items-center justify-content-between py-2 mb-3" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $activity->estEnRetard() ? 'exclamation-triangle' : 'clock' }} me-2"></i>
                                    <small class="fw-semibold">
                                        @if($activity->estEnRetard())
                                            Date limite dépassée : 
                                        @else
                                            Date limite : 
                                        @endif
                                        {{ $activity->date_limite->format('d/m/Y') }}
                                    </small>
                                </div>
                                @if($activity->estEnRetard())
                                <span class="badge bg-{{ $activity->estEnRetard() ? 'danger' : 'warning' }} text-white">
                                    <i class="fas fa-exclamation-circle me-1"></i>En retard
                                </span>
                                @endif
                            </div>
                            @endif
                            
                            <div class="mt-auto">
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('activities.show', $activity) }}" class="btn btn-primary">
                                        <i class="fas fa-eye"></i>
                                        <span class="d-none d-sm-inline ms-1">Voir</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light p-4">
                        <i class="fas fa-inbox fa-2x text-muted"></i>
                    </div>
                </div>
                <h5 class="text-muted fw-light mb-3">Aucune activité assignée</h5>
                <p class="text-muted mb-4">Vous n'avez pas encore d'activités à réaliser.</p>
                <div class="alert alert-info d-inline-block">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>Les activités proposées par vos encadrants apparaîtront ici une fois acceptées.</small>
                </div>
            </div>
            @endif
        </div>

        
        <!-- Activités proposées -->
        <div class="tab-pane fade" id="proposed">
            @if($allProposedActivities->count() > 0)
            <div class="row">
                @foreach($allProposedActivities as $activity)
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column text-center">
                            <div class="mb-3">
                                <h5 class="card-title fs-6 mb-2">{{ $activity->titre }}</h5>
                                <div class="d-flex justify-content-center gap-2 mb-2">
                                    @if($activity->stagiaire_id === auth()->user()->id)
                                        <span class="badge bg-info">Proposée par moi</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Proposée par encadrant</span>
                                    @endif
                                    <span class="badge bg-{{ $activity->priorite_color ?? 'secondary' }} text-dark">{{ $activity->priorite_label ?? 'Normale' }}</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="text-muted small">
                                    @if($activity->stagiaire_id === auth()->user()->id)
                                        <i class="fas fa-paper-plane me-1"></i> 
                                        Destinataire: {{ $activity->encadrant->prenom }} {{ $activity->encadrant->nom }}
                                    @else
                                        <i class="fas fa-user me-1"></i> 
                                        {{ $activity->encadrant->prenom }} {{ $activity->encadrant->nom }}
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-3 flex-grow-1">
                                <p class="card-text text-muted small">{{ Str::limit($activity->description, 100) }}</p>
                            </div>
                            
                            @if($activity->date_limite)
                            <div class="alert alert-info d-flex align-items-center py-2 mb-3" role="alert">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <div class="flex-grow-1">
                                    <small class="fw-semibold">Date limite proposée :</small>
                                    <div class="text-muted small">{{ $activity->date_limite->format('d/m/Y') }}</div>
                                </div>
                                <span class="badge bg-info text-white">
                                    <i class="fas fa-calendar me-1"></i>Proposée
                                </span>
                            </div>
                            @endif
                            
                            <div class="mt-auto">
                                <div class="btn-group w-100" role="group">
                                    @if($activity->stagiaire_id !== auth()->user()->id)
                                        <button type="button" class="btn btn-outline-danger" onclick="refuserActiviteProposee({{ $activity->id }})">
                                            <i class="fas fa-times"></i>
                                            <span class="d-none d-sm-inline ms-1">Refuser</span>
                                        </button>
                                    @endif
                                    <a href="{{ route('activities.show', $activity) }}" class="btn btn-primary">
                                        <i class="fas fa-eye"></i>
                                        <span class="d-none d-sm-inline ms-1">Voir</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light p-4">
                        <i class="fas fa-lightbulb fa-2x text-muted"></i>
                    </div>
                </div>
                <h5 class="text-muted fw-light mb-3">Aucune activité proposée</h5>
                
            </div>
            @endif
        </div>
        
            </div>
</div>

<div class="modal fade" id="infoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient bg-info text-white border-0">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                            <i class="fas fa-question-circle fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="modal-title mb-0 fw-bold">Demander des informations complémentaires</h5>
                        <p class="mb-0 opacity-75 small">Posez votre question à l'encadrant</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="infoForm">
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            <label for="questionInfo" class="form-label fw-semibold mb-0">Votre question</label>
                        </div>
                        <textarea class="form-control form-control-lg border-2" id="questionInfo" name="question" rows="4" 
                                  placeholder="Décrivez clairement l'information dont vous avez besoin..." required></textarea>
                        <div class="form-text mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Soyez précis dans votre question pour obtenir une réponse rapide et pertinente.
                        </div>
                    </div>
                    
                    <div class="alert alert-info border-0 bg-light-blue-subtle">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-tie me-3 text-info"></i>
                            <div>
                                <strong class="text-info">Votre question sera envoyée à l'encadrant</strong>
                                <p class="mb-0 small text-muted">Vous recevrez une notification dès que l'encadrant répondra</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0 p-4">
                <div class="d-flex justify-content-between w-100">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="button" class="btn btn-info px-4" onclick="envoyerQuestion()">
                        <i class="fas fa-paper-plane me-2"></i>Envoyer la question
                    </button>
                </div>
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

@push('styles')
<style>
    .modal-header.bg-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
    }
    
    .modal-footer.bg-light {
        background-color: #f8f9fa !important;
        border-top: 1px solid #dee2e6 !important;
    }
    
    .text-primary {
        color: #0d6efd !important;
        font-weight: 500;
    }
    
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    /* Animations pour les alertes */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    /* Amélioration du conteneur d'alertes */
    #alertContainer {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1055;
        max-width: 400px;
        width: 100%;
    }
    
    #alertContainer .alert {
        margin-bottom: 10px;
        border-radius: 8px;
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
    }
    
    #alertContainer .alert-success {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.1) 0%, rgba(25, 135, 84, 0.05) 100%);
        color: #0f5132;
    }
    
    #alertContainer .alert-danger {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
        color: #842029;
    }
    
    #alertContainer .alert-warning {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%);
        color: #664d03;
    }
    
    #alertContainer .alert-info {
        background: linear-gradient(135deg, rgba(13, 202, 240, 0.1) 0%, rgba(13, 202, 240, 0.05) 100%);
        color: #055160;
    }
    
    /* Effet hover sur les alertes */
    #alertContainer .alert:hover {
        transform: scale(1.02);
        transition: transform 0.2s ease;
    }
    
    /* Styles pour le modal de refus amélioré */
    #refusModal .modal-content {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    #refusModal .modal-header {
        border-bottom: none;
        padding: 1.5rem;
    }
    
    #refusModal .modal-body {
        padding: 1.5rem;
    }
    
    #refusModal .form-control:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    #refusModal .btn {
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    #refusModal .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    #refusModal .alert {
        border: none;
        border-radius: 0.5rem;
    }
    
    #refusModal .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    /* Responsive pour le modal */
    @media (max-width: 576px) {
        #refusModal .modal-dialog {
            margin: 1rem;
        }
        
        #refusModal .modal-header {
            padding: 1rem;
        }
        
        #refusModal .modal-body {
            padding: 1rem;
        }
    }
    
    /* Styles SweetAlert2 Bootstrap 5 */
    .swal2-bootstrap5 {
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 1rem;
        line-height: 1.5;
    }
    
    .swal2-bootstrap5-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #212529;
    }
    
    .swal2-bootstrap5-content {
        color: #6c757d;
    }
    
    .swal2-bootstrap5-actions {
        margin-top: 1.5rem;
    }
    
    .swal2-bootstrap5 .swal2-textarea {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .swal2-bootstrap5 .swal2-textarea:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .swal2-bootstrap5 .swal2-validation-message {
        font-size: 0.875rem;
        color: #dc3545;
        margin-top: 0.25rem;
    }
    
    .swal2-bootstrap5 .swal2-progress-steps .swal2-progress-step {
        background: #0d6efd;
    }
    
    .swal2-bootstrap5 .swal2-progress-steps .swal2-progress-step.swal2-active-progress-step {
        background: #0d6efd;
    }
</style>
@endpush

@section('scripts')
<script>
// Test de chargement du JavaScript
console.log('JavaScript chargé - stagiaire/activities/index.blade.php');

// Variables globales
let currentActivityId = null;

// Vérifier si SweetAlert2 est chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé');
    console.log('Swal disponible au DOM loaded:', typeof Swal);
    
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 n\'est pas chargé!');
        // Afficher une alerte simple pour le débogage
        setTimeout(() => {
            alert('ERREUR: SweetAlert2 n\'est pas chargé. Vérifiez la connexion internet ou rechargez la page.');
        }, 1000);
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
            <div class="message-content-wrapper">
                <div class="message-text">${message.message}</div>
                ${isSent ? `
                    <button class="btn btn-sm btn-outline-secondary edit-message-btn" onclick="editMessage(${message.id}, '${message.message.replace(/'/g, "\\'")}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-message-btn" onclick="deleteMessage(${message.id})">
                        <i class="fas fa-trash"></i>
                    </button>` : ''}
            </div>
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

// Fonction pour afficher des messages Bootstrap 5 améliorés
function showAlert(message, type = 'info', title = null) {
    const alertContainer = document.getElementById('alertContainer') || createAlertContainer();
    const alertId = 'alert-' + Date.now();
    
    const alertConfig = getAlertConfig(type);
    const titleHtml = title ? `<h6 class="alert-heading mb-2">${title}</h6>` : '';
    
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show shadow-lg border-0" role="alert" id="${alertId}" 
             style="animation: slideInRight 0.3s ease-out; border-left: 4px solid ${alertConfig.borderColor};">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <i class="fas ${alertConfig.icon} fa-lg me-3" style="color: ${alertConfig.iconColor};"></i>
                </div>
                <div class="flex-grow-1">
                    ${titleHtml}
                    <div class="alert-message">${message}</div>
                    ${type === 'success' ? '<div class="mt-2"><small class="text-muted">Cette action a été enregistrée avec succès.</small></div>' : ''}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHtml);
    
    // Animation d'entrée
    const alertElement = document.getElementById(alertId);
    setTimeout(() => {
        alertElement.style.opacity = '1';
        alertElement.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto-suppression après 6 secondes
    setTimeout(() => {
        if (alertElement && alertElement.parentNode) {
            alertElement.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alertElement);
                bsAlert.close();
            }, 300);
        }
    }, 6000);
}

function getAlertConfig(type) {
    const configs = {
        'success': {
            icon: 'fa-check-circle',
            iconColor: '#198754',
            borderColor: '#198754',
            title: 'Succès'
        },
        'danger': {
            icon: 'fa-exclamation-triangle',
            iconColor: '#dc3545',
            borderColor: '#dc3545',
            title: 'Erreur'
        },
        'warning': {
            icon: 'fa-exclamation-circle',
            iconColor: '#ffc107',
            borderColor: '#ffc107',
            title: 'Attention'
        },
        'info': {
            icon: 'fa-info-circle',
            iconColor: '#0dcaf0',
            borderColor: '#0dcaf0',
            title: 'Information'
        }
    };
    return configs[type] || configs.info;
}

function getAlertIcon(type) {
    const icons = {
        'success': 'check-circle',
        'danger': 'exclamation-triangle',
        'warning': 'exclamation-circle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alertContainer';
    // Le style est déjà défini dans le CSS
    document.body.appendChild(container);
    return container;
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
                showAlert(data.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('Erreur: ' + data.error, 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('Erreur lors de l\'acceptation de l\'activité.', 'danger');
        });
    }
}

function confirmerRefus() {
    const raison = document.getElementById('raisonRefus').value;
    if (!raison.trim()) {
        showAlert('Veuillez indiquer la raison du refus.', 'warning', 'Champ obligatoire');
        return;
    }
    
    confirmerRefusAvecRaison(raison);
}

function confirmerRefusAvecRaison(raison) {
    // Solution simple sans SweetAlert2
    alert('Traitement du refus en cours...');
    
    // Créer un formulaire temporaire pour l'envoi
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/activities/${currentActivityId}/refuser`;
    form.style.display = 'none';
    
    // Ajouter le CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        form.appendChild(csrfInput);
    }
    
    // Ajouter la raison
    const raisonInput = document.createElement('input');
    raisonInput.type = 'hidden';
    raisonInput.name = 'justification';
    raisonInput.value = raison;
    form.appendChild(raisonInput);
    
    // Ajouter le formulaire à la page et le soumettre
    document.body.appendChild(form);
    form.submit();
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
    console.log('refuserActiviteProposee appelé avec activityId:', activityId);
    
    // Solution AJAX directe
    if (confirm('Êtes-vous sûr de vouloir refuser cette activité ?')) {
        const raison = prompt('Veuillez indiquer la raison du refus :');
        
        if (raison !== null && raison.trim() !== '') {
            console.log('Envoi AJAX du refus pour activité ID:', activityId, 'raison:', raison);
            
            // Récupérer le CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) {
                alert('ERREUR: CSRF token non trouvé');
                return;
            }
            
            // Envoyer la requête AJAX
            fetch(`/activities/${activityId}/refuser`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    raison: raison
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Réponse AJAX:', data);
                if (data.success) {
                    alert('Activité refusée avec succès !');
                    // Rafraîchir la page pour voir les changements
                    window.location.reload();
                } else {
                    alert('Erreur: ' + (data.error || 'Erreur lors du refus'));
                }
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
                alert('Erreur lors de l\'envoi: ' + error.message);
            });
        } else {
            console.log('Raison vide ou annulée');
        }
    } else {
        console.log('Refus annulé');
    }
}

// Compteur de caractères
function updateCharCount() {
    const textarea = document.getElementById('raisonRefus');
    const charCount = document.getElementById('charCount');
    const remaining = 500 - textarea.value.length;
    charCount.textContent = remaining;
    
    if (remaining < 50) {
        charCount.className = 'text-danger';
    } else if (remaining < 100) {
        charCount.className = 'text-warning';
    } else {
        charCount.className = 'text-muted';
    }
}

// Fonction pour soumettre le travail
function soumettreTravail(activityId) {
    // Créer une modal pour soumettre le travail
    const modalHtml = `
        <div class="modal fade" id="soumettreTravailModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-file-upload me-2"></i>Soumettre le travail
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="travailFile" class="form-label">Fichier du travail</label>
                            <input type="file" class="form-control" id="travailFile" accept=".pdf,.doc,.docx,.zip,.rar">
                            <small class="text-muted">Formats acceptés: PDF, DOC, DOCX, ZIP, RAR</small>
                        </div>
                        <div class="mb-3">
                            <label for="travailDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="travailDescription" rows="3" placeholder="Décrivez brièvement votre travail..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-success" onclick="envoyerTravail(${activityId})">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer le travail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Supprimer la modal existante si elle existe
    const existingModal = document.getElementById('soumettreTravailModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Ajouter la nouvelle modal
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Afficher la modal
    new bootstrap.Modal(document.getElementById('soumettreTravailModal')).show();
}

// Fonction pour envoyer le travail
function envoyerTravail(activityId) {
    const fileInput = document.getElementById('travailFile');
    const description = document.getElementById('travailDescription').value;
    
    if (!fileInput.files[0]) {
        alert('Veuillez sélectionner un fichier.');
        return;
    }
    
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('description', description);
    formData.append('activity_id', activityId);
    
    fetch(`/activities/${activityId}/soumettre`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Travail soumis avec succès!');
            bootstrap.Modal.getInstance(document.getElementById('soumettreTravailModal')).hide();
            location.reload();
        } else {
            alert('Erreur: ' + (data.error || 'Erreur lors de la soumission'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la soumission du travail.');
    });
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

function demarrerActivite(activityId) {
    console.log('demarrerActivite appelé avec activityId:', activityId);
    
    if (confirm('Êtes-vous sûr de vouloir démarrer cette activité ? Cette action enverra une notification à votre encadrant.')) {
        // Créer un formulaire temporaire pour l'envoi
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/activities/${activityId}/realiser`;
        form.style.display = 'none';
        
        // Ajouter le CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        } else {
            alert('ERREUR: CSRF token non trouvé');
            return;
        }
        
        // Ajouter le formulaire à la page et le soumettre
        document.body.appendChild(form);
        console.log('Formulaire de démarrage créé et prêt à être soumis');
        form.submit();
    } else {
        console.log('Démarrage annulé');
    }
}

function discuterActiviteModal(activityId) {
    console.log('discuterActiviteModal appelé avec activityId:', activityId);
    
    // Solution simple : rediriger vers la page de discussion de l'activité
    window.location.href = `/activities/${activityId}`;
}

// Variables globales pour la modification de message
let currentMessageId = null;

// Fonction pour ouvrir la modal de modification
function editMessage(messageId, messageText) {
    currentMessageId = messageId;
    document.getElementById('editedMessage').value = messageText;
    new bootstrap.Modal(document.getElementById('editMessageModal')).show();
}

// Fonction pour sauvegarder le message modifié
function saveEditedMessage() {
    const newMessage = document.getElementById('editedMessage').value.trim();
    
    if (!newMessage) {
        alert('Le message ne peut pas être vide.');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/messages/${currentMessageId}/edit`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            message: newMessage
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer la modal
            bootstrap.Modal.getInstance(document.getElementById('editMessageModal')).hide();
            
            // Recharger les messages pour afficher la modification
            loadDiscussionMessages(currentActivityId);
            
            // Afficher un message de succès
            showToast('Message modifié avec succès', 'success');
        } else {
            alert('Erreur: ' + (data.error || 'Impossible de modifier le message'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification du message.');
    });
}

// Fonction pour afficher un toast (si elle n'existe pas déjà)
function showToast(message, type = 'info') {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    // Créer un conteneur pour les toasts s'il n'existe pas
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Ajouter le toast
    const toastElement = document.createElement('div');
    toastElement.innerHTML = toastHtml;
    toastContainer.appendChild(toastElement);
    
    // Afficher le toast
    const toast = new bootstrap.Toast(toastElement.querySelector('.toast'));
    toast.show();
    
    // Supprimer le toast après qu'il soit caché
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// Variables pour la suppression de message
let messageIdToDelete = null;

// Fonction pour ouvrir la modal de suppression
function deleteMessage(messageId) {
    messageIdToDelete = messageId;
    new bootstrap.Modal(document.getElementById('deleteMessageModal')).show();
}

// Fonction pour confirmer la suppression du message
function confirmDeleteMessage() {
    if (!messageIdToDelete) {
        return;
    }
    
    // Vérifier le CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('ERREUR: Token CSRF non trouvé');
        return;
    }
    
    // Fermer la modal
    bootstrap.Modal.getInstance(document.getElementById('deleteMessageModal')).hide();
    
    // Envoyer la requête de suppression
    fetch(`/messages/${messageIdToDelete}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recharger les messages pour afficher la suppression
            loadDiscussionMessages(currentActivityId);
            
            // Afficher un message de succès
            showToast('Message supprimé avec succès', 'success');
        } else {
            alert('Erreur: ' + (data.error || 'Impossible de supprimer le message'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la suppression du message.');
    })
    .finally(() => {
        messageIdToDelete = null;
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

.message-content-wrapper {
    position: relative;
}

.edit-message-btn {
    position: absolute;
    top: -5px;
    right: 25px;
    padding: 2px 6px;
    font-size: 10px;
    opacity: 0;
    transition: opacity 0.3s;
}

.delete-message-btn {
    position: absolute;
    top: -5px;
    right: -5px;
    padding: 2px 6px;
    font-size: 10px;
    opacity: 0;
    transition: opacity 0.3s;
}

.message-content-wrapper:hover .edit-message-btn,
.message-content-wrapper:hover .delete-message-btn {
    opacity: 1;
}
</style>

<!-- Modal pour modifier un message -->
<div class="modal fade" id="editMessageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Modifier le message
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editedMessage" class="form-label">Message</label>
                    <textarea class="form-control" id="editedMessage" rows="4" placeholder="Modifiez votre message..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveEditedMessage()">
                    <i class="fas fa-save me-2"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour supprimer un message -->
<div class="modal fade" id="deleteMessageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Supprimer le message
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>
                        Êtes-vous sûr de vouloir supprimer ce message ?
                    </div>
                </div>
                <p class="text-muted mb-0">
                    Cette action est irréversible. Le message sera définitivement supprimé de la discussion.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteMessage()">
                    <i class="fas fa-trash me-2"></i>Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
