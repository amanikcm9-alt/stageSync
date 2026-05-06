@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header avec informations clés -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-3">
                <div class="candidat-avatar-large rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                     style="width: 80px; height: 80px; font-size: 28px; font-weight: bold; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);">
                    {{ strtoupper(substr($candidature->prenom, 0, 1)) }}{{ strtoupper(substr($candidature->nom, 0, 1)) }}
                </div>
                <div class="flex-grow-1">
                    <h1 class="mb-1">{{ $candidature->nom }} {{ $candidature->prenom }}</h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-clock"></i> 
                        Candidature du {{ $candidature->created_at->format('d/m/Y H:i') }} | 
                        Offre : {{ $candidature->offreStage?->titre ?? 'Offre non spécifiée' }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('rh.candidatures.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="status-card text-center p-3 rounded" style="background: {{ $candidature->statut === 'recue' ? 'linear-gradient(135deg, #fff3cd 0%, #ffc107 100%)' : ($candidature->statut === 'en_cours' ? 'linear-gradient(135deg, #d1ecf1 0%, #17a2b8 100%)' : ($candidature->statut === 'accepte' ? 'linear-gradient(135deg, #d4edda 0%, #28a745 100%)' : 'linear-gradient(135deg, #f8d7da 0%, #dc3545 100%)')) }};">
                <div class="status-icon mb-2">
                    <i class="fas fa-{{ $candidature->statut === 'recue' ? 'inbox' : ($candidature->statut === 'en_cours' ? 'clock' : ($candidature->statut === 'accepte' ? 'check-circle' : 'times-circle')) }} fa-2x"></i>
                </div>
                <div class="status-title fw-bold">{{ $candidature->statut === 'recue' ? 'Reçue' : ($candidature->statut === 'en_cours' ? 'En cours' : ($candidature->statut === 'accepte' ? 'Acceptée' : 'Refusée')) }}</div>
                @if($candidature->date_decision)
                    <div class="status-date small">Décision le {{ $candidature->date_decision->format('d/m/Y') }}</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Informations du candidat -->
    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-user"></i> Informations du Candidat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="fw-bold mb-3">{{ $candidature->nom }} {{ $candidature->prenom }}</h4>
                            
                            <div class="info-item">
                                <strong>Email :</strong> 
                                <a href="mailto:{{ $candidature->email }}">{{ $candidature->email }}</a>
                            </div>
                            
                            <div class="info-item">
                                <strong>Téléphone :</strong> 
                                <a href="tel:{{ $candidature->telephone }}">{{ $candidature->telephone }}</a>
                            </div>
                            
                            <div class="info-item">
                                <strong>Adresse :</strong> 
                                {{ $candidature->adresse }}
                            </div>
                            
                            <div class="info-item">
                                <strong>Code postal :</strong> 
                                {{ $candidature->code_postal }}
                            </div>
                            
                            <div class="info-item">
                                <strong>Ville :</strong> 
                                {{ $candidature->ville }}
                            </div>
                            
                            <div class="info-item">
                                <strong>Date de naissance :</strong> 
                                {{ $candidature->date_naissance ? $candidature->date_naissance->format('d/m/Y') : 'Non renseignée' }}
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width: 80px; height: 80px; font-size: 28px;">
                                    {{ strtoupper(substr($candidature->prenom, 0, 1)) }}{{ strtoupper(substr($candidature->nom, 0, 1)) }}
                                </div>
                                <span class="badge bg-primary fs-6">{{ $candidature->statut === 'recue' ? 'Reçue' : ($candidature->statut === 'en_cours' ? 'En cours' : ($candidature->statut === 'accepte' ? 'Acceptée' : 'Refusée')) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase"></i> Détails de l'Offre
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <strong>Titre de l'offre :</strong> {{ $candidature->offreStage?->titre ?? 'Offre non spécifiée' }}
                    </div>
                    
                    <div class="info-item">
                        <strong>Entreprise :</strong> {{ $candidature->offreStage?->entreprise?->nom ?? 'Entreprise non spécifiée' }}
                    </div>
                    
                    <div class="info-item">
                        <strong>Lieu :</strong> {{ $candidature->offreStage?->lieu ?? 'Non spécifié' }}
                    </div>
                    
                    <div class="info-item">
                        <strong>Durée :</strong> {{ $candidature->offreStage?->duree_semaines ?? 'N/A' }} semaines
                    </div>
                    
                    <div class="info-item">
                        <strong>Rémunération :</strong> {{ $candidature->offreStage?->remuneration ? number_format($candidature->offreStage->remuneration, 2, ',', ' ') . ' €/mois' : 'Non renseignée' }}
                    </div>
                    
                    <div class="info-item">
                        <strong>Publié par :</strong> {{ $candidature->offreStage?->rh?->nom ?? '' }} {{ $candidature->offreStage?->rh?->prenom ?? 'Non spécifié' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-file-alt"></i> Documents Fournis
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="document-card">
                        <div class="text-center p-3">
                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                            <h6>CV</h6>
                            @if($candidature->cv_path)
                                <a href="{{ asset('storage/' . $candidature->cv_path) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            @else
                                <span class="text-muted">Non fourni</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="document-card">
                        <div class="text-center p-3">
                            <i class="fas fa-envelope fa-3x text-info mb-2"></i>
                            <h6>Lettre de motivation</h6>
                            
                            {{-- Affichage direct de la lettre de motivation pour débogage --}}
                            @if($candidature->lettre_motivation)
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="toggleLettreMotivation()">
                                        <i class="fas fa-eye"></i> Voir
                                    </button>
                                </div>
                                <div id="lettreMotivationDirect" class="d-none">
                                    <div class="alert alert-info mt-2" style="font-size: 0.8rem; max-height: 200px; overflow-y: auto;">
                                        <strong>Lettre de motivation :</strong><br>
                                        {!! nl2br(e($candidature->lettre_motivation)) !!}
                                    </div>
                                </div>
                                <small class="d-block text-muted mt-1">Texte ({{ strlen($candidature->lettre_motivation) }} caractères)</small>
                            @elseif($candidature->lettre_motivation_path)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $candidature->lettre_motivation_path) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                </div>
                                <small class="d-block text-muted mt-1">Fichier: {{ basename($candidature->lettre_motivation_path) }}</small>
                            @else
                                <span class="text-muted">Non fournie</span>
                                <small class="d-block text-muted mt-1">
                                    Aucune lettre de motivation trouvée
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
                
                            </div>
        </div>
    </div>

    <!-- Informations supplémentaires -->
    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap"></i> Formation
                    </h5>
                </div>
                <div class="card-body">
                    <p>{{ $candidature->formation ?: 'Non renseignée' }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase"></i> Expérience
                    </h5>
                </div>
                <div class="card-body">
                    <p>{{ $candidature->experience ?: 'Non renseignée' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-cog"></i> Actions
            </h5>
        </div>
        <div class="card-body">
            @if($candidature->statut == 'recue' || $candidature->statut == 'en_cours')
                <div class="row">
                    <div class="col-md-4">
                        <button type="button" 
                                class="btn btn-success w-100" 
                                data-bs-toggle="modal" 
                                data-bs-target="#accepterModal">
                            <i class="fas fa-check"></i> Accepter la candidature
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" 
                                class="btn btn-warning w-100" 
                                data-bs-toggle="modal" 
                                data-bs-target="#entretienModal">
                            <i class="fas fa-calendar"></i> Planifier un entretien
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" 
                                class="btn btn-danger w-100" 
                                data-bs-toggle="modal" 
                                data-bs-target="#refuserModal">
                            <i class="fas fa-times"></i> Refuser la candidature
                        </button>
                    </div>
                </div>
            @elseif($candidature->statut === 'en_cours')
                <div class="alert alert-info">
                    <i class="fas fa-calendar"></i> 
                    Entretien planifié le {{ $candidature->date_entretien ? $candidature->date_entretien->format('d/m/Y') : 'N/A' }} 
                    à {{ $candidature->heure_entretien ?: 'N/A' }}
                    @if($candidature->lieu_entretien)
                        - {{ $candidature->lieu_entretien }}
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <button type="button" 
                            class="btn btn-success" 
                            data-bs-toggle="modal" 
                            data-bs-target="#accepterModal">
                        <i class="fas fa-check"></i> Accepter
                    </button>
                    <button type="button" 
                            class="btn btn-danger" 
                            data-bs-toggle="modal" 
                            data-bs-target="#refuserModal">
                        <i class="fas fa-times"></i> Refuser
                    </button>
                </div>
            @else
                <div class="alert alert-{{ $candidature->statut === 'accepte' ? 'success' : 'danger' }}">
                    <i class="fas fa-{{ $candidature->statut === 'accepte' ? 'check' : 'times' }}"></i> 
                    Candidature {{ $candidature->statut === 'accepte' ? 'acceptée' : 'refusée' }}
                    @if($candidature->date_decision)
                        le {{ $candidature->date_decision->format('d/m/Y') }}
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Modal Accepter -->
<div class="modal fade" id="accepterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Accepter la candidature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('rh.candidatures.accepter', $candidature) }}">
                @csrf
                <div class="modal-body">
                    <p>Voulez-vous vraiment accepter la candidature de <strong>{{ $candidature->nom }} {{ $candidature->prenom }}</strong> ?</p>
                    <div class="mb-3">
                        <label class="form-label">Commentaire (optionnel)</label>
                        <textarea class="form-control" name="commentaire" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Accepter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Refuser -->
<div class="modal fade" id="refuserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Refuser la candidature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('rh.candidatures.refuser', $candidature) }}">
                @csrf
                <div class="modal-body">
                    <p>Voulez-vous vraiment refuser la candidature de <strong>{{ $candidature->nom }} {{ $candidature->prenom }}</strong> ?</p>
                    <div class="mb-3">
                        <label class="form-label">Motif du refus *</label>
                        <textarea class="form-control" name="motif_refus" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Refuser</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Entretien -->
<div class="modal fade" id="entretienModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Planifier un entretien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('rh.candidatures.entretien', $candidature) }}">
                @csrf
                <div class="modal-body">
                    <p>Planifier un entretien pour <strong>{{ $candidature->nom }} {{ $candidature->prenom }}</strong></p>
                    <div class="mb-3">
                        <label class="form-label">Date *</label>
                        <input type="date" class="form-control" name="date_entretien" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Heure *</label>
                        <input type="time" class="form-control" name="heure_entretien" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lieu *</label>
                        <input type="text" class="form-control" name="lieu_entretien" placeholder="Ex: Salle de réunion A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control" name="notes_entretien" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Planifier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Styles professionnels améliorés -->
<style>
/* Header et avatar */
.candidat-avatar-large {
    box-shadow: 0 4px 15px rgba(0,123,255,0.3);
    transition: all 0.3s ease;
}

.candidat-avatar-large:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0,123,255,0.4);
}

.status-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    color: white;
}

.status-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.status-icon {
    opacity: 0.9;
}

.status-title {
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-date {
    opacity: 0.8;
}

/* Cartes */
.card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    border-radius: 12px 12px 0 0 !important;
    font-weight: 600;
}

.info-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s ease;
}

.info-item:hover {
    background-color: #f8f9fa;
    padding-left: 0.5rem;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item strong {
    color: #495057;
    font-weight: 600;
}

/* Documents */
.document-card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    transition: all 0.3s ease;
    height: 100%;
}

.document-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #007bff;
}

.document-card i {
    opacity: 0.8;
    transition: all 0.2s ease;
}

.document-card:hover i {
    opacity: 1;
    transform: scale(1.1);
}

.document-card h6 {
    font-weight: 600;
    color: #495057;
    margin-top: 0.5rem;
}

/* Boutons */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
    border: none;
    padding: 0.5rem 1.5rem;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
}

/* Alertes */
.alert {
    border-radius: 10px;
    border: none;
    font-weight: 500;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #17a2b8 100%);
    color: white;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #28a745 100%);
    color: white;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #dc3545 100%);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .candidat-avatar-large {
        width: 60px !important;
        height: 60px !important;
        font-size: 20px !important;
    }
    
    .status-card {
        margin-top: 1rem;
    }
    
    .btn {
        font-size: 0.875rem;
        padding: 0.4rem 1rem;
    }
    
    .document-card {
        margin-bottom: 1rem;
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.5s ease-out;
}

/* Liens */
a {
    color: #007bff;
    text-decoration: none;
    transition: color 0.2s ease;
}

a:hover {
    color: #0056b3;
    text-decoration: underline;
}

/* Form controls */
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #ced4da;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    transform: translateY(-1px);
}

/* Styles pour réduire encore plus la taille des polices */
.container-fluid {
    font-size: 0.85rem !important;
    padding: 1rem !important;
}

.card {
    margin-bottom: 0.5rem !important;
}

.card-header {
    padding: 0.5rem 0.8rem !important;
}

.card-header h5 {
    font-size: 0.9rem !important;
    font-weight: 600;
    margin-bottom: 0 !important;
}

.card-body {
    font-size: 0.8rem !important;
    padding: 0.6rem 0.8rem !important;
}

.row {
    margin-bottom: 0.5rem !important;
}

.col-md-6, .col-md-4, .col-md-8, .col-md-12 {
    padding-bottom: 0.3rem !important;
}

h1 {
    font-size: 1.4rem !important;
    font-weight: 600;
    margin-bottom: 0.5rem !important;
}

h2 {
    font-size: 1.2rem !important;
    font-weight: 600;
    margin-bottom: 0.4rem !important;
}

h3 {
    font-size: 1rem !important;
    font-weight: 600;
    margin-bottom: 0.3rem !important;
}

h5 {
    font-size: 0.9rem !important;
    font-weight: 600;
}

h6 {
    font-size: 0.8rem !important;
}

.text-muted {
    font-size: 0.75rem !important;
}

.info-item {
    font-size: 0.8rem !important;
    margin-bottom: 0.3rem !important;
    padding: 0.2rem 0 !important;
}

.info-item strong {
    font-size: 0.8rem !important;
    font-weight: 600;
}

.badge {
    font-size: 0.6rem !important;
    padding: 0.2em 0.4em;
}

.btn {
    font-size: 0.75rem !important;
    padding: 0.3rem 0.6rem !important;
    font-weight: 500;
}

.table {
    font-size: 0.8rem !important;
    margin-bottom: 0.5rem !important;
}

.table th {
    font-size: 0.75rem !important;
    font-weight: 600;
    padding: 0.4rem 0.6rem !important;
}

.table td {
    font-size: 0.75rem !important;
    padding: 0.3rem 0.6rem !important;
}

.list-group-item {
    font-size: 0.8rem !important;
    padding: 0.4rem 0.6rem !important;
}

.status-card {
    font-size: 0.8rem !important;
    padding: 0.8rem !important;
}

.status-title {
    font-size: 0.85rem !important;
}

.status-date {
    font-size: 0.7rem !important;
}

.candidat-avatar-large {
    font-size: 22px !important;
    width: 65px !important;
    height: 65px !important;
}

.modal-title {
    font-size: 1rem !important;
}

.modal-body {
    font-size: 0.8rem !important;
}

.lettre-motivation-content {
    font-size: 0.8rem !important;
    line-height: 1.4;
}

small {
    font-size: 0.7rem !important;
}

.alert {
    font-size: 0.8rem !important;
    padding: 0.6rem 0.8rem !important;
    margin-bottom: 0.5rem !important;
}

.document-card {
    font-size: 0.8rem !important;
    padding: 0.8rem !important;
    margin-bottom: 0.3rem !important;
}

.document-card i {
    font-size: 2.2rem !important;
    margin-bottom: 0.3rem !important;
}

.document-card h6 {
    font-size: 0.75rem !important;
    margin-bottom: 0.5rem !important;
}

.document-card .btn {
    margin-bottom: 0.3rem !important;
}

/* Réduction des espacements */
.mb-4 {
    margin-bottom: 1rem !important;
}

.mb-3 {
    margin-bottom: 0.8rem !important;
}

.mb-2 {
    margin-bottom: 0.5rem !important;
}

.mb-1 {
    margin-bottom: 0.3rem !important;
}

.py-4 {
    padding-top: 1rem !important;
    padding-bottom: 1rem !important;
}

.py-3 {
    padding-top: 0.8rem !important;
    padding-bottom: 0.8rem !important;
}

.p-3 {
    padding: 0.8rem !important;
}

@media (max-width: 768px) {
    .container-fluid {
        font-size: 0.8rem !important;
        padding: 0.8rem !important;
    }
    
    .btn {
        font-size: 0.7rem !important;
        padding: 0.25rem 0.5rem !important;
    }
    
    h1 {
        font-size: 1.3rem !important;
    }
    
    h2 {
        font-size: 1.1rem !important;
    }
    
    h3 {
        font-size: 0.95rem !important;
    }
    
    .candidat-avatar-large {
        font-size: 20px !important;
        width: 55px !important;
        height: 55px !important;
    }
    
    .card-body {
        font-size: 0.75rem !important;
        padding: 0.5rem 0.6rem !important;
    }
    
    .document-card {
        padding: 0.6rem !important;
    }
}

</style>

<script>
function toggleLettreMotivation() {
    const element = document.getElementById('lettreMotivationDirect');
    if (element) {
        element.classList.toggle('d-none');
    }
}
</script>

/* Modal Lettre de Motivation */
@if($candidature->lettre_motivation)
<div class="modal fade" id="lettreMotivationModal" tabindex="-1" aria-labelledby="lettreMotivationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lettreMotivationModalLabel">
                    <i class="fas fa-envelope me-2"></i>Lettre de motivation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Candidat :</strong> {{ $candidature->nom }} {{ $candidature->prenom }}
                </div>
                <div class="mb-3">
                    <strong>Offre :</strong> {{ $candidature->offreStage?->titre ?? 'Offre non spécifiée' }}
                </div>
                <hr>
                <div class="lettre-motivation-content">
                    {!! nl2br(e($candidature->lettre_motivation)) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
