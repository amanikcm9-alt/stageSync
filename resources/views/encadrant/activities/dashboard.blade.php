@extends('layouts.app')

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

    <!-- Statistiques principales -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-primary mb-1">{{ $stats['total_activities'] }}</h5>
                    <small class="text-muted">Total activités</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-warning mb-1">{{ $stats['en_cours'] }}</h5>
                    <small class="text-muted">En cours</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-info mb-1">{{ $stats['soumises'] }}</h5>
                    <small class="text-muted">Soumises</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-success mb-1">{{ $stats['validees'] }}</h5>
                    <small class="text-muted">Validées</small>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Section Mes Stagiaires -->
    <a href="#" onclick="toggleStagiairesSection()" class="card border-0 shadow-sm mb-3 text-decoration-none">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-users"></i> Mes Stagiaires
                <span class="badge bg-light text-primary ms-2">{{ $stagiaires->count() }}</span>
                <small class="float-end">
                    <i class="fas fa-chevron-down" id="stagiaires-chevron"></i>
                </small>
            </h6>
        </div>
        <div class="card-body">
            @if($stagiaires->count() > 0)
            <div class="row">
                @foreach($stagiaires->take(3) as $stagiaire)
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-2">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                    {{ strtoupper(substr($stagiaire->prenom, 0, 1)) }}{{ strtoupper(substr($stagiaire->nom, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $stagiaire->prenom }} {{ $stagiaire->nom }}</div>
                                    <small class="text-muted">{{ $stagiaire->activities->count() }} activités</small>
                                </div>
                            </div>
                            
                            <!-- Aperçu de l'offre de stage -->
                            @if($stagiaire->offre_stage)
                            <div class="small">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-briefcase text-primary me-1" style="font-size: 0.75rem;"></i>
                                        <small class="text-muted">
                                            <strong>{{ Str::limit($stagiaire->offre_stage->titre, 25) }}</strong>
                                            @if($stagiaire->offre_stage->entreprise)
                                            - {{ $stagiaire->offre_stage->entreprise->nom }}
                                            @endif
                                        </small>
                                    </div>
                                    <button type="button" class="btn btn-xs btn-outline-info" 
                                        data-offre-id="{{ $stagiaire->offre_stage->id }}" 
                                        data-offre-titre="{{ $stagiaire->offre_stage->titre }}"
                                        data-stagiaire-id="{{ $stagiaire->id }}"
                                        data-stagiaire-nom="{{ $stagiaire->nom }}"
                                        data-stagiaire-prenom="{{ $stagiaire->prenom }}"
                                        data-stagiaire-email="{{ $stagiaire->email }}"
                                        data-stagiaire-telephone="{{ $stagiaire->telephone ?? '' }}"
                                        data-stagiaire-activities-count="{{ $stagiaire->activities->count() }}"
                                        data-stagiaire-activities-en-cours="{{ $stagiaire->activities->where('statut', 'en_cours')->count() }}"
                                        onclick="voirDetailsOffre(this)" 
                                        style="font-size: 0.7rem; padding: 2px 6px;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            @else
                            <div class="small">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exclamation-triangle text-warning me-1" style="font-size: 0.75rem;"></i>
                                        <small class="text-muted">Aucune offre</small>
                                    </div>
                                                                    </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                @if($stagiaires->count() > 3)
                <div class="text-center">
                    <small class="text-muted">Et {{ $stagiaires->count() - 3 }} autre(s) stagiaire(s)</small>
                </div>
                @endif
            </div>
            @else
            <div class="text-center py-2">
                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                <h6 class="text-muted small">Aucun stagiaire affecté</h6>
            </div>
            @endif
        </div>
    </a>

    <!-- Section Notifications Récentes effacée -->

    <!-- Section Mes Documents effacée -->
</div>
@endsection

@section('scripts')
<script>

