@extends('layouts.app')

@section('content')
    <!-- Interface principale -->
    <div class="row justify-content-center mb-5">
        
        <div class="col-md-6 text-center mb-4">
            <div class="text-primary mb-3">
                <i class="fas fa-user-graduate fa-3x"></i>
            </div>
            <h2 class="font-weight-bold text-dark mb-2">Évaluer le stagiaire</h2>
            <p class="text-muted mb-4">Évaluez les performances et compétences de vos stagiaires.</p>
            <a href="{{ route('evaluations.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-star mr-2"></i> Évaluer un stagiaire
            </a>
        </div>

        <div class="col-md-6 text-center mb-4">
            <div class="text-success mb-3">
                <i class="fas fa-user-check fa-3x"></i>
            </div>
            <h2 class="font-weight-bold text-dark mb-2">Auto-évaluations</h2>
            <p class="text-muted mb-4">Consultez les auto-évaluations des stagiaires.</p>
            <button onclick="window.location.href='/encadrant/dashboard'" class="btn btn-success btn-sm">
                <i class="fas fa-eye mr-2"></i> Voir les auto-évaluations
            </button>
        </div>
    </div>

    <hr class="my-5">

    <div class="text-center">
            <div class="text-muted mb-3">
                <i class="fas fa-clipboard-list fa-3x"></i>
            </div>
            <h3 class="font-weight-bold text-secondary mb-2">Aucune évaluation</h3>
            <p class="text-muted mb-2">Vous n'avez pas encore créé d'évaluations.</p>
            
        </div>

    </div>
@endsection