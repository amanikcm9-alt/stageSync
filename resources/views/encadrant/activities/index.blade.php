@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 bg-light">
    <!-- 1. HEADER : Titre w bouton Nouvelle activité -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fs-4 fw-bold text-dark">
                <i class="fas fa-list-ul text-success me-2"></i>Mes Activités
            </h2>
            <p class="text-muted small mb-0">Gérez les activités de vos stagiaires</p>
        </div>
        <a href="{{ route('activities.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Nouvelle activité
        </a>
    </div>

    <!-- 2. FILTERS : El bar mte3 el recherche (M3ABIYA TAW) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('activities.index') }}" method="GET" class="row g-2">
                
                <!-- Filter: Statut -->
                <div class="col-md-3">
                    <select name="statut" class="form-select form-select-sm border-secondary-subtle">
                        <option value="">Tous les statuts</option>
                        @foreach(['En cours', 'Terminer', 'Refusée', 'Demander complement d\'information'] as $st)
                            <option value="{{ $st }}" {{ request('statut') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter: Priorité -->
                <div class="col-md-3">
                    <select name="priorite" class="form-select form-select-sm border-secondary-subtle">
                        <option value="">Toutes les priorités</option>
                        @foreach(['Basse', 'Moyenne', 'Haute'] as $pr)
                            <option value="{{ $pr }}" {{ request('priorite') == $pr ? 'selected' : '' }}>{{ $pr }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter: Stagiaire (M3abi mel base) -->
                <div class="col-md-3">
                    <select name="stagiaire_id" class="form-select form-select-sm border-secondary-subtle">
                        <option value="">Tous les stagiaires</option>
                        @isset($stagiaires)
                            @foreach($stagiaires as $s)
                                <option value="{{ $s->id }}" {{ request('stagiaire_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->prenom }} {{ $s->nom }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <!-- Boutonnet Filtrer / Reset -->
                <div class="col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-filter me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fas fa-undo me-1"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. LISTE DES CARDS -->
    <div class="row">
        @forelse($activities as $activity)
        <div class="col-xl-6 col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <!-- Header Card bleu -->
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
                    <h6 class="mb-0 fw-bold">{{ $activity->titre }}</h6>
                    <div class="d-flex gap-2">
                        <span class="badge bg-white text-dark small">{{ $activity->statut ?? 'Assignée' }}</span>
                        <span class="badge bg-info text-dark small">{{ $activity->priorite ?? 'Moyenne' }}</span>
                    </div>
                </div>

                <!-- Body Card -->
                <div class="card-body">
                    <p class="text-muted small mb-3">{{ Str::limit($activity->description, 50) }}</p>
                    <div class="mb-3">
                        <small class="text-secondary">
                            <i class="fas fa-user me-2"></i>Stagiaire: {{ $activity->stagiaire->prenom ?? '---' }}
                        </small>
                    </div>
                    <!-- Date limite box -->
                    <div class="alert alert-warning border-0 py-2 mb-3" style="background-color: #fff4d1;">
                        <small class="text-dark">
                            <i class="fas fa-clock me-2"></i>Date limite: {{ $activity->date_limite ? \Carbon\Carbon::parse($activity->date_limite)->format('d/07/2026') : 'Non définie' }}
                        </small>
                    </div>
                    <!-- Progress Bar -->
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1 small fw-bold">
                            <span>Progression:</span><span>{{ $activity->progression ?? 0 }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $activity->progression ?? 0 }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Affichage des commentaires pour les activités refusées -->
                    @if($activity->statut === 'refusee' && $activity->commentaires)
                        <div class="alert alert-danger border-0 py-2 mb-3" style="background-color: #f8d7da;">
                            <small class="text-dark">
                                <i class="fas fa-exclamation-triangle me-2"></i><strong>Raison du refus:</strong> {{ $activity->commentaires }}
                            </small>
                        </div>
                    @endif
                    
                    <small class="text-muted"><i class="fas fa-comments me-1"></i>2 discussion(s)</small>
                </div>

                <!-- Footer Card : El bouttonet mte3ek -->
                <div class="card-footer p-0 bg-transparent">
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-primary border-0 rounded-0 py-2 border-top border-end">
                            <i class="fas fa-eye me-1"></i> Détails
                        </a>
                        
                        @if($activity->statut === 'soumise')
                            <a href="{{ route('activities.edit', $activity) }}" class="btn btn-outline-warning border-0 rounded-0 py-2 border-top border-end">
                                <i class="fas fa-edit me-1"></i> Modifier
                            </a>
                            <button type="button" class="btn btn-outline-danger border-0 rounded-0 py-2 border-top" 
                                    onclick="ouvrirModalRefusActivite({{ $activity->id }}, '{{ addslashes($activity->titre) }}')">
                                <i class="fas fa-times me-1"></i> Refuser
                            </button>
                        @else
                            <a href="{{ route('activities.edit', $activity) }}" class="btn btn-outline-warning border-0 rounded-0 py-2 border-top border-end">
                                <i class="fas fa-edit me-1"></i> Modifier
                            </a>
                            <button type="button" class="btn btn-outline-danger border-0 rounded-0 py-2 border-top" 
                                    onclick="ouvrirModalArchivage({{ $activity->id }}, '{{ addslashes($activity->titre) }}')">
                                <i class="fas fa-archive me-1"></i> Archiver
                            </button>
                        @endif
                    </div>
                </div>
                
                <!-- Messages de discussion pour les activités avec demander_info -->
                @if($activity->statut === 'demander_info')
                    <div class="card-footer bg-light p-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted fw-bold">
                                <i class="fas fa-comments me-1"></i>Messages de discussion
                            </small>
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    onclick="loadEncadrantMessages({{ $activity->id }})">
                                <i class="fas fa-sync-alt me-1"></i>Rafraîchir
                            </button>
                        </div>
                        <div id="messages-{{ $activity->id }}" class="messages-container" style="max-height: 150px; overflow-y: auto; background-color: #f8f9fa; padding: 10px; border-radius: 5px;">
                            <div class="text-center text-muted">
                                <small>Cliquez sur "Rafraîchir" pour charger les messages</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted">Aucune activité trouvée pour ces filtres.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- MODAL UNIQUE ARCHIVAGE -->
<div class="modal fade" id="modalArchivageUnique" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fs-6 fw-bold">Confirmation d'archivage</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-archive fa-3x text-danger mb-3 opacity-25"></i>
                <p class="mb-0 text-muted">Voulez-vous archiver l'activité :</p>
                <h5 id="displayActivityTitle" class="fw-bold text-dark mt-2"></h5>
            </div>
            <div class="modal-footer bg-light justify-content-center border-0">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger btn-sm px-4" id="btnConfirmArchivage" onclick="executerArchivage()">Confirmer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    /* CSS FIX image_efa5e6.png (Anti-tebayadh) */
    .btn-group .btn:hover, 
    .btn-group .btn:active, 
    .btn-group .btn:focus {
        background-color: transparent !important;
        box-shadow: none !important;
    }

    /* Outlines dima dahrin */
    .btn-outline-primary { color: #0d6efd !important; border-color: #0d6efd; }
    .btn-outline-warning { color: #f59e0b !important; border-color: #f59e0b; }
    .btn-outline-danger  { color: #dc3545 !important; border-color: #dc3545; }

    /* Hover khfif */
    .btn-group .btn:hover { background-color: rgba(0, 0, 0, 0.03) !important; }

    /* Style général */
    .card-footer { border-top: 1px solid #dee2e6 !important; }
    .btn-group .btn { font-size: 0.85rem; font-weight: 500; }
</style>

<script>
    let activityIdGlobal = null;

    function ouvrirModalArchivage(id, titre) {
        activityIdGlobal = id;
        document.getElementById('displayActivityTitle').textContent = titre;
        new bootstrap.Modal(document.getElementById('modalArchivageUnique')).show();
    }

    function ouvrirModalRefusActivite(id, titre) {
        activityIdGlobal = id;
        document.getElementById('displayActivityTitleRefus').textContent = titre;
        new bootstrap.Modal(document.getElementById('modalRefusActivite')).show();
    }

    function executerRefusActivite() {
        if (!activityIdGlobal) return;
        const btn = document.getElementById('btnConfirmRefus');
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(`/activities/${activityIdGlobal}/refuser-encadrant`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + (data.error || 'Erreur lors du refus'));
                btn.disabled = false;
                btn.innerHTML = 'Confirmer';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la requête');
            btn.disabled = false;
            btn.innerHTML = 'Confirmer';
        });
    }

    function executerArchivage() {
        if (!activityIdGlobal) return;
        const btn = document.getElementById('btnConfirmArchivage');
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(`/activities/${activityIdGlobal}/archiver`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json' }
        }).then(() => window.location.reload());
    }

    // Fonction pour charger les messages de l'encadrant
    function loadEncadrantMessages(activityId) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const messagesContainer = document.getElementById('messages-' + activityId);
        
        if (!messagesContainer) return;
        
        // Afficher un indicateur de chargement
        messagesContainer.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div></div>';
        
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
                if (data.messages.length === 0) {
                    messagesContainer.innerHTML = '<div class="text-center text-muted"><small>Aucun message</small></div>';
                } else {
                    let messagesHtml = '';
                    data.messages.forEach(msg => {
                        const senderName = msg.sender ? msg.sender.prenom + ' ' + msg.sender.nom : 'Inconnu';
                        const isFromMe = msg.sender_id === {{ Auth::user()->id }};
                        const messageClass = isFromMe ? 'bg-primary text-white ms-auto' : 'bg-light';
                        const textAlign = isFromMe ? 'text-end' : 'text-start';
                        
                        messagesHtml += `
                            <div class="mb-2 ${textAlign}">
                                <div class="d-inline-block">
                                    <small class="text-muted d-block">${senderName}</small>
                                    <div class="p-2 rounded ${messageClass}" style="max-width: 200px;">
                                        <small>${msg.message}</small>
                                    </div>
                                    <small class="text-muted">${new Date(msg.created_at).toLocaleTimeString()}</small>
                                </div>
                            </div>
                        `;
                    });
                    messagesContainer.innerHTML = messagesHtml;
                }
            } else {
                messagesContainer.innerHTML = '<div class="text-center text-danger"><small>Erreur: ' + (data.error || 'Erreur inconnue') + '</small></div>';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            messagesContainer.innerHTML = '<div class="text-center text-danger"><small>Erreur de chargement</small></div>';
        });
    }
</script>
@endsection

<!-- Modal de refus d'activité -->
<div class="modal fade" id="modalRefusActivite" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Refuser l'activité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir refuser cette activité ?</p>
                <p class="text-muted"><strong id="displayActivityTitleRefus"></strong></p>
                <p class="text-warning small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Cette action va refuser l'activité et la marquer comme non valide.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="btnConfirmRefus" onclick="executerRefusActivite()">
                    <i class="fas fa-times me-1"></i> Refuser
                </button>
            </div>
        </div>
    </div>
</div>