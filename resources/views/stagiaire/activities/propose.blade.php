@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-plus text-success"></i> 
                Proposer une activité
            </h2>
            <small class="text-muted">
                Suggérez une nouvelle activité à votre encadrant
            </small>
        </div>
        <div>
            <a href="{{ route('stagiaire.activities.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour à mes activités
            </a>
        </div>
    </div>

    <!-- Formulaire de proposition -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-lightbulb me-2"></i>Proposition d'activité
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('stagiaire.activities.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="encadrant_id" class="form-label">Encadrant *</label>
                            <select class="form-select" id="encadrant_id" name="encadrant_id" required>
                                <option value="">Sélectionner un encadrant...</option>
                                @foreach($encadrants as $encadrant)
                                    <option value="{{ $encadrant->id }}">
                                        {{ $encadrant->prenom }} {{ $encadrant->nom }}
                                        @if($encadrant->entreprise)
                                            ({{ $encadrant->entreprise->nom }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if($encadrants->isEmpty())
                                <div class="text-danger small mt-1">
                                    Aucun encadrant disponible. Veuillez contacter l'administrateur.
                                </div>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre de l'activité *</label>
                            <input type="text" class="form-control" id="titre" name="titre" required 
                                   placeholder="Ex: Développement d'une application mobile">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required 
                                      placeholder="Décrivez en détail l'activité que vous souhaitez réaliser..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="objectifs" class="form-label">Objectifs pédagogiques</label>
                            <textarea class="form-control" id="objectifs" name="objectifs" rows="3" 
                                      placeholder="Qu'avez-vous l'intention d'apprendre avec cette activité ?"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="competences" class="form-label">Compétences visées</label>
                            <textarea class="form-control" id="competences" name="competences" rows="3" 
                                      placeholder="Quelles compétences souhaitez-vous développer ?"></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        
                        
                        <div class="mb-3">
                            <label for="date_limite" class="form-label">Date limite souhaitée</label>
                            <input type="date" class="form-control" id="date_limite" name="date_limite">
                        </div>
                        
                       
                        
                        <div class="mb-3">
                            <label for="ressources" class="form-label">Ressources nécessaires</label>
                            <textarea class="form-control" id="ressources" name="ressources" rows="3" 
                                      placeholder="Matériel, logiciels, documents nécessaires..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('stagiaire.activities.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-2"></i>Envoyer la proposition
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Informations -->
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>Informations importantes
            </h6>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li class="mb-2">Votre proposition sera envoyée à votre encadrant pour validation.</li>
                <li class="mb-2">L'encadrant pourra accepter, refuser ou demander des modifications.</li>
                <li class="mb-2">Soyez précis dans la description pour faciliter la prise de décision.</li>
                <li class="mb-2">Les activités proposées apparaîtront dans votre liste avec le statut "Proposée".</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const titre = document.getElementById('titre').value.trim();
        const description = document.getElementById('description').value.trim();
        
        if (!titre || !description) {
            e.preventDefault();
            alert('Veuillez remplir les champs obligatoires (Titre et Description).');
            return false;
        }
        
        if (titre.length < 5) {
            e.preventDefault();
            alert('Le titre doit contenir au moins 5 caractères.');
            return false;
        }
        
        if (description.length < 20) {
            e.preventDefault();
            alert('La description doit contenir au moins 20 caractères.');
            return false;
        }
    });
    
    // Auto-ajustement de la date limite minimale (demain)
    const dateLimiteInput = document.getElementById('date_limite');
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    dateLimiteInput.min = tomorrow.toISOString().split('T')[0];
});
</script>
@endsection
