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

    <!-- Section Évaluations -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-clipboard-check text-primary"></i> 
                        Évaluations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Évaluer l'organisation -->
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-building fa-3x text-primary"></i>
                                    </div>
                                    <h6 class="card-title">Évaluer l'organisation</h6>
                                    <p class="card-text small text-muted">
                                        Évaluez l'organisation de votre stage et l'entreprise d'accueil
                                    </p>
                                    <a href="#" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit me-2"></i>Commencer l'évaluation
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Évaluer l'encadrant -->
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-user-tie fa-3x text-success"></i>
                                    </div>
                                    <h6 class="card-title">Évaluer l'encadrant</h6>
                                    <p class="card-text small text-muted">
                                        Évaluez l'encadrement et le suivi de votre encadrant
                                    </p>
                                    <a href="#" class="btn btn-success btn-sm">
                                        <i class="fas fa-star me-2"></i>Évaluer mon encadrant
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Réaliser une auto-évaluation -->
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-user-check fa-3x text-info"></i>
                                    </div>
                                    <h6 class="card-title">Auto-évaluation</h6>
                                    <p class="card-text small text-muted">
                                        Évaluez votre propre travail et vos compétences
                                    </p>
                                    <a href="#" class="btn btn-info btn-sm">
                                        <i class="fas fa-clipboard me-2"></i>M'auto-évaluer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
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