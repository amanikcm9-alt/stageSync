@extends('layouts.app')

@section('title', 'Modifier une activité')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title">Modifier une activité</h1>
        <p class="page-subtitle">Mettre à jour les informations de l'activité</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Modifier l'activité
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('activities.update', $activity) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informations générales -->
                        <div class="mb-4">
                            <h6 class="text-warning mb-3">Informations générales</h6>
                            
                            <div class="mb-3">
                                <label for="titre" class="form-label">Titre de l'activité *</label>
                                <input type="text" class="form-control" id="titre" name="titre" 
                                       value="{{ old('titre', $activity->titre) }}" required maxlength="255">
                                @error('titre')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description', $activity->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="objectifs" class="form-label">Objectifs pédagogiques</label>
                                <textarea class="form-control" id="objectifs" name="objectifs" rows="3">{{ old('objectifs', $activity->objectifs) }}</textarea>
                                @error('objectifs')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="livrables_attendus" class="form-label">Livrables attendus</label>
                                <textarea class="form-control" id="livrables_attendus" name="livrables_attendus" rows="3">{{ old('livrables_attendus', $activity->livrables_attendus) }}</textarea>
                                @error('livrables_attendus')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Planification -->
                        <div class="mb-4">
                            <h6 class="text-warning mb-3">Planification</h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_debut" class="form-label">Date de début</label>
                                        <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                               value="{{ old('date_debut', $activity->date_debut ? $activity->date_debut->format('Y-m-d') : '') }}">
                                        @error('date_debut')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_fin" class="form-label">Date de fin</label>
                                        <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                               value="{{ old('date_fin', $activity->date_fin ? $activity->date_fin->format('Y-m-d') : '') }}">
                                        @error('date_fin')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="date_limite" class="form-label">Date limite de rendu</label>
                                <input type="date" class="form-control" id="date_limite" name="date_limite" 
                                       value="{{ old('date_limite', $activity->date_limite ? $activity->date_limite->format('Y-m-d') : '') }}">
                                @error('date_limite')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Priorité -->
                        <div class="mb-4">
                            <h6 class="text-warning mb-3">Priorité</h6>
                            
                            <div class="mb-3">
                                <label for="priorite" class="form-label">Niveau de priorité *</label>
                                <select class="form-select" id="priorite" name="priorite" required>
                                    <option value="">Choisir une priorité</option>
                                    <option value="basse" {{ old('priorite', $activity->priorite) == 'basse' ? 'selected' : '' }}>Basse</option>
                                    <option value="moyenne" {{ old('priorite', $activity->priorite) == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                                    <option value="haute" {{ old('priorite', $activity->priorite) == 'haute' ? 'selected' : '' }}>Haute</option>
                                    <option value="urgente" {{ old('priorite', $activity->priorite) == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                </select>
                                @error('priorite')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Assignation -->
                        <div class="mb-4">
                            <h6 class="text-warning mb-3">Assignation</h6>
                            
                            <div class="mb-3">
                                <label for="stagiaire_id" class="form-label">Stagiaire (optionnel)</label>
                                <select class="form-select" id="stagiaire_id" name="stagiaire_id">
                                    <option value="">Ne pas assigner maintenant (activité proposée)</option>
                                    {{-- Débogage : Afficher le nombre de stagiaires --}}
                                    @if($stagiaires->count() === 0)
                                        <option value="" disabled>Aucun stagiaire trouvé</option>
                                    @endif
                                    @foreach($stagiaires as $stagiaire)
                                        <option value="{{ $stagiaire->id }}" 
                                                data-prenom="{{ $stagiaire->prenom }}"
                                                data-nom="{{ $stagiaire->nom }}"
                                                data-offre="{{ $stagiaire->offre_stage ? $stagiaire->offre_stage->titre : 'Aucune offre' }}"
                                                {{ old('stagiaire_id', $activity->stagiaire_id) == $stagiaire->id ? 'selected' : '' }}>
                                            {{ $stagiaire->prenom }} {{ $stagiaire->nom }} 
                                            @if($stagiaire->offre_stage) - {{ $stagiaire->offre_stage->titre }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('stagiaire_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Laissez vide pour proposer l'activité à tous vos stagiaires</small>
                            </div>
                            
                                                    </div>
                        
                        <!-- Actions -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Mettre à jour l'activité
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Informations sur l'activité -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations sur l'activité
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Statut actuel</h6>
                    <div class="mb-3">
                        <span class="badge bg-{{ $activity->statut_color }}">
                            {{ $activity->statut_label }}
                        </span>
                    </div>
                    
                    @if($activity->stagiaire)
                        <h6 class="text-primary">Stagiaire assigné</h6>
                        <p class="mb-2">
                            <strong>{{ $activity->stagiaire->prenom }} {{ $activity->stagiaire->nom }}</strong>
                        </p>
                        <small class="text-muted">{{ $activity->stagiaire->email }}</small>
                    @endif
                    
                    <h6 class="text-primary mt-3">Dates importantes</h6>
                    <ul class="small">
                        <li><strong>Créée le:</strong> {{ $activity->created_at->format('d/m/Y') }}</li>
                        @if($activity->date_debut)
                            <li><strong>Début:</strong> {{ $activity->date_debut->format('d/m/Y') }}</li>
                        @endif
                        @if($activity->date_limite)
                            <li><strong>Limite:</strong> {{ $activity->date_limite->format('d/m/Y') }}</li>
                        @endif
                    </ul>
                </div>
            </div>
            
           
            
            <!-- Actions rapides -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-2"></i>Voir l'activité
                        </a>
                        @if($activity->stagiaire)
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="contacterStagiaire({{ $activity->stagiaire->id }}, '{{ $activity->stagiaire->name }}')">
                                <i class="fas fa-envelope me-2"></i>Contacter le stagiaire
                            </button>
                        @endif
                                            </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Fonction pour contacter le stagiaire
function contacterStagiaire(stagiaireId, stagiaireName) {
    console.log('DEBUG: Contact du stagiaire ID:', stagiaireId, 'Nom:', stagiaireName);
    
    // Créer une modal de contact
    const modalHtml = `
        <div class="modal fade" id="contactStagiaireModal" tabindex="-1" aria-labelledby="contactStagiaireModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="contactStagiaireModalLabel">
                            <i class="fas fa-envelope me-2"></i>Contacter le stagiaire
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="messageSubject" class="form-label">Sujet du message</label>
                            <input type="text" class="form-control" id="messageSubject" placeholder="Sujet de votre message" value="Concernant votre activité">
                        </div>
                        <div class="mb-3">
                            <label for="messageContent" class="form-label">Message</label>
                            <textarea class="form-control" id="messageContent" rows="5" placeholder="Tapez votre message ici..."></textarea>
                        </div>
                                            </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                        <button type="button" class="btn btn-info" onclick="envoyerMessage(${stagiaireId})">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer le message
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter la modal au body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Afficher la modal
    const modal = new bootstrap.Modal(document.getElementById('contactStagiaireModal'));
    modal.show();
    
    // Nettoyer la modal après fermeture
    document.getElementById('contactStagiaireModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Fonction pour envoyer le message
function envoyerMessage(stagiaireId) {
    console.log('DEBUG: Envoi du message au stagiaire ID:', stagiaireId);
    
    const subject = document.getElementById('messageSubject').value;
    const content = document.getElementById('messageContent').value;
    
    if (!subject || !content) {
        alert('Veuillez remplir tous les champs');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('Erreur: Token CSRF non trouvé');
        return;
    }
    
    // Désactiver le bouton d'envoi
    const sendBtn = event.target;
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
    
    // Envoyer le message via AJAX
    fetch('/messages/send', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            stagiaire_id: stagiaireId,
            subject: subject,
            content: content
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer la modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('contactStagiaireModal'));
            modal.hide();
            
            // Afficher un toast de succès
            showToast('Message envoyé avec succès', 'success');
        } else {
            alert('Erreur lors de l\'envoi: ' + (data.error || 'Erreur inconnue'));
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Envoyer le message';
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur réseau lors de l\'envoi du message');
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Envoyer le message';
    });
}

// Fonction pour afficher un toast centré permanent
function showToast(message, type = 'info') {
    const toastHtml = `
        <div class="toast-container position-fixed top-50 start-50 translate-middle d-flex justify-content-center align-items-center" style="z-index: 9999;">
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 300px; max-width: 400px;">
                <div class="toast-header bg-${type} text-white">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                    <strong class="me-auto">${type === 'success' ? 'Succès' : 'Information'}</strong>
                    <button type="button" class="btn-close btn-close-white ms-2 me-2" data-bs-dismiss="toast" aria-label="Fermer"></button>
                </div>
                <div class="toast-body text-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-${type === 'success' ? 'check-circle text-success' : 'info-circle text-info'} me-2"></i>
                        <span>${message}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('afterbegin', toastHtml);
    
    const toastElement = document.querySelector('.toast');
    const toast = new bootstrap.Toast(toastElement, {
        autohide: false,  // Le toast reste permanent
        delay: 0
    });
    
    toast.show();
}
</script>


@endsection
