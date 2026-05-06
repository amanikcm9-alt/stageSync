@extends('layouts.app')

@section('title', 'Détails de l\'activité')

@section('content')

<!-- Page Header -->
<div class="page-header" style="margin-top: 40px;">
    <div class="container">
        <div>
            <h1 class="page-title mb-1">{{ $activity->titre }}</h1>
            <p class="page-subtitle mb-0">Détails et suivi de l'activité</p>
        </div>
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
                        
                        <!-- Bouton Publier support (toujours visible pour les stagiaires) -->
                        @if(auth()->check() && auth()->user()->role->name === 'stagiaire')
                            <a href="/submissions/create/{{ $activity->id }}" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-book me-2"></i>Publier support
                            </a>
                        @endif
                        
                        @if(in_array($activity->statut, ['assignee', 'en_cours']))
                            <button type="button" class="btn btn-danger btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#refusModal">
                                <i class="fas fa-times me-2"></i>Refuser l'activité
                            </button>
                            <button type="button" class="btn btn-info btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#discussionModal">
                                Demander complément d'information
                            </button>
                        @endif
                    @endif
                    
                    @if(auth()->user()->role->name === 'encadrant')
                        <!-- Actions encadrant -->
                        <a href="{{ route('encadrant.activities.index') }}" class="btn btn-secondary btn-sm w-100 mb-2">
                            <i class="fas fa-list me-2"></i>Mes activités
                        </a>
                        
                        @if($activity->statut === 'proposee')
                            <button type="button" class="btn btn-success btn-sm w-100 mb-2" onclick="assignerActivite({{ $activity->id }})">
                                <i class="fas fa-user-plus me-2"></i>Assigner à un stagiaire
                            </button>
                        @endif
                        
                        @if($activity->statut === 'soumise')
                            <button type="button" class="btn btn-danger btn-sm w-100 mb-2" onclick="validerActivite({{ $activity->id }})">
                                <i class="fas fa-check me-2"></i>Valider l'activité
                            </button>
                        @endif
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
    Swal.fire({
        title: 'Démarrer l\'activité',
        html: `
            <div class="text-start">
                <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                    <i class="fas fa-info-circle me-3"></i>
                    <div>
                        <strong>Information :</strong> En démarrant cette activité, une notification sera envoyée à votre encadrant.
                    </div>
                </div>
                <p class="mb-0">Êtes-vous sûr de vouloir démarrer cette activité ?</p>
            </div>
        `,
        icon: 'question',
        iconColor: '#0d6efd',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-play me-2"></i>Démarrer',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Annuler',
        reverseButtons: true,
        customClass: {
            popup: 'swal2-bootstrap5',
            title: 'swal2-bootstrap5-title',
            content: 'swal2-bootstrap5-content',
            actions: 'swal2-bootstrap5-actions',
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
        }
    }).then((result) => {
        if (result.isConfirmed) {
        // Vérifier le CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showAlert('ERREUR: Meta tag CSRF token non trouvé !', 'danger', 'Erreur technique');
            return;
        }
        
        const token = csrfToken.getAttribute('content');
        if (!token) {
            showAlert('ERREUR: CSRF token vide !', 'danger', 'Erreur technique');
            return;
        }
        
        // Afficher un message de chargement avec SweetAlert2
        Swal.fire({
            title: 'Démarrage en cours...',
            html: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(`/activities/${activityId}/realiser`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Activité démarrée !',
                    html: `
                        <div class="text-start">
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="fas fa-check-circle me-3"></i>
                                <div>
                                    <strong>Succès :</strong> ${data.message || 'Activité démarrée avec succès !'}
                                </div>
                            </div>
                            <p class="mb-0">La page va se recharger automatiquement...</p>
                        </div>
                    `,
                    icon: 'success',
                    iconColor: '#198754',
                    confirmButtonColor: '#198754',
                    confirmButtonText: '<i class="fas fa-check me-2"></i>OK',
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'swal2-bootstrap5',
                        confirmButton: 'btn btn-success'
                    }
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Erreur de démarrage',
                    html: `
                        <div class="text-start">
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-triangle me-3"></i>
                                <div>
                                    <strong>Erreur :</strong> ${data.error || 'Erreur lors du démarrage de l\'activité'}
                                </div>
                            </div>
                        </div>
                    `,
                    icon: 'error',
                    iconColor: '#dc3545',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: '<i class="fas fa-times me-2"></i>OK',
                    customClass: {
                        popup: 'swal2-bootstrap5',
                        confirmButton: 'btn btn-danger'
                    }
                });
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            Swal.fire({
                title: 'Erreur technique',
                html: `
                    <div class="text-start">
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-3"></i>
                            <div>
                                Une erreur technique est survenue lors du démarrage de l\'activité. Veuillez réessayer.
                            </div>
                        </div>
                    </div>
                `,
                icon: 'error',
                iconColor: '#dc3545',
                confirmButtonColor: '#dc3545',
                confirmButtonText: '<i class="fas fa-times me-2"></i>OK',
                customClass: {
                    popup: 'swal2-bootstrap5',
                    confirmButton: 'btn btn-danger'
                }
            });
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
    console.log('refuserActivite appelé avec activityId:', activityId);
    
    // Solution simple et directe avec confirm() et prompt()
    if (confirm('Êtes-vous sûr de vouloir refuser cette activité ?')) {
        const raison = prompt('Veuillez indiquer la raison du refus :');
        
        if (raison !== null && raison.trim() !== '') {
            console.log('Envoi du refus pour activité ID:', activityId, 'raison:', raison);
            
            // Créer un formulaire temporaire pour l'envoi
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/activities/${activityId}/refuser`;
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
            
            // Ajouter la raison avec le bon nom de champ
            const raisonInput = document.createElement('input');
            raisonInput.type = 'hidden';
            raisonInput.name = 'raison';
            raisonInput.value = raison;
            form.appendChild(raisonInput);
            
            // Ajouter le formulaire à la page et le soumettre
            document.body.appendChild(form);
            console.log('Formulaire créé et prêt à être soumis');
            form.submit();
        } else {
            console.log('Raison vide ou annulée');
        }
    } else {
        console.log('Refus annulé');
    }
}

function demanderInfoActivite(activityId) {
    console.log('demanderInfoActivite appelé avec activityId:', activityId);
    
    // Solution simple et directe avec confirm() et prompt()
    if (confirm('Voulez-vous demander des informations complémentaires sur cette activité ?')) {
        const question = prompt('Quelles informations souhaitez-vous obtenir ?');
        
        if (question !== null && question.trim() !== '') {
            console.log('Envoi de la demande d\'information pour activité ID:', activityId, 'question:', question);
            
            // Créer un formulaire temporaire pour l'envoi
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/activities/${activityId}/demander-info`;
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
            
            // Ajouter la question
            const questionInput = document.createElement('input');
            questionInput.type = 'hidden';
            questionInput.name = 'question';
            questionInput.value = question;
            form.appendChild(questionInput);
            
            // Ajouter le formulaire à la page et le soumettre
            document.body.appendChild(form);
            console.log('Formulaire créé et prêt à être soumis');
            form.submit();
        } else {
            console.log('Question vide ou annulée');
        }
    } else {
        console.log('Demande d\'information annulée');
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
                location.reload();
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la demande d\'information.');
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

// Variable pour stocker l'ID du message à supprimer
let messageIdToDelete = null;

// Fonction pour supprimer un message
function deleteMessage(messageId) {
    messageIdToDelete = messageId;
    
    // Ouvrir la modal Bootstrap de confirmation
    const modalElement = document.getElementById('deleteMessageModal');
    if (modalElement) {
        // Utiliser l'API Bootstrap 5
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        console.error('Modal element not found');
        // Fallback vers confirm()
        if (confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
            performDelete(messageId);
        }
    }
}

// Fonction pour effectuer la suppression
function performDelete(messageId) {
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
            
            // Afficher un message de succès simple
            alert('Message supprimé avec succès');
        } else {
            alert('Erreur lors de la suppression du message: ' + (data.error || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la suppression du message');
    });
}

// Fonction pour confirmer la suppression du message
document.addEventListener('DOMContentLoaded', function() {
    const confirmDeleteBtn = document.getElementById('confirmDeleteMessage');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (messageIdToDelete) {
                // Effectuer la suppression
                performDelete(messageIdToDelete);
                
                // Fermer la modal
                const modalElement = document.getElementById('deleteMessageModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
                
                // Réinitialiser l'ID
                messageIdToDelete = null;
            }
        });
    }
});

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

