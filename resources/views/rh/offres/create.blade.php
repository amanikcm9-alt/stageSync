@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">
                <i class="fas fa-plus text-primary"></i> 
                Créer une Nouvelle Offre
            </h1>
            <p class="text-muted mb-0">
                Remplissez les informations ci-dessous pour créer une offre de stage
            </p>
        </div>
        <div>
            <a href="{{ route('rh.offres') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <form method="POST" action="{{ route('rh.offres.store') }}">
        @csrf
        
        <!-- Informations principales -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Informations Principales
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Titre de l'offre *</label>
                        <input type="text" class="form-control" name="titre" placeholder="Ex: Développeur Web Junior" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Secteur d'activité *</label>
                        <select class="form-select" name="secteur_id" required>
                            <option value="">Choisir un secteur...</option>
                            @foreach(\App\Models\Secteur::actif()->get() as $secteur)
                                <option value="{{ $secteur->id }}">{{ $secteur->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Type de stage *</label>
                        <select class="form-select" name="type_stage_id" required>
                            <option value="">Choisir un type...</option>
                            @foreach(\App\Models\TypeStage::actif()->get() as $typeStage)
                                <option value="{{ $typeStage->id }}">{{ $typeStage->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lieu du stage *</label>
                        <input type="text" class="form-control" name="lieu" placeholder="Ex: Paris (75)" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Rémunération (TND/mois)</label>
                        <input type="number" class="form-control" name="remuneration" placeholder="800.00" min="0" max="9999.99" step="0.01">
                        <small class="text-muted">Optionnel - Laissez vide si non spécifié</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description et missions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt"></i> Description et Missions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Description de l'offre *</label>
                        <textarea class="form-control" name="description" rows="4" placeholder="Décrivez l'offre, le contexte, les avantages..." required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Missions principales *</label>
                        <textarea class="form-control" name="missions" rows="4" placeholder="Listez les tâches et responsabilités..." required></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dates -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-calendar"></i> Période du Stage
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date de début</label>
                        <input type="date" class="form-control" name="date_debut">
                        <small class="text-muted">Laissez vide pour "immédiat"</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date de fin</label>
                        <input type="date" class="form-control" name="date_fin">
                        <small class="text-muted">Date limite de candidature</small>
                    </div>
                   
                                    </div>
            </div>
        </div>

        <!-- Statut -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-cog"></i> Publication
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Statut de l'offre *</label>
                        <select class="form-select" name="statut" required>
                            <option value="">Choisir un statut...</option>
                            <option value="brouillon">📝 Brouillon (non visible publiquement)</option>
                            <option value="publiee">✅ Publiée (visible publiquement)</option>
                            <option value="cloturee">❌ Clôturée (plus de candidatures)</option>
                        </select>
                        <small class="text-muted">
                            <strong>Brouillon :</strong> Enregistrement sans publication<br>
                            <strong>Publiée :</strong> Visible immédiatement par les candidats<br>
                            <strong>Clôturée :</strong> Plus recevable pour les candidatures
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('rh.offres') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Créer l'offre
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la visibilité du champ entreprise
    const roleSelect = document.getElementById('role');
    const offreRow = document.getElementById('offre-row');
    const entrepriseField = document.getElementById('entreprise-field');
    const offreIdField = document.getElementById('offre-id-field');
    
    if (roleSelect && offreRow && entrepriseField && offreIdField) {
        roleSelect.addEventListener('change', function() {
            const selectedRole = this.value;
            
            if (selectedRole === 'stagiaire') {
                offreRow.style.display = 'block';
                entrepriseField.style.display = 'none';
                offreIdField.style.display = 'block';
                entrepriseField.querySelector('select').required = false;
                offreIdField.querySelector('select').required = true;
            } else {
                offreRow.style.display = 'none';
                entrepriseField.style.display = 'block';
                offreIdField.style.display = 'none';
                entrepriseField.querySelector('select').required = true;
                offreIdField.querySelector('select').required = false;
            }
        });
        
        // Initialiser l'état
        roleSelect.dispatchEvent(new Event('change'));
    }
    
    // Remplissage automatique du lieu selon le secteur
    const secteurSelect = document.querySelector('select[name="secteur_id"]');
    const lieuInput = document.querySelector('input[name="lieu"]');
    
    console.log('Secteur select trouvé:', secteurSelect);
    console.log('Lieu input trouvé:', lieuInput);
    
    if (secteurSelect && lieuInput) {
        secteurSelect.addEventListener('change', function() {
            const selectedSecteurId = this.value;
            console.log('Secteur sélectionné:', selectedSecteurId);
            
            if (selectedSecteurId) {
                // Récupérer le lieu du secteur depuis la base de données
                fetch(`/rh/api/secteurs/${selectedSecteurId}/lieu`)
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Données reçues:', data);
                        if (data.success && data.lieu) {
                            lieuInput.value = data.lieu;
                            lieuInput.placeholder = `Lieu: ${data.lieu}`;
                            console.log('Lieu mis à jour:', data.lieu);
                        } else {
                            lieuInput.value = '';
                            lieuInput.placeholder = 'Ex: Tunis, Sfax, Sousse...';
                            console.log('Pas de lieu trouvé ou erreur');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement du lieu:', error);
                        // Logique de base pour déterminer le lieu selon le secteur
                        const secteurNom = this.options[this.selectedIndex].text.toLowerCase();
                        
                        if (secteurNom.includes('informatique') || secteurNom.includes('technologie')) {
                            lieuInput.value = 'Tunis';
                            lieuInput.placeholder = 'Lieu: Tunis';
                        } else if (secteurNom.includes('finance') || secteurNom.includes('bancaire')) {
                            lieuInput.value = 'Tunis';
                            lieuInput.placeholder = 'Lieu: Tunis';
                        } else if (secteurNom.includes('santé') || secteurNom.includes('médical')) {
                            lieuInput.value = 'Sfax';
                            lieuInput.placeholder = 'Lieu: Sfax';
                        } else if (secteurNom.includes('industrie') || secteurNom.includes('manufacturier')) {
                            lieuInput.value = 'Sousse';
                            lieuInput.placeholder = 'Lieu: Sousse';
                        } else {
                            lieuInput.value = '';
                            lieuInput.placeholder = 'Ex: Tunis, Sfax, Sousse...';
                        }
                    });
            } else {
                lieuInput.value = '';
                lieuInput.placeholder = 'Ex: Tunis, Sfax, Sousse...';
            }
        });
    }
    
    // Validation des dates
    const dateDebutInput = document.querySelector('input[name="date_debut"]');
    const dateFinInput = document.querySelector('input[name="date_fin"]');
    
    if (dateDebutInput && dateFinInput) {
        let validationTimeout;
        
        function validerDates() {
            // Annuler le timeout précédent
            clearTimeout(validationTimeout);
            
            // Retarder la validation pour permettre la saisie complète
            validationTimeout = setTimeout(() => {
                const dateDebut = new Date(dateDebutInput.value);
                const dateFin = new Date(dateFinInput.value);
                
                // Valider uniquement si les deux dates sont complètes
                if (dateDebutInput.value && dateFinInput.value && 
                    dateDebutInput.value.length === 10 && dateFinInput.value.length === 10) {
                    
                    if (dateFin <= dateDebut) {
                        dateFinInput.setCustomValidity('La date de fin doit être supérieure à la date de début');
                        // N'afficher la validation que lors de la perte de focus ou du blur
                    } else {
                        dateFinInput.setCustomValidity('');
                    }
                } else {
                    // Réinitialiser la validation si les dates ne sont pas complètes
                    dateFinInput.setCustomValidity('');
                }
            }, 500); // Attendre 500ms après la dernière saisie
        }
        
        // Valider lors de la perte de focus (blur) pour une meilleure expérience
        dateDebutInput.addEventListener('blur', validerDates);
        dateFinInput.addEventListener('blur', validerDates);
        
        // Valider lors du changement mais avec délai
        dateDebutInput.addEventListener('input', validerDates);
        dateFinInput.addEventListener('input', validerDates);
        
        // Valider lors de la soumission du formulaire
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            console.log('Soumission du formulaire détectée');
            
            clearTimeout(validationTimeout);
            validerDates();
            
            // Si la validation échoue, empêcher la soumission
            if (dateFinInput.validationMessage) {
                console.log('Validation des dates échouée:', dateFinInput.validationMessage);
                e.preventDefault();
                dateFinInput.reportValidity();
                return;
            }
            
            // Vérifier tous les champs requis
            const requiredFields = form.querySelectorAll('[required]');
            let allValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    console.log('Champ requis vide:', field.name, field.value);
                    allValid = false;
                    field.reportValidity();
                }
            });
            
            if (!allValid) {
                console.log('Validation des champs requis échouée');
                e.preventDefault();
                return;
            }
            
            console.log('Formulaire valide, soumission en cours...');
        });
    }
});
</script>
@endsection
