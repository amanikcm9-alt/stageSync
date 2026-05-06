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

    <!-- Notifications -->
    @if($notifications->count() > 0)
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-bell text-warning"></i> 
                            Notifications ({{ $notifications->count() }})
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="marquerNotificationsLues()">
                            <i class="fas fa-check"></i> Marquer comme lues
                        </button>
                    </div>
                </div>
                <div class="card-body p-2">
                    @foreach($notifications as $notification)
                    <div class="notification-item d-flex align-items-start p-2 border-bottom">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <small class="text-muted d-block">
                                        @if($notification->sender)
                                            <i class="fas fa-user"></i> {{ $notification->sender->prenom }} {{ $notification->sender->nom }}
                                        @else
                                            <i class="fas fa-robot"></i> Système
                                        @endif
                                    </small>
                                    <p class="mb-1 small">{{ $notification->message }}</p>
                                    @if($notification->activity)
                                        <small class="text-muted">
                                            <i class="fas fa-tasks"></i> {{ $notification->activity->titre }}
                                        </small>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

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
                                    <button type="button" class="btn btn-xs btn-outline-info" data-offre-id="{{ $stagiaire->offre_stage->id }}" data-offre-titre="{{ $stagiaire->offre_stage->titre }}" onclick="voirDetailsOffre(this)" style="font-size: 0.7rem; padding: 2px 6px;">
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

    <!-- Section détaillée des stagiaires (cachée par défaut) -->
    <div id="stagiaires-details" style="display: none;">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-users"></i> Détails des stagiaires
                    <button type="button" class="btn btn-sm btn-outline-light float-end" onclick="toggleStagiairesSection()">
                        <i class="fas fa-times"></i>
                    </button>
                </h6>
            </div>
            <div class="card-body">
                @if($stagiaires->count() > 0)
                <div class="row">
                    @foreach($stagiaires as $stagiaire)
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        {{ strtoupper(substr($stagiaire->prenom, 0, 1)) }}{{ strtoupper(substr($stagiaire->nom, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">{{ $stagiaire->prenom }} {{ $stagiaire->nom }}</h6>
                                        <small class="text-muted">{{ $stagiaire->email }}</small>
                                    </div>
                                </div>
                                
                                <!-- Informations sur l'offre de stage -->
                            @if($stagiaire->offre_stage)
                            <div class="mb-3 p-2 bg-light rounded border">
                                    <div class="d-flex align-items-start mb-2">
                                        <i class="fas fa-briefcase text-primary me-2 mt-1"></i>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <small class="fw-bold text-primary">Offre de stage</small>
                                                    <div class="mt-1">
                                                        <strong class="d-block">{{ $stagiaire->offre_stage->titre }}</strong>
                                                        <small class="text-muted d-block">
                                                            @if($stagiaire->offre_stage->entreprise)
                                                            {{ $stagiaire->offre_stage->entreprise->nom }} - 
                                                            @endif
                                                            {{ $stagiaire->offre_stage->duree_semaines }} semaines
                                                        </small>
                                                    </div>
                                                    @if($stagiaire->offre_stage->description)
                                                    <div class="mt-2">
                                                        <small class="text-muted">{{ Str::limit($stagiaire->offre_stage->description, 100) }}</small>
                                                    </div>
                                                    @endif
                                                    <div class="mt-2">
                                                        <small class="badge bg-info text-white">
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            {{ $stagiaire->offre_stage->date_debut ? $stagiaire->offre_stage->date_debut->format('d/m/Y') : 'Date non définie' }} - 
                                                            {{ $stagiaire->offre_stage->date_fin ? $stagiaire->offre_stage->date_fin->format('d/m/Y') : 'Date non définie' }}
                                                        </small>
                                                    </div>
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-outline-info" data-offre-id="{{ $stagiaire->offre_stage->id }}" data-offre-titre="{{ $stagiaire->offre_stage->titre }}" onclick="voirDetailsOffre(this)">
                                                        <i class="fas fa-eye"></i> Détails
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="mb-3 p-2 bg-warning bg-opacity-10 rounded border border-warning">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                            <small class="text-warning">Aucune offre de stage assignée</small>
                                        </div>
                                                                            </div>
                                </div>
                                @endif
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted fw-bold">Activités assignées</small>
                                        <span class="badge bg-info">{{ $stagiaire->activities->count() }}</span>
                                    </div>
                                            
                                    <!-- Statistiques des activités du stagiaire -->
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <small class="text-muted d-block">En cours</small>
                                                <strong class="text-info">{{ $stagiaire->activities->where('statut', 'en_cours')->count() }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <small class="text-muted d-block">Soumises</small>
                                                <strong class="text-warning">{{ $stagiaire->activities->where('statut', 'soumise')->count() }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <small class="text-muted d-block">Validées</small>
                                                <strong class="text-success">{{ $stagiaire->activities->where('statut', 'validee')->count() }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <small class="text-muted d-block">En retard</small>
                                                <strong class="text-danger">{{ $stagiaire->activities->filter(fn($a) => $a->estEnRetard())->count() }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions rapides -->
                                @if($stagiaire->offre_stage)
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-info" data-offre-id="{{ $stagiaire->offre_stage->id }}" data-offre-titre="{{ $stagiaire->offre_stage->titre }}" onclick="voirDetailsOffre(this)">
                                        <i class="fas fa-eye"></i> Voir détails
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Aucun stagiaire affecté</h6>
                    <p class="text-muted small">Vous n'avez pas encore de stagiaires assignés.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Section Notifications -->
    <div class="card border-0 shadow-sm mb-3 notifications-section">
        <div class="card-header bg-warning text-dark">
            <h6 class="mb-0">
                <i class="fas fa-bell"></i> Notifications Récentes
                @if($notifications->count() > 0)
                <span class="badge bg-danger ms-2">{{ $notifications->count() }}</span>
                @endif
            </h6>
        </div>
        <div class="card-body">
            @if($notifications->count() > 0)
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
                    <a href="#" class="btn btn-outline-warning btn-sm">
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

    <!-- Section Documents -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-warning text-white">
            <h6 class="mb-0">
                <i class="fas fa-book"></i> Mes Documents
            </h6>
        </div>
        <div class="card-body">
            @if($documents->count() > 0)
            <div class="row">
                @foreach($documents->take(6) as $document)
                <div class="col-md-4 mb-2">
                    <div class="card border-0 shadow-sm h-100 small">
                        <div class="card-body p-2">
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
            <div class="text-center mt-2">
                <a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-primary btn-sm small">
                    Voir tous les documents
                </a>
            </div>
            @endif
            @else
            <div class="text-center py-3">
                <i class="fas fa-book fa-2x text-muted mb-2"></i>
                <h6 class="text-muted small">Aucun document</h6>
                <p class="text-muted small">Vous n'avez pas encore publié de documents.</p>
            </div>
            @endif
        </div>
    </div>
</div>

    <!-- Section Évaluations -->
    <a href="{{ route('encadrant.evaluations.index') }}" class="card border-0 shadow-sm mb-3 text-decoration-none">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0">
                <i class="fas fa-clipboard-check"></i> Évaluations
                <small class="float-end">
                    <i class="fas fa-arrow-right"></i>
                </small>
            </h6>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-white text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-white">Gérer les évaluations</h6>
                            <small class="text-white-50">Évaluer les stagiaires et consulter les auto-évaluations</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="text-white">
                        <div class="h4 mb-0">{{ $evaluations->count() }}</div>
                        <small>Évaluations</small>
                    </div>
                </div>
            </div>
        </div>
    </a>

@endsection

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
    console.log('DEBUG: voirDetailsOffre appelé avec offreId:', offreId, 'offreTitre:', offreTitre);
    
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
                '<div class="col-md-8">' +
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
                '</div>' +
                
                '<div class="col-md-4">' +
                    '<div class="card border-0 shadow-sm h-100">' +
                        '<div class="card-body">' +
                            '<h6 class="text-muted mb-3">Informations complémentaires</h6>' +
                            
                            (offre.secteur ? 
                            '<div class="mb-2">' +
                                '<small class="text-muted">Secteur:</small><br>' +
                                '<strong>' + offre.secteur + '</strong>' +
                            '</div>' : '') +
                            
                            (offre.lieu ? 
                            '<div class="mb-2">' +
                                '<small class="text-muted">Lieu:</small><br>' +
                                '<strong>' + offre.lieu + '</strong>' +
                            '</div>' : '') +
                            
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
                        '</div>' +
                    '</div>' +
                '</div>' +
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

// Fonction pour marquer les notifications comme lues
function marquerNotificationsLues() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/notifications/marquer-lues', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Masquer la section des notifications
            const notificationsSection = document.querySelector('.card:has(.fa-bell)');
            if (notificationsSection) {
                notificationsSection.style.display = 'none';
            }
            
            // Recharger la page pour mettre à jour le compteur
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            alert('Erreur lors du marquage des notifications comme lues');
        }
    })
    .catch(error => {
        console.error('Erreur lors du marquage des notifications:', error);
        alert('Erreur lors du marquage des notifications comme lues');
    });
}
</script>