<!-- Modal de discussion avec l'encadrant -->
<div class="modal fade" id="discussionModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-comments me-2"></i>Discussion avec l'encadrant
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Zone d'affichage des messages -->
                <div class="messages-container" style="height: 400px; overflow-y: auto; padding: 20px; background-color: #f8f9fa;">
                    <div id="messagesList">
                        <!-- Messages chargés dynamiquement -->
                        <div class="text-center text-muted mb-3">
                            <small>Cliquez pour charger les messages existants...</small>
                        </div>
                    </div>
                </div>
                
                <!-- Zone de saisie du message -->
                <div class="message-input-area p-3 border-top">
                    <form id="messageForm">
                        <div class="d-flex gap-2">
                            <textarea class="form-control" id="messageTextarea" rows="2" 
                                      placeholder="Tapez votre message ici..." 
                                      style="resize: none;"></textarea>
                            <button type="submit" class="btn btn-primary align-self-end">
                                <i class="fas fa-paper-plane me-1"></i>Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression de message -->
<div class="modal fade" id="deleteMessageModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmation de suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-3"></i>
                    <div>
                        Êtes-vous sûr de vouloir supprimer ce message ? Cette action est irréversible.
                    </div>
                </div>
                <p class="mb-0">Le message sera définitivement supprimé de la discussion.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteMessage">
                    <i class="fas fa-trash me-2"></i>Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de refus -->