// Fonction pour afficher/masquer la section détaillée des stagiaires
function toggleStagiairesSection() {
    const detailsSection = document.getElementById('stagiaires-details');
    const chevron = document.getElementById('stagiaires-chevron');
    
    if (detailsSection.style.display === 'none') {
        detailsSection.style.display = 'block';
        chevron.classList.remove('fa-chevron-down');
        chevron.classList.add('fa-chevron-up');
        
        // Scroller vers la section détaillée
        setTimeout(() => {
            detailsSection.scrollIntoView({ behavior: 'smooth' });
        }, 100);
    } else {
        detailsSection.style.display = 'none';
        chevron.classList.remove('fa-chevron-up');
        chevron.classList.add('fa-chevron-down');
    }
}

// Fonction pour voir les détails d'une offre de stage
function voirDetailsOffre(button) {
    const offreId = button.getAttribute('data-offre-id');
    const offreTitre = button.getAttribute('data-offre-titre');
    const stagiaireId = button.getAttribute('data-stagiaire-id');
    const stagiaireNom = button.getAttribute('data-stagiaire-nom');
    const stagiairePrenom = button.getAttribute('data-stagiaire-prenom');
    const stagiaireEmail = button.getAttribute('data-stagiaire-email');
    const stagiaireTelephone = button.getAttribute('data-stagiaire-telephone');
    const stagiaireActivitiesCount = button.getAttribute('data-stagiaire-activities-count');
    const stagiaireActivitiesEnCours = button.getAttribute('data-stagiaire-activities-en-cours');
    console.log('DEBUG: voirDetailsOffre appelé avec offreId:', offreId, 'offreTitre:', offreTitre, 'stagiaireEmail:', stagiaireEmail);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('ERREUR: Meta tag CSRF token non trouvé!');
        alert('ERREUR: Meta tag CSRF token non trouvé!');
        return;
    }
    
    const token = csrfToken.getAttribute('content');
    console.log('DEBUG: CSRF token trouvé:', token);
    
    // Afficher un loader
    const modalHtml = '<div class="modal fade" id="offreDetailsModal" tabindex="-1">' +
        '<div class="modal-dialog modal-lg">' +
            '<div class="modal-content">' +
                '<div class="modal-header">' +
                    '<h5 class="modal-title">' +
                        '<i class="fas fa-briefcase text-info me-2"></i>' +
                        'Détails de l\'offre: ' + offreTitre +
                    '</h5>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>' +
                '</div>' +
                '<div class="modal-body">' +
                    '<div class="text-center">' +
                        '<div class="spinner-border text-info" role="status">' +
                            '<span class="visually-hidden">Chargement...</span>' +
                        '</div>' +
                        '<p class="mt-2">Chargement des détails de l\'offre...</p>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
    
    // Ajouter la modale au body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    console.log('DEBUG: Modale HTML ajoutée au body');
    
    // Afficher la modale
    const modalElement = document.getElementById('offreDetailsModal');
    if (!modalElement) {
        console.error('ERREUR: Élément modale non trouvé!');
        alert('ERREUR: Élément modale non trouvé!');
        return;
    }
    
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
    console.log('DEBUG: Modale affichée');
    
    // URL de l'API
    const apiUrl = `/offres/${offreId}/details`;
    console.log('DEBUG: Appel API vers:', apiUrl);
    
    // Charger les détails de l'offre
    fetch(apiUrl, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('DEBUG: Response status:', response.status);
        console.log('DEBUG: Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('DEBUG: Données reçues:', data);
        
        if (data.success) {
            const offre = data.offre;
            const modalBody = document.querySelector('#offreDetailsModal .modal-body');
            
            modalBody.innerHTML = '<div class="row">' +
                '<div class="col-12">' +
                    '<div class="mb-3">' +
                        '<h5 class="text-primary">' + offre.titre + '</h5>' +
                        (offre.entreprise ? '<p class="text-muted"><i class="fas fa-building me-2"></i>' + offre.entreprise.nom + '</p>' : '') +
                    '</div>' +
                    
                    (offre.description ? 
                    '<div class="mb-3">' +
                        '<h6 class="text-muted">Description</h6>' +
                        '<p class="text-muted">' + offre.description + '</p>' +
                    '</div>' : '') +
                    
                    (offre.missions ? 
                    '<div class="mb-3">' +
                        '<h6 class="text-muted">Missions</h6>' +
                        '<p class="text-muted">' + offre.missions + '</p>' +
                    '</div>' : '') +
                    
                    '<div class="row mb-3">' +
                        '<div class="col-md-6">' +
                            '<div class="d-flex align-items-center">' +
                                '<i class="fas fa-calendar-alt text-info me-2"></i>' +
                                '<div>' +
                                    '<small class="text-muted d-block">Période</small>' +
                                    '<strong>' + (offre.date_debut ? new Date(offre.date_debut).toLocaleDateString('fr-FR') : 'Non définie') + ' - ' + (offre.date_fin ? new Date(offre.date_fin).toLocaleDateString('fr-FR') : 'Non définie') + '</strong>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                            '<div class="d-flex align-items-center">' +
                                '<i class="fas fa-clock text-warning me-2"></i>' +
                                '<div>' +
                                    '<small class="text-muted d-block">Durée</small>' +
                                    '<strong>' + offre.duree_semaines + ' semaines</strong>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    
                    (offre.remuneration ? 
                    '<div class="mb-3">' +
                        '<div class="d-flex align-items-center">' +
                            '<i class="fas fa-euro-sign text-success me-2"></i>' +
                            '<div>' +
                                '<small class="text-muted d-block">Rémunération</small>' +
                                '<strong>' + parseFloat(offre.remuneration).toFixed(2) + ' €</strong>' +
                            '</div>' +
                        '</div>' +
                    '</div>' : '') +
                    
                    (offre.secteur ? 
                    '<div class="mb-3">' +
                        '<small class="text-muted">Secteur:</small><br>' +
                        '<strong>' + offre.secteur + '</strong>' +
                    '</div>' : '') +
                    
                    (offre.lieu ? 
                    '<div class="mb-3">' +
                        '<small class="text-muted">Lieu:</small><br>' +
                        '<strong>' + offre.lieu + '</strong>' +
                    '</div>' : '') +
                '</div>' +
                
                                
                                
                                            
                            (offre.type_stage ? 
                            '<div class="mb-2">' +
                                '<small class="text-muted">Type:</small><br>' +
                                '<strong>' + offre.type_stage + '</strong>' +
                            '</div>' : '') +
                            
                            '<div class="mt-3">' +
                                '<small class="badge bg-' + (offre.statut === 'publié' ? 'success' : 'warning') + '">' +
                                    offre.statut.charAt(0).toUpperCase() + offre.statut.slice(1) +
                                '</small>' +
                            '</div>' +
                            
                            '<div class="mt-4">' +
                                '<div class="card border-0 shadow-sm">' +
                                    '<div class="card-body">' +
                                        '<h6 class="text-primary mb-3"><i class="fas fa-graduation-cap me-2 text-primary"></i>Informations du stagiaire</h6>' +
                                        
                                        '<div class="row">' +
                                            '<div class="col-md-6">' +
                                                '<div class="mb-2">' +
                                                    '<small class="text-muted">Nom:</small><br>' +
                                                    '<strong>' + (stagiaireNom || 'Non renseigné') + '</strong>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div class="col-md-6">' +
                                                '<div class="mb-2">' +
                                                    '<small class="text-muted">Prénom:</small><br>' +
                                                    '<strong>' + (stagiairePrenom || 'Non renseigné') + '</strong>' +
                                                '</div>' +
                                            '</div>' +
                                        '</div>' +
                                        
                                        '<div class="row">' +
                                            '<div class="col-md-6">' +
                                                '<div class="mb-2">' +
                                                    '<small class="text-muted">Email:</small><br>' +
                                                    '<strong>' + (stagiaireEmail || 'Non renseigné') + '</strong>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div class="col-md-6">' +
                                                '<div class="mb-2">' +
                                                    '<small class="text-muted">Téléphone:</small><br>' +
                                                    '<strong>' + (stagiaireTelephone || 'Non renseigné') + '</strong>' +
                                                '</div>' +
                                            '</div>' +
                                        '</div>' +
                                        
                                                                                
                                        '<div class="row mt-3">' +
                                            '<div class="col-12">' +
                                                '<a href="/activities/create?stagiaire_id=' + (stagiaireId || '') + '" class="btn btn-success w-100">' +
                                                    '<i class="fas fa-plus me-2"></i>Ajouter une activité' +
                                                '</a>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            
            // Informations du stagiaire sur toute la largeur
            (offre.stagiaire ? 
            '<div class="row mt-4">' +
                '<div class="col-12">' +
                    '<div class="card border-primary">' +
                        '<div class="card-header bg-primary text-white">' +
                            '<h6 class="mb-0">' +
                                '<i class="fas fa-user-graduate text-white me-2"></i>' +
                                'Informations du Stagiaire' +
                            '</h6>' +
                        '</div>' +
                        '<div class="card-body">' +
                            '<div class="row">' +
                                '<div class="col-md-6">' +
                                    '<div class="d-flex align-items-center mb-3 bg-light rounded p-2">' +
                                        '<div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">' +
                                            offre.stagiaire.prenom.charAt(0).toUpperCase() + offre.stagiaire.nom.charAt(0).toUpperCase() +
                                        '</div>' +
                                        '<div class="flex-grow-1">' +
                                            '<h6 class="mb-1 text-primary">' + offre.stagiaire.prenom + ' ' + offre.stagiaire.nom + '</h6>' +
                                            '<small class="text-primary">' + offre.stagiaire.email + '</small>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="col-md-6">' +
                                    '<div class="text-end">' +
                                        '<small class="text-muted d-block">Activités en cours</small>' +
                                        '<span class="badge bg-primary">' + (offre.stagiaire.activities_count || 0) + '</span>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                            '<div class="mt-3">' +
                                '<button type="button" class="btn btn-success btn-sm" onclick="ajouterActiviteStagiaire(' + offre.stagiaire.id + ', \'' + offre.stagiaire.prenom + ' ' + offre.stagiaire.nom + '\')">' +
                                    '<i class="fas fa-plus me-2"></i>' +
                                    'Ajouter une activité' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' : '') +
            '</div>';
            console.log('DEBUG: Contenu de la modale mis à jour');
        } else {
            const modalBody = document.querySelector('#offreDetailsModal .modal-body');
            modalBody.innerHTML = '<div class="alert alert-danger">' +
                '<i class="fas fa-exclamation-triangle me-2"></i>' +
                'Erreur lors du chargement des détails de l\'offre: ' + (data.error || 'Erreur inconnue') +
            '</div>';
            console.error('DEBUG: Erreur retournée par l\'API:', data.error);
        }
    })
    .catch(error => {
        console.error('DEBUG: Erreur fetch:', error);
        const modalBody = document.querySelector('#offreDetailsModal .modal-body');
        modalBody.innerHTML = '<div class="alert alert-danger">' +
            '<i class="fas fa-exclamation-triangle me-2"></i>' +
            'Erreur lors du chargement des détails de l\'offre: ' + error.message +
        '</div>';
    });
    
    // Nettoyer la modale quand elle est fermée
    modalElement.addEventListener('hidden.bs.modal', function() {
        console.log('DEBUG: Modale fermée, nettoyage...');
        this.remove();
    });
}

