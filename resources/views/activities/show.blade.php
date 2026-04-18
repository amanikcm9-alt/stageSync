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
                        @if($activity->statut === 'assignee')
                            <a href="{{ route('activities.realiser', $activity) }}" class="btn btn-primary btn-sm w-100 mb-2">
                                <i class="fas fa-play me-2"></i>Démarrer l'activité
                            </a>
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
                            <button type="button" class="btn btn-warning btn-sm w-100" onclick="demanderInfo({{ $activity->id }})">
                                <i class="fas fa-question me-2"></i>Demander info
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
                            @foreach(auth()->user()->stagiaires as $stagiaire)
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
    });
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

function demanderInfo(activityId) {
    const message = prompt('Votre question :');
    if (message) {
        fetch(`/activities/${activityId}/demander-info`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({message})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Demande envoyée à l\'encadrant');
            }
        });
    }
}

function supprimerActivite(activityId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')) {
        fetch(`/activities/${activityId}`, {method: 'DELETE'})
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route("encadrant.dashboard") }}';
                }
            });
    }
}
</script>
@endsection