<div class="modal fade" id="refusModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmation de refus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="refusForm" method="POST" action="{{ route('activities.refuser', $activity) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="justification" class="form-label fw-bold">
                            <i class="fas fa-comment-alt me-2"></i>Veuillez indiquer la raison du refus <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="justification" name="raison" rows="4" 
                                  placeholder="Expliquez pourquoi vous refusez cette activité..." required></textarea>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Cette justification sera transmise à l'encadrant.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-check me-2"></i>Confirmer le refus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Gestion de la modal de refus
document.addEventListener('DOMContentLoaded', function() {
    const refusForm = document.getElementById('refusForm');
    if (refusForm) {
        refusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Fermer la modal
            const refusModal = bootstrap.Modal.getInstance(document.getElementById('refusModal'));
            refusModal.hide();
            
            // Afficher un message de succès
            showSuccessMessage('Activité refusée avec succès !');
            
            // Soumettre le formulaire après un court délai
            setTimeout(() => {
                this.submit();
            }, 1500);
        });
    }
    
    // Gestion de la modal de discussion
    const messageForm = document.getElementById('messageForm');
    const messageTextarea = document.getElementById('messageTextarea');
    const messagesList = document.getElementById('messagesList');
    
    // Gérer l'envoi du message
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageTextarea.value.trim();
            if (!message) {
                return;
            }
            
            // Ajouter le message à la liste
            addMessageToList(message, 'sent');
            
            // Vider le textarea
            messageTextarea.value = '';
            
            // Simuler l'envoi au serveur (à remplacer par un vrai appel API)
            sendMessageToServer(message);
        });
    }
    
    // Charger les messages existants quand la modal s'ouvre
    const discussionModal = document.getElementById('discussionModal');
    if (discussionModal) {
        discussionModal.addEventListener('show.bs.modal', function() {
            loadExistingMessages();
        });
    }
});

