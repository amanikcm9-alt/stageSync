@extends('layouts.app')

@section('title', 'Mes Notifications')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-sms"></i> Mes Notifications SMS
                        </h4>
                        <div>
                            <span class="badge bg-light text-dark">
                                {{ $email }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-sms fa-2x mb-2"></i>
                                    <h3>{{ $notifications->total() }}</h3>
                                    <p class="mb-0">Total</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <h3>{{ $notifications->where('statut', 'envoye')->count() }}</h3>
                                    <p class="mb-0">Envoyés</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h3>{{ $notifications->where('statut', 'en_attente')->count() }}</h3>
                                    <p class="mb-0">En attente</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-danger text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                    <h3>{{ $notifications->where('statut', 'echec')->count() }}</h3>
                                    <p class="mb-0">Échecs</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Statut</label>
                            <select class="form-select" onchange="window.location.href='?statut='+this.value">
                                <option value="">Tous les statuts</option>
                                <option value="envoye" {{ request('statut') === 'envoye' ? 'selected' : '' }}>Envoyés</option>
                                <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="echec" {{ request('statut') === 'echec' ? 'selected' : '' }}>Échecs</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type de notification</label>
                            <select class="form-select" onchange="window.location.href='?type='+this.value">
                                <option value="">Tous les types</option>
                                <option value="acceptation" {{ request('type') === 'acceptation' ? 'selected' : '' }}>Acceptation</option>
                                <option value="refus" {{ request('type') === 'refus' ? 'selected' : '' }}>Refus</option>
                                <option value="entretien" {{ request('type') === 'entretien' ? 'selected' : '' }}>Entretien</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Recherche</label>
                            <input type="text" class="form-control" placeholder="Rechercher dans les notifications..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Liste des notifications -->
                    @if($notifications->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date et Heure</th>
                                        <th>Type</th>
                                        <th>Sujet</th>
                                        <th>Message</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notifications as $notification)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">
                                                    {{ $notification->date_envoi ? $notification->date_envoi->format('d/m/Y H:i') : '-' }}
                                                </div>
                                                @if($notification->date_envoi)
                                                    <small class="text-muted">{{ $notification->date_envoi->diffForHumans() }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if(str_contains($notification->sujet, 'Acceptée'))
                                                    <span class="badge bg-success">Acceptation</span>
                                                @elseif(str_contains($notification->sujet, 'Refusée'))
                                                    <span class="badge bg-danger">Refus</span>
                                                @elseif(str_contains($notification->sujet, 'Entretien'))
                                                    <span class="badge bg-info">Entretien</span>
                                                @else
                                                    <span class="badge bg-secondary">Autre</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $notification->sujet }}</div>
                                            </td>
                                            <td>
                                                <div class="message-cell">
                                                    {{ $notification->contenu }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $notification->statut_couleur }}">
                                                    {{ $notification->statut_formate }}
                                                </span>
                                                @if($notification->erreur_message)
                                                    <i class="fas fa-exclamation-triangle text-danger ms-2" data-bs-toggle="tooltip" title="{{ $notification->erreur_message }}"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Affichage de {{ $notifications->firstItem() }} à {{ $notifications->lastItem() }} 
                                sur {{ $notifications->total() }} notifications
                            </div>
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-sms fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune notification trouvée</h5>
                            <p class="text-muted">
                                Vous n'avez pas encore reçu de notification SMS.<br>
                                Les notifications apparaîtront ici lorsqu'une décision sera prise sur vos candidatures.
                            </p>
                            <a href="{{ route('candidat.dashboard', ['email' => $email]) }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.message-cell {
    max-width: 300px;
    word-wrap: break-word;
    white-space: normal;
}
</style>
@endsection
