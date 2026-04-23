@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard Stagiaire</h1>
    <p>Bienvenue, <strong>{{ Auth::user()->nom }} {{ Auth::user()->prenom }}</strong></p>

    <!-- Notifications -->
    @if($notifications->count() > 0)
    <div class="row mb-4">
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

    <div class="row g-4">
        <div class="col-md-12">
            <div class="card h-100 border-secondary">
                <div class="card-body">
                    <h5 class="card-title">Mon Encadrant</h5>
                    <p class="card-text">
                        {{ Auth::user()->encadrant ? Auth::user()->encadrant->nom . ' ' . Auth::user()->encadrant->prenom : 'Aucun encadrant assigné' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
@endsection