// Fonction pour ajouter un message à la liste
function addMessageToList(message, type) {
    const messagesList = document.getElementById('messagesList');
    if (!messagesList) return;
    
    // Supprimer le message "Aucun message" s'il existe
    const noMessage = messagesList.querySelector('.text-center.text-muted');
    if (noMessage) {
        noMessage.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `message mb-3 ${type === 'sent' ? 'd-flex justify-content-end' : 'd-flex justify-content-start'}`;
    
    if (type === 'sent') {
        // Conteneur pour les messages envoyés alignés à droite
        const messageContainer = document.createElement('div');
        messageContainer.className = 'd-flex flex-column align-items-end';
        messageContainer.style.maxWidth = '80%';
        
        // Heure au-dessus de la bulle en noir
        const timeElement = document.createElement('div');
        timeElement.className = 'small text-dark mb-2';
        timeElement.textContent = new Date().toLocaleTimeString();
        messageContainer.appendChild(timeElement);
        
        // Bulle de message bleue agrandie
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'bg-primary text-white rounded p-3 position-relative';
        bubbleDiv.style.minWidth = '200px';
        
        // Contenu de la bulle avec texte centré et icônes en bas
        bubbleDiv.innerHTML = `
            <div class="d-flex flex-column">
                <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                    <div class="message-text text-center">${message}</div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-2" style="opacity: 0.3; transition: opacity 0.3s;">
                    <button type="button" class="btn btn-sm btn-outline-light text-white" onclick="editMessage(this)" style="padding: 2px 6px; font-size: 10px; border-radius: 4px;">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-light text-white" onclick="deleteMessage(this)" style="padding: 2px 6px; font-size: 10px; border-radius: 4px;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        messageContainer.appendChild(bubbleDiv);
        messageDiv.appendChild(messageContainer);
        
        // Event listeners pour les icônes
        bubbleDiv.addEventListener('mouseenter', function() {
            const actions = this.querySelector('.d-flex.justify-content-end');
            if (actions) actions.style.opacity = '1';
        });
        
        bubbleDiv.addEventListener('mouseleave', function() {
            const actions = this.querySelector('.d-flex.justify-content-end');
            if (actions) actions.style.opacity = '0.3';
        });
        
    } else {
        // Messages reçus (structure standard)
        const messageContainer = document.createElement('div');
        messageContainer.className = 'd-flex flex-column align-items-start';
        messageContainer.style.maxWidth = '70%';
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'bg-light text-dark rounded p-3';
        bubbleDiv.innerHTML = `
            <div class="d-flex flex-column">
                <div class="message-text">${message}</div>
                <div class="small text-muted mt-2">${new Date().toLocaleTimeString()}</div>
            </div>
        `;
        
        messageContainer.appendChild(bubbleDiv);
        messageDiv.appendChild(messageContainer);
    }
    
    messagesList.appendChild(messageDiv);
    
    // Ajouter l'event listener pour afficher les icônes au survol
    if (type === 'sent') {
        const messageContent = messageDiv.querySelector('.bg-primary');
        messageContent.addEventListener('mouseenter', function() {
            const actions = this.querySelector('.d-flex.justify-content-end');
            if (actions) {
                actions.style.opacity = '1';
            }
        });
        
        messageContent.addEventListener('mouseleave', function() {
            const actions = this.querySelector('.d-flex.justify-content-end');
            if (actions) {
                actions.style.opacity = '0.3';
            }
        });
        
        // Rendre les icônes cliquables dès le début
        const actions = messageContent.querySelector('.d-flex.justify-content-end');
        if (actions) {
            actions.style.opacity = '0.5';
        }
    }
    
    // Scroller vers le bas
    messagesList.scrollTop = messagesList.scrollHeight;
}

// Fonction pour charger les messages existants
function loadExistingMessages() {
    const activityId = {{ $activity->id }};
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/messages/activity/${activityId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Vider la liste actuelle
            const messagesList = document.getElementById('messagesList');
            if (messagesList) {
                messagesList.innerHTML = '';
                
                // Ajouter chaque message (uniquement les messages de type 'discussion')
                data.messages.forEach(msg => {
                    // Ignorer les messages de type 'demande_info' pour ne pas afficher les notifications système
                    if (msg.type === 'demande_info') {
                        return;
                    }
                    
                    const currentUser = {{ Auth::user()->id }};
                    const messageType = msg.sender_id === currentUser ? 'sent' : 'received';
                    addMessageToList(msg.message, messageType);
                });
                
                // Afficher le nombre de messages
                const messageCount = data.messages.length;
                const messageText = messageCount === 0 ? 'Aucun message' : `${messageCount} message(s)`;
                const messageHtml = `<small class="text-muted mb-2">${messageText}</small>`;
                messagesList.insertAdjacentHTML('afterbegin', messageHtml);
            }
        } else {
            console.error('Erreur:', data.error);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Fonction pour changer automatiquement le statut assignée en en cours
function changeStatutToEnCours() {
    const activityId = {{ $activity->id }};
    const currentStatut = '{{ $activity->statut }}';
    
    if (currentStatut === 'assignee') {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/activities/${activityId}/change-statut`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                statut: 'en_cours'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Statut changé à en cours');
                // Mettre à jour le badge de statut dans la modal
                const statusBadge = document.getElementById('activityStatusBadge');
                if (statusBadge) {
                    statusBadge.textContent = 'en cours';
                    statusBadge.className = 'badge bg-info text-dark ms-2';
                }
            } else {
                console.error('Erreur:', data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    }
}

// Fonction pour supprimer un message
function deleteMessage(button) {
    const bubble = button.closest('.bg-primary');
    const messageContainer = bubble.closest('.d-flex.flex-column.align-items-end');
    const messageDiv = messageContainer.closest('.message');
    
    if (confirm('Voulez-vous vraiment supprimer ce message ?')) {
        messageDiv.remove();
        
        // Vérifier s'il reste des messages
        const messagesList = document.getElementById('messagesList');
        if (messagesList && messagesList.children.length === 0) {
            messagesList.innerHTML = `
                <div class="text-center text-muted mb-3">
                    <small>Aucun message pour le moment</small>
                </div>
            `;
        }
    }
}

// Fonction pour modifier un message
function editMessage(button) {
    const bubble = button.closest('.bg-primary');
    const messageText = bubble.querySelector('.message-text');
    const currentText = messageText.textContent.trim();
    
    // Créer un input pour l'édition
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control form-control-sm text-white bg-primary border-light';
    input.value = currentText;
    input.style.maxWidth = '200px';
    
    // Remplacer le texte par l'input
    messageText.replaceWith(input);
    input.focus();
    input.select();
    
    // Gérer la sauvegarde de la modification
    const saveEdit = function() {
        const newText = input.value.trim();
        if (newText && newText !== currentText) {
            const newTextDiv = document.createElement('div');
            newTextDiv.className = 'message-text';
            newTextDiv.textContent = newText;
            input.replaceWith(newTextDiv);
            
            // Mettre à jour l'heure
            const timeDiv = messageContent.querySelector('.small');
            if (timeDiv) {
                timeDiv.textContent = new Date().toLocaleTimeString() + ' (modifié)';
            }
        } else {
            const originalTextDiv = document.createElement('div');
            originalTextDiv.className = 'message-text';
            originalTextDiv.textContent = currentText;
            input.replaceWith(originalTextDiv);
        }
    };
    
    // Sauvegarder au blur ou à la touche Entrée
    input.addEventListener('blur', saveEdit);
    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveEdit();
        }
    });
}