// Fonction pour ajouter une activité à un stagiaire
function ajouterActiviteStagiaire(stagiaireId, stagiaireNom) {
    // Rediriger vers la page de création d'activité avec le stagiaire pré-sélectionné
    window.location.href = '/activities/create?stagiaire_id=' + stagiaireId + '&stagiaire_nom=' + encodeURIComponent(stagiaireNom);
}

// Fonction pour modifier une offre
function modifierOffre(button) {
    const offreId = button.getAttribute('data-offre-id');
    const offreTitre = button.getAttribute('data-offre-titre');
    
    // Rediriger vers la page de modification de l'offre
    window.location.href = '/offres/' + offreId + '/edit';
}

// Fonction pour archiver une offre
function archiverOffre(button) {
    const offreId = button.getAttribute('data-offre-id');
    const offreTitre = button.getAttribute('data-offre-titre');
    
    if (confirm('Êtes-vous sûr de vouloir archiver l\'offre "' + offreTitre + '" ?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('/offres/' + offreId + '/archiver', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher un message de succès
                const toast = '<div class="toast align-items-center text-white bg-success border-0" role="alert">' +
                    '<div class="d-flex">' +
                        '<div class="toast-body">' +
                            '<i class="fas fa-check-circle me-2"></i>' +
                            'Offre archivée avec succès' +
                        '</div>' +
                    '</div>' +
                '</div>';
                
                // Créer et afficher le toast
                const toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.innerHTML = toast;
                document.body.appendChild(toastContainer);
                
                const toastElement = toastContainer.querySelector('.toast');
                const bsToast = new bootstrap.Toast(toastElement);
                bsToast.show();
                
                // Recharger la page après 2 secondes
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert('Erreur lors de l\'archivage: ' + (data.error || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'archivage de l\'offre');
        });
    }
}

