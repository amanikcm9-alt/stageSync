@extends('layouts.app')

@section('title', 'Dashboard - Statistiques')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title">Tableau de Bord</h1>
        <p class="page-subtitle">Statistiques générales du système</p>
    </div>
</div>

<!-- Statistics Grid -->
<div class="container">
    <!-- First Row - Main Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card fade-in-up">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number">{{ \App\Models\User::count() }}</div>
                <div class="stat-label">Utilisateurs Total</div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card fade-in-up">
                <div class="stat-icon success">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-number">{{ \App\Models\User::whereHas('role', function($query) { $query->where('name', 'stagiaire'); })->count() }}</div>
                <div class="stat-label">Stagiaires</div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card fade-in-up">
                <div class="stat-icon warning">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-number">{{ \App\Models\User::whereHas('role', function($query) { $query->where('name', 'encadrant'); })->count() }}</div>
                <div class="stat-label">Encadrants</div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card fade-in-up">
                <div class="stat-icon info">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-number">{{ \App\Models\User::whereHas('role', function($query) { $query->where('name', 'rh'); })->count() }}</div>
                <div class="stat-label">RH</div>
            </div>
        </div>
    </div>

    <!-- Second Row - Business Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card fade-in-up">
                <div class="stat-icon primary">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-number">{{ \App\Models\OffreStage::count() }}</div>
                <div class="stat-label">Offres de Stage</div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card fade-in-up">
                <div class="stat-icon success">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-number">{{ \App\Models\Entreprise::count() }}</div>
                <div class="stat-label">Entreprises</div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card fade-in-up">
                <div class="stat-icon warning">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number">{{ \App\Models\Candidature::count() }}</div>
                <div class="stat-label">Candidatures</div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card fade-in-up">
                <div class="stat-icon info">
                    <i class="fas fa-link"></i>
                </div>
                <div class="stat-number">{{ \App\Models\User::whereHas('role', function($query) { $query->where('name', 'stagiaire'); })->whereNotNull('encadrant_id')->count() }}</div>
                <div class="stat-label">Affectations</div>
            </div>
        </div>
    </div>

    <!-- Third Row - Status Statistics -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="content-card">
                <h5 class="mb-3">Statut des Utilisateurs</h5>
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="stat-number text-success">{{ \App\Models\User::whereNotNull('email_verified_at')->count() }}</div>
                            <div class="stat-label">Actifs</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="stat-number text-warning">{{ \App\Models\User::whereNull('email_verified_at')->count() }}</div>
                            <div class="stat-label">En attente</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="content-card">
                <h5 class="mb-3">Statut des Offres</h5>
                <div class="row">
                    <div class="col-4">
                        <div class="text-center">
                            <div class="stat-number text-success">{{ \App\Models\OffreStage::where('statut', 'publie')->count() }}</div>
                            <div class="stat-label">Publiées</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="stat-number text-warning">{{ \App\Models\OffreStage::where('statut', 'brouillon')->count() }}</div>
                            <div class="stat-label">Brouillons</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="stat-number text-secondary">{{ \App\Models\OffreStage::where('statut', 'cloture')->count() }}</div>
                            <div class="stat-label">Clôturées</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="content-card">
                <h5 class="mb-3">Statut des Candidatures</h5>
                <div class="row">
                    <div class="col-3">
                        <div class="text-center">
                            <div class="stat-number text-warning">{{ \App\Models\Candidature::where('statut', 'recue')->count() }}</div>
                            <div class="stat-label">Reçues</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="text-center">
                            <div class="stat-number text-info">{{ \App\Models\Candidature::where('statut', 'en_cours')->count() }}</div>
                            <div class="stat-label">En cours</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="text-center">
                            <div class="stat-number text-success">{{ \App\Models\Candidature::where('statut', 'accepte')->count() }}</div>
                            <div class="stat-label">Acceptées</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="text-center">
                            <div class="stat-number text-danger">{{ \App\Models\Candidature::where('statut', 'refuse')->count() }}</div>
                            <div class="stat-label">Refusées</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fourth Row - Recent Activity -->
    <div class="row">
        <div class="col-lg-6">
            <div class="content-card">
                <h5 class="mb-3">Utilisateurs Récents</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Rôle</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $recentUsers = \App\Models\User::with('role')
                                    ->latest()
                                    ->take(5)
                                    ->get();
                            @endphp
                            @forelse($recentUsers as $user)
                                <tr>
                                    <td>{{ $user->nom }} {{ $user->prenom }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'rh' ? 'info' : ($user->role->name === 'encadrant' ? 'success' : 'primary')) }}">
                                            {{ ucfirst($user->role->name) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucun utilisateur récent</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="content-card">
                <h5 class="mb-3">Offres Récentes</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Entreprise</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $recentOffres = \App\Models\OffreStage::with('entreprise')
                                    ->latest()
                                    ->take(5)
                                    ->get();
                            @endphp
                            @forelse($recentOffres as $offre)
                                <tr>
                                    <td>{{ $offre->titre }}</td>
                                    <td>{{ $offre->entreprise->nom }}</td>
                                    <td>{{ $offre->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucune offre récente</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Animation Script -->
<script>
    // Add staggered animation to stat cards
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.fade-in-up');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });
</script>
@endsection
