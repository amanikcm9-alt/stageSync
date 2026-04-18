@extends('layouts.rh')

@section('title', 'Notifications SMS')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-sms"></i> Notifications SMS
                        </h5>
                        <div>
                            <span class="badge bg-light text-dark">
                                {{ $notifications->total() }} total
                            </span>
                            <span class="badge bg-success">
                                {{ \App\Models\Notification::sms()->envoye()->count() }} envoyés
                            </span>
                            <span class="badge bg-warning">
                                {{ \App\Models\Notification::sms()->enAttente()->count() }} en attente
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Statut</label>
                            <select class="form-select" id="statutFilter">
                                <option value="">Tous les statuts</option>
                                <option value="envoye">Envoyés</option>
                                <option value="en_attente">En attente</option>
                                <option value="echec">Échec</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type</label>
                            <select class="form-select" id="typeFilter">
                                <option value="">Tous les types</option>
                                <option value="acceptation">Acceptation</option>
                                <option value="refus">Refus</option>
                                <option value="entretien">Entretien</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Recherche</label>
                            <input type="text" class="form-control" id="searchFilter" placeholder="Rechercher par destinataire ou sujet...">
                        </div>
                    </div>

                    <!-- Tableau des notifications -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Destinataire</th>
                                    <th>Sujet</th>
                                    <th>Contenu</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $notification)
                                    <tr>
                                        <td>
                                            {{ $notification->date_envoi ? $notification->date_envoi->format('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td>
                                            <code>{{ $notification->destinataire }}</code>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $notification->sujet }}</span>
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $notification->contenu }}">
                                                {{ Str::limit($notification->contenu, 50) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $notification->statut_couleur }}">
                                                {{ $notification->statut_formate }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if($notification->statut === 'echec')
                                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="{{ $notification->erreur_message }}">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="renvoyerSms({{ $notification->id }})" data-bs-toggle="tooltip" title="Renvoyer le SMS">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-sms fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">Aucune notification SMS trouvée</p>
                                        </td>
                                    </tr>
                                @endforelse
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript pour les filtres et actions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtres
    const statutFilter = document.getElementById('statutFilter');
    const typeFilter = document.getElementById('typeFilter');
    const searchFilter = document.getElementById('searchFilter');
    
    function applyFilters() {
        const url = new URL(window.location);
        if (statutFilter.value) url.searchParams.set('statut', statutFilter.value);
        if (typeFilter.value) url.searchParams.set('type', typeFilter.value);
        if (searchFilter.value) url.searchParams.set('search', searchFilter.value);
        window.location.href = url.toString();
    }
    
    statutFilter.addEventListener('change', applyFilters);
    typeFilter.addEventListener('change', applyFilters);
    
    // Recherche avec délai
    let searchTimeout;
    searchFilter.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500);
    });
    
    // Renvoyer SMS
    window.renvoyerSms = function(notificationId) {
        if (confirm('Voulez-vous renvoyer ce SMS ?')) {
            fetch(`/rh/notifications/${notificationId}/renvoyer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors du renvoi du SMS');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors du renvoi du SMS');
            });
        }
    };
});
</script>
@endsection
