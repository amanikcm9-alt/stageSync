@extends('layouts.app')

@section('title', 'Créer une activité')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title">Créer une activité</h1>
        <p class="page-subtitle">Proposer une nouvelle activité pour vos stagiaires</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <!-- Formulaire -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-plus me-2"></i>Nouvelle activité
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('activities.store') }}">
                                @csrf
                                
                                <!-- Informations générales -->
                                <div class="mb-4">
                                    <h6 class="text-primary mb-3">Informations générales</h6>
                                    
                                    <div class="mb-3">
                                        <label for="titre" class="form-label">Titre de l'activité </label>
                                        <input type="text" class="form-control" id="titre" name="titre" 
                                               value="{{ old('titre') }}" required maxlength="255">
                                        @error('titre')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description </label>
                                        <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="objectifs" class="form-label">Objectifs pédagogiques</label>
                                        <textarea class="form-control" id="objectifs" name="objectifs" rows="3">{{ old('objectifs') }}</textarea>
                                        @error('objectifs')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="livrables_attendus" class="form-label">Livrables attendus</label>
                                        <textarea class="form-control" id="livrables_attendus" name="livrables_attendus" rows="3">{{ old('livrables_attendus') }}</textarea>
                                        @error('livrables_attendus')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="support_path" class="form-label">Support de l'activité</label>
                                        <input type="file" class="form-control" id="support_path" name="support_path" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip,.rar">
                                    
                                        @error('support_path')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Planification -->
                                <div class="mb-4">
                                    <h6 class="text-primary mb-3">Planification</h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="date_debut" class="form-label">Date de début</label>
                                                <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                                       value="{{ old('date_debut') }}">
                                                @error('date_debut')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="date_fin" class="form-label">Date de fin</label>
                                                <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                                       value="{{ old('date_fin') }}">
                                                @error('date_fin')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="date_limite" class="form-label">Date limite de rendu</label>
                                        <input type="date" class="form-control" id="date_limite" name="date_limite" 
                                               value="{{ old('date_limite') }}">
                                        @error('date_limite')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Priorité -->
                                <div class="mb-4">
                                    <h6 class="text-primary mb-3">Priorité</h6>
                                    
                                    <div class="mb-3">
                                        <label for="priorite" class="form-label">Niveau de priorité *</label>
                                        <select class="form-select" id="priorite" name="priorite" required>
                                            <option value="" disabled selected hidden >Choisir une priorité</option>
                                            <option value="basse" {{ old('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                                            <option value="moyenne" {{ old('priorite') == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                                            <option value="haute" {{ old('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                                            <option value="urgente" {{ old('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                        </select>
                                        @error('priorite')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Assignation -->
                                <div class="mb-4">
                                    <h6 class="text-primary mb-3">Assignation</h6>
                                    
                                    <div class="mb-3">
                                        <label for="stagiaire_id" class="form-label">Assigner à</label>
                                        <select class="form-select" id="stagiaire_id" name="stagiaire_id">
                                            <option value="" disabled selected hidden>Sélectionner un stagiaire</option>
                                            @foreach($stagiaires as $stagiaire)
                                                <option value="{{ $stagiaire->id }}" 
                                                        data-prenom="{{ $stagiaire->prenom }}"
                                                        data-nom="{{ $stagiaire->nom }}"
                                                        data-offre="{{ $stagiaire->offre_stage ? $stagiaire->offre_stage->titre : 'Aucune offre' }}"
                                                        {{ old('stagiaire_id') == $stagiaire->id ? 'selected' : '' }} {{ request()->get('stagiaire_id') == $stagiaire->id ? 'selected' : '' }}>
                                                    {{ $stagiaire->prenom }} {{ $stagiaire->nom }}
                                                    @if($stagiaire->offre_stage)
                                                        - {{ $stagiaire->offre_stage->titre }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('stagiaire_id')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Créer l'activité
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Aide sur le côté -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-question-circle me-2"></i>Aide
                            </h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-primary">Conseils pour créer une activité</h6>
                            <ul class="small">
                                <li>Soyez précis dans le titre et la description</li>
                                <li>Définissez clairement les objectifs pédagogiques</li>
                                <li>Précisez les livrables attendus</li>
                                <li>Fixez des dates réalistes</li>
                                <li>Choisissez une priorité appropriée</li>
                            </ul>
                            
                            <h6 class="text-primary mt-3">Types d'activités</h6>
                            <ul class="small">
                                <li><strong>Recherche:</strong> Documentation, veille</li>
                                <li><strong>Pratique:</strong> Développement, design</li>
                                <li><strong>Analyse:</strong> Étude de cas, rapport</li>
                                <li><strong>Présentation:</strong> Exposé, démo</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Fonction globale pour fermer le message de succès
function closeSuccessMessage() {
    console.log('DEBUG: Fermeture du message de succès');
    const messageElement = document.getElementById('successMessage');
    if (messageElement) {
        // Animation de fermeture
        messageElement.style.transform = 'translate(-50%, -50%) scale(0.8)';
        messageElement.style.opacity = '0';
        
        // Supprimer l'élément après l'animation
        setTimeout(() => {
            messageElement.remove();
        }, 300);
    }
}

// Calculer automatiquement la date limite de rendu (une semaine après la date de fin)
document.addEventListener('DOMContentLoaded', function() {
    const dateDebutInput = document.getElementById('date_debut');
    const dateFinInput = document.getElementById('date_fin');
    const dateLimiteInput = document.getElementById('date_limite');
    
    function calculerDateLimite() {
        const dateFin = dateFinInput.value;
        
        if (dateFin) {
            // Créer un objet Date à partir de la date de fin
            const dateFinObj = new Date(dateFin);
            
            // Ajouter 7 jours (une semaine) à la date de fin
            dateFinObj.setDate(dateFinObj.getDate() + 7);
            
            // Formater la date en YYYY-MM-DD pour l'input de type date
            const annee = dateFinObj.getFullYear();
            const mois = String(dateFinObj.getMonth() + 1).padStart(2, '0');
            const jour = String(dateFinObj.getDate()).padStart(2, '0');
            const dateLimite = `${annee}-${mois}-${jour}`;
            
            // Définir la date limite de rendu
            dateLimiteInput.value = dateLimite;
        }
    }
    
    // Écouter les changements sur la date de fin
    dateFinInput.addEventListener('change', calculerDateLimite);
    
    // Calculer la date limite si une date de fin est déjà définie (cas de l'édition)
    if (dateFinInput.value && !dateLimiteInput.value) {
        calculerDateLimite();
    }
    
    // Intercepter la soumission du formulaire pour afficher le message de succès
    const form = document.querySelector('form[method="POST"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Désactiver le bouton de soumission
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Création en cours...';
            }
            
            // Soumettre le formulaire normalement
            // Le message sera affiché après la redirection si la création réussit
        });
    }
    
    // Vérifier si nous venons de créer une activité avec succès
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === 'created') {
        // Créer et afficher le message de succès
        const messageHtml = `
            <div id="successMessage" class="position-fixed top-50 start-50 translate-middle" style="z-index: 9999;">
                <div class="card border-0 shadow-lg" style="min-width: 300px; max-width: 400px;">
                    <div class="card-header bg-success text-white py-3 position-relative">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-check-circle fa-lg me-2"></i>
                            <h5 class="mb-0 fw-bold">Succès!</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-3" style="opacity: 0.8;" aria-label="Fermer" onclick="closeSuccessMessage()"></button>
                    </div>
                    <div class="card-body text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-plus-circle fa-3x text-success"></i>
                        </div>
                        <p class="mb-0 fs-6 text-muted">activité créée avec succès</p>
                    </div>
                    <div class="card-footer bg-light text-center py-2">
                        <small class="text-muted">L'activité a été ajoutée</small>
                    </div>
                </div>
            </div>
        `;
        
        // Ajouter le message au début du body
        document.body.insertAdjacentHTML('afterbegin', messageHtml);
        
        // Animation d'entrée
        const messageElement = document.getElementById('successMessage');
        messageElement.style.transform = 'translate(-50%, -50%) scale(0.8)';
        messageElement.style.opacity = '0';
        messageElement.style.transition = 'transform 0.3s ease-in-out, opacity 0.3s ease-in-out';
        
        setTimeout(() => {
            messageElement.style.transform = 'translate(-50%, -50%) scale(1)';
            messageElement.style.opacity = '1';
        }, 100);
        
        // Nettoyer l'URL pour ne pas afficher le message au rechargement
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>


@endsection