// Fonction pour ouvrir une discussion générale avec un stagiaire
function openGeneralDiscussion(stagiaireId, stagiaireNom) {
    // Créer une modale dynamique pour la discussion
    const modalHtml = '<div class="modal fade" id="discussionModal" tabindex="-1">' +
        '<div class="modal-dialog modal-lg">' +
            '<div class="modal-content">' +
                '<div class="modal-header">' +
                    '<h5 class="modal-title">' +
                        '<i class="fas fa-comments text-primary me-2"></i>' +
                        'Discussion avec ' + stagiaireNom +
                    '</h5>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>' +
                '</div>' +
                '<div class="modal-body">' +
                    '<div id="discussionMessages" style="max-height: 400px; overflow-y: auto;">' +
                        '<!-- Messages chargés dynamiquement -->' +
                    '</div>' +
                    '<div class="mt-3">' +
                        '<div class="input-group">' +
                            '<input type="text" class="form-control" id="messageInput" placeholder="Tapez votre message..." maxlength="500">' +
                            '<button class="btn btn-primary" onclick="sendMessage()" data-stagiaire-id="' + stagiaireId + '">' +
                                '<i class="fas fa-paper-plane"></i> Envoyer' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
    
    // Ajouter la modale au body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Afficher la modale
    const modal = new bootstrap.Modal(document.getElementById('discussionModal'));
    modal.show();
    
    // Charger les messages existants
    loadDiscussionMessages(stagiaireId);
    
    // Nettoyer la modale quand elle est fermée
    document.getElementById('discussionModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Fonction pour charger les messages de discussion
function loadDiscussionMessages(stagiaireId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/discussions/${stagiaireId}`, {
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
            messagesContainer.innerHTML = data.messages.map(msg => 
                '<div class="mb-2 ' + (msg.sender_id == {{ auth()->id() }} ? 'text-end' : 'text-start') + '">' +
                    '<div class="d-inline-block p-2 rounded ' + (msg.sender_id == {{ auth()->id() }} ? 'bg-primary text-white' : 'bg-light') + '">' +
                        '<small class="d-block">' + msg.content + '</small>' +
                        '<small class="' + (msg.sender_id == {{ auth()->id() }} ? 'text-white' : 'text-muted') + '">' + msg.created_at + '</small>' +
                    '</div>' +
                '</div>'
            ).join('');
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

// Fonction pour envoyer un message
function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    // Récupérer le stagiaireId depuis le bouton
    const sendButton = document.querySelector('[data-stagiaire-id]');
    const stagiaireId = sendButton.getAttribute('data-stagiaire-id');
    
    if (!message) return;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/discussions/' + stagiaireId, {
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
            loadDiscussionMessages(stagiaireId);
        } else {
            alert('Erreur lors de l\'envoi du message');
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'envoi du message:', error);
        alert('Erreur lors de l\'envoi du message');
    });
}

</script>