// Fonction pour afficher un message de succès
function showSuccessMessage(message) {
    // Créer un conteneur pour les alertes s'il n'existe pas
    let alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alertContainer';
        alertContainer.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1060;
            max-width: 400px;
            width: 100%;
        `;
        document.body.appendChild(alertContainer);
    }
    
    // Créer l'alerte de succès
    const alertId = 'alert-' + Date.now();
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show shadow-lg border-0" role="alert" id="${alertId}">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3" style="font-size: 1.5rem;"></i>
                <div class="flex-grow-1">
                    <strong>Succès !</strong><br>
                    <span class="small">${message}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    `;
    
    // Ajouter l'alerte au conteneur
    alertContainer.insertAdjacentHTML('beforeend', alertHtml);
    
    // L'alerte restera affichée indéfiniment jusqu'à ce que l'utilisateur clique sur le X
}

// Fonction pour envoyer le message au serveur
function sendMessageToServer(message) {
    const activityId = {{ $activity->id }};
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/messages/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            message: message,
            activity_id: activityId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Message envoyé avec succès');
            // Recharger les messages
            loadExistingMessages();
            
            // S'assurer que le statut reste correct
            setTimeout(() => {
                const statusBadge = document.getElementById('activityStatusBadge');
                if (statusBadge) {
                    // Forcer le statut à rester 'en cours'
                    statusBadge.textContent = 'en cours';
                    statusBadge.className = 'badge bg-info text-dark ms-2';
                }
            }, 500);
        } else {
            console.error('Erreur:', data.error);
            alert('Erreur lors de l\'envoi du message: ' + (data.error || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'envoi du message');
    });
}

