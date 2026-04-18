@extends('layouts.app')

@section('title', 'Espace Candidat')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-graduate"></i> Espace Candidat
                        </h4>
                        @if($email)
                            <div class="badge bg-light text-dark">
                                {{ $email }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(!$email)
                        <!-- Formulaire d'accès -->
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="text-center mb-4">
                                    <i class="fas fa-user-graduate fa-4x text-primary mb-3"></i>
                                    <h5>Accéder à votre espace</h5>
                                    <p class="text-muted">Entrez votre email pour voir vos candidatures et notifications</p>
                                </div>
                                <form method="GET" action="{{ route('candidat.dashboard') }}">
                                    <div class="mb-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" class="form-control" name="email" required placeholder="votre.email@example.com">
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-sign-in-alt"></i> Accéder à mon espace
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Tableau de bord -->
                        <div class="row">
                            <!-- Statistiques -->
                            <div class="col-md-4">
                                <div class="card border-0 bg-primary text-white mb-4">
                                    <div class="card-body text-center">
                                        <i class="fas fa-paper-plane fa-2x mb-2"></i>
                                        <h3>{{ $candidatures->count() }}</h3>
                                        <p class="mb-0">Candidatures envoyées</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-success text-white mb-4">
                                    <div class="card-body text-center">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <h3>{{ $candidatures->where('statut', 'accepte')->count() }}</h3>
                                        <p class="mb-0">Candidatures acceptées</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-info text-white mb-4">
                                    <div class="card-body text-center">
                                        <i class="fas fa-sms fa-2x mb-2"></i>
                                        <h3>{{ $notifications->count() }}</h3>
                                        <p class="mb-0">Notifications reçues</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications récentes -->
                        @if($notifications->isNotEmpty())
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="mb-3">
                                        <i class="fas fa-bell"></i> Notifications récentes
                                    </h5>
                                    <div class="row">
                                        @foreach($notifications->take(3) as $notification)
                                            <div class="col-md-4 mb-3">
                                                <div class="card border-0 shadow-sm h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-start">
                                                            <div class="me-3">
                                                                <i class="fas fa-sms fa-2x text-primary"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="card-title">{{ $notification->sujet }}</h6>
                                                                <p class="card-text text-muted small">{{ Str::limit($notification->contenu, 80) }}</p>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-clock"></i> 
                                                                    {{ $notification->date_envoi ? $notification->date_envoi->format('d/m/Y H:i') : 'En attente' }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('candidat.notifications', ['email' => $email]) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-list"></i> Voir toutes les notifications
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Mes candidatures -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="fas fa-briefcase"></i> Mes candidatures
                                </h5>
                                @if($candidatures->isNotEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Offre</th>
                                                    <th>Entreprise</th>
                                                    <th>Date</th>
                                                    <th>Statut</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($candidatures as $candidature)
                                                    <tr>
                                                        <td>
                                                            <div class="fw-semibold">{{ $candidature->offreStage->titre }}</div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if($candidature->offreStage->entreprise->logo_path)
                                                                    <img src="{{ asset('storage/' . $candidature->offreStage->entreprise->logo_path) }}" 
                                                                         alt="{{ $candidature->offreStage->entreprise->nom }}" 
                                                                         class="rounded me-2" 
                                                                         style="width: 30px; height: 30px; object-fit: cover;">
                                                                @endif
                                                                <div>
                                                                    <div class="fw-semibold">{{ $candidature->offreStage->entreprise->nom }}</div>
                                                                    <small class="text-muted">{{ $candidature->offreStage->lieu }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">{{ $candidature->created_at->format('d/m/Y') }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ 
                                                                $candidature->statut === 'recue' ? 'warning' : 
                                                                ($candidature->statut === 'en_cours' ? 'info' : 
                                                                ($candidature->statut === 'accepte' ? 'success' : 'danger')) }}">
                                                                {{ 
                                                                    $candidature->statut === 'recue' ? 'Reçue' : 
                                                                    ($candidature->statut === 'en_cours' ? 'En cours' : 
                                                                    ($candidature->statut === 'accepte' ? 'Acceptée' : 'Refusée')) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('candidat.candidature', $candidature->id) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i> Voir
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Vous n'avez pas encore postulé à des offres</p>
                                        <a href="{{ route('offres') }}" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Voir les offres disponibles
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
