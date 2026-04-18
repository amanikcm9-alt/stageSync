@extends('layouts.app')

@section('title', 'Détails Candidature')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Détails Candidature</h1>
                <p class="page-subtitle">Candidature de {{ $candidature->nom }} {{ $candidature->prenom }}</p>
            </div>
            <div>
                <a href="{{ route('admin.candidatures.index') }}" class="btn btn-modern btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
                @if($candidature->statut === 'recue' || $candidature->statut === 'en_cours')
                    <button type="button" class="btn btn-modern btn-success me-2" data-bs-toggle="modal" data-bs-target="#acceptModal">
                        <i class="fas fa-check me-2"></i>Accepter
                    </button>
                    <button type="button" class="btn btn-modern btn-warning me-2" data-bs-toggle="modal" data-bs-target="#entretienModal">
                        <i class="fas fa-calendar me-2"></i>Planifier Entretien
                    </button>
                    <button type="button" class="btn btn-modern btn-danger" data-bs-toggle="modal" data-bs-target="#refuseModal">
                        <i class="fas fa-times me-2"></i>Refuser
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Candidature Details -->
<div class="container">
    <div class="row">
        <!-- Main Info -->
        <div class="col-lg-8">
            <!-- Candidate Info -->
            <div class="content-card mb-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="candidat-avatar-large me-4">
                        {{ strtoupper(substr($candidature->prenom, 0, 1)) }}{{ strtoupper(substr($candidature->nom, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="mb-1">{{ $candidature->nom }} {{ $candidature->prenom }}</h3>
                        <p class="text-muted mb-0">{{ $candidature->email }}</p>
                        @if($candidature->telephone)
                            <p class="text-muted mb-0">{{ $candidature->telephone }}</p>
                        @endif
                        <span class="badge bg-{{ 
                            $candidature->statut === 'recue' ? 'warning' : 
                            ($candidature->statut === 'en_cours' ? 'info' : 
                            ($candidature->statut === 'accepte' ? 'success' : 'danger')) }}">
                            <i class="fas fa-{{ 
                                $candidature->statut === 'recue' ? 'inbox' : 
                                ($candidature->statut === 'en_cours' ? 'clock' : 
                                ($candidature->statut === 'accepte' ? 'check-circle' : 'times-circle')) }} me-1"></i>
                            {{ ucfirst($candidature->statut) }}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-muted mb-3">Informations Personnelles</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Nom:</strong></td>
                                <td>{{ $candidature->nom }}</td>
                            </tr>
                            <tr>
                                <td><strong>Prénom:</strong></td>
                                <td>{{ $candidature->prenom }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $candidature->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Téléphone:</strong></td>
                                <td>{{ $candidature->telephone ?: 'Non renseigné' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Adresse:</strong></td>
                                <td>{{ $candidature->adresse ?: 'Non renseignée' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date de naissance:</strong></td>
                                <td>{{ $candidature->date_naissance ? $candidature->date_naissance->format('d/m/Y') : 'Non renseignée' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-muted mb-3">Informations Candidature</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Date de candidature:</strong></td>
                                <td>{{ $candidature->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Statut:</strong></td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $candidature->statut === 'recue' ? 'warning' : 
                                        ($candidature->statut === 'en_cours' ? 'info' : 
                                        ($candidature->statut === 'accepte' ? 'success' : 'danger')) }}">
                                        {{ ucfirst($candidature->statut) }}
                                    </span>
                                </td>
                            </tr>
                            @if($candidature->date_decision)
                                <tr>
                                    <td><strong>Date de décision:</strong></td>
                                    <td>{{ $candidature->date_decision->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endif
                            @if($candidature->date_entretien)
                                <tr>
                                    <td><strong>Date entretien:</strong></td>
                                    <td>{{ $candidature->date_entretien->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endif
                            @if($candidature->lieu_entretien)
                                <tr>
                                    <td><strong>Lieu entretien:</strong></td>
                                    <td>{{ $candidature->lieu_entretien }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Offer Info -->
            <div class="content-card mb-4">
                <h5 class="mb-3">Offre de Stage</h5>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Titre:</strong></td>
                                <td>{{ $candidature->offreStage->titre }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td>{{ $candidature->offreStage->type }}</td>
                            </tr>
                            <tr>
                                <td><strong>Durée:</strong></td>
                                <td>{{ $candidature->offreStage->duree }} mois</td>
                            </tr>
                            <tr>
                                <td><strong>Date début:</strong></td>
                                <td>{{ $candidature->offreStage->date_debut->format('d/m/Y') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Entreprise:</strong></td>
                                <td>{{ $candidature->offreStage->entreprise->nom }}</td>
                            </tr>
                            <tr>
                                <td><strong>Secteur:</strong></td>
                                <td>{{ $candidature->offreStage->entreprise->secteur }}</td>
                            </tr>
                            <tr>
                                <td><strong>Lieu:</strong></td>
                                <td>{{ $candidature->offreStage->lieu }}</td>
                            </tr>
                            <tr>
                                <td><strong>Rémunération:</strong></td>
                                <td>{{ $candidature->offreStage->remuneration ?: 'Non spécifiée' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Documents -->
            <div class="content-card mb-4">
                <h5 class="mb-3">Documents</h5>
                <div class="list-group">
                    @if($candidature->cv)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                <span>CV</span>
                            </div>
                            <a href="{{ asset('storage/' . $candidature->cv) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </div>
                    @endif
                    @if($candidature->lettre_motivation)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-alt text-success me-2"></i>
                                <span>Lettre de motivation</span>
                            </div>
                            <a href="{{ asset('storage/' . $candidature->lettre_motivation) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </div>
                    @endif
                    @if($candidature->portfolio)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-briefcase text-info me-2"></i>
                                <span>Portfolio</span>
                            </div>
                            <a href="{{ asset('storage/' . $candidature->portfolio) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="content-card mb-4">
                <h5 class="mb-3">Actions</h5>
                <div class="d-grid gap-2">
                    @if($candidature->statut === 'recue' || $candidature->statut === 'en_cours')
                        <button type="button" class="btn btn-modern btn-success" data-bs-toggle="modal" data-bs-target="#acceptModal">
                            <i class="fas fa-check me-2"></i>Accepter
                        </button>
                        <button type="button" class="btn btn-modern btn-warning" data-bs-toggle="modal" data-bs-target="#entretienModal">
                            <i class="fas fa-calendar me-2"></i>Planifier Entretien
                        </button>
                        <button type="button" class="btn btn-modern btn-danger" data-bs-toggle="modal" data-bs-target="#refuseModal">
                            <i class="fas fa-times me-2"></i>Refuser
                        </button>
                    @endif
                    <form method="POST" action="{{ route('admin.candidatures.destroy', $candidature) }}" onsubmit="return confirm('Supprimer cette candidature ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-modern btn-outline-danger w-100">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Accept Modal -->
<div class="modal fade" id="acceptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Accepter la Candidature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.candidatures.accepter', $candidature) }}">
                @csrf
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir accepter la candidature de <strong>{{ $candidature->nom }} {{ $candidature->prenom }}</strong> ?</p>
                    <div class="mb-3">
                        <label class="form-label">Notes (optionnel)</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
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

<!-- Refuse Modal -->
<div class="modal fade" id="refuseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Refuser la Candidature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.candidatures.refuser', $candidature) }}">
                @csrf
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir refuser la candidature de <strong>{{ $candidature->nom }} {{ $candidature->prenom }}</strong> ?</p>
                    <div class="mb-3">
                        <label class="form-label">Motif du refus *</label>
                        <textarea name="motif_refus" class="form-control" rows="3" required></textarea>
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

<!-- Entretien Modal -->
<div class="modal fade" id="entretienModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Planifier un Entretien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.candidatures.entretien', $candidature) }}">
                @csrf
                <div class="modal-body">
                    <p>Planifier un entretien pour <strong>{{ $candidature->nom }} {{ $candidature->prenom }}</strong></p>
                    <div class="mb-3">
                        <label class="form-label">Date de l'entretien *</label>
                        <input type="datetime-local" name="date_entretien" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lieu</label>
                        <input type="text" name="lieu_entretien" class="form-control" placeholder="Ex: Salle de réunion A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes_entretien" class="form-control" rows="2"></textarea>
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

<!-- Styles -->
<style>
.candidat-avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content-center;
    font-weight: bold;
    font-size: 2rem;
}

.table-borderless td {
    padding: 0.5rem 0;
    border: none;
}

.table-borderless td:first-child {
    font-weight: 600;
    color: #495057;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #e9ecef;
    padding: 0.75rem 1rem;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>
@endsection