// Fonction pour afficher des messages Bootstrap 5
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
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-suppression après 6 secondes
    setTimeout(() => {
        const alertElement = document.getElementById(alertId);
        if (alertElement && alertElement.parentNode) {
            const bsAlert = new bootstrap.Alert(alertElement);
            bsAlert.close();
        }
    }, 6000);
}

function getAlertConfig(type) {
    const configs = {
        'success': {
            icon: 'fa-check-circle',
            iconColor: '#198754',
            borderColor: '#198754'
        },
        'danger': {
            icon: 'fa-exclamation-triangle',
            iconColor: '#dc3545',
            borderColor: '#dc3545'
        },
        'warning': {
            icon: 'fa-exclamation-circle',
            iconColor: '#ffc107',
            borderColor: '#ffc107'
        },
        'info': {
            icon: 'fa-info-circle',
            iconColor: '#0dcaf0',
            borderColor: '#0dcaf0'
        }
    };
    return configs[type] || configs.info;
}

function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alertContainer';
    container.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1055;
        max-width: 400px;
        width: 100%;
    `;
    document.body.appendChild(container);
    return container;
}

// Fonction pour évaluer une activité
function evaluerActivite(activityId) {
    const note = prompt("Veuillez attribuer une note à l'activité (sur 20) :");
    if (note === null) return; // L'utilisateur a annulé
    
    const noteNum = parseFloat(note);
    if (isNaN(noteNum) || noteNum < 0 || noteNum > 20) {
        alert('Veuillez entrer une note valide entre 0 et 20.');
        return;
    }
    
    const commentaire = prompt("Veuillez ajouter un commentaire sur l'évaluation :");
    if (commentaire === null) return; // L'utilisateur a annulé
    
    if (commentaire.trim() === '') {
        alert('Veuillez ajouter un commentaire pour l\'évaluation.');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('ERREUR: Token CSRF non trouvé');
        return;
    }
    
    fetch(`/activities/${activityId}/evaluer`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ 
            note: noteNum,
            commentaire: commentaire
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Activité évaluée avec succès !');
            location.reload();
        } else {
            alert('Erreur: ' + (data.error || 'Impossible d\'évaluer l\'activité'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'évaluation de l\'activité');
    });
}
</script>
@endsection
