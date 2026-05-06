@extends('layouts.app')

@section('title', 'Ajouter un Utilisateur')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title">Ajouter un Utilisateur</h1>
        <p class="page-subtitle">Créer un nouveau stagiaire ou encadrant</p>
    </div>
</div>

<!-- Form -->
<div class="container">
    <div class="content-card">
        <form method="POST" action="{{ route('rh.users.store') }}">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom') }}" required>
                        @error('nom')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                        @error('prenom')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone" value="{{ old('telephone') }}">
                        @error('telephone')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        @error('password')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimum 6 caractères</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe *</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="role" class="form-label">Rôle *</label>
                        <select class="form-select" id="role" name="role" required onchange="toggleOffreField()">
                            <option value="">Sélectionner un rôle</option>
                            <option value="stagiaire" {{ old('role') === 'stagiaire' ? 'selected' : '' }}>Stagiaire</option>
                            <option value="encadrant" {{ old('role') === 'encadrant' ? 'selected' : '' }}>Encadrant</option>
                        </select>
                        @error('role')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Description du rôle</label>
                        <div class="alert alert-info mb-0">
                            <small id="role-description">
                                Sélectionnez un rôle pour voir la description
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="offre-row" style="display: none;">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="offre_id" class="form-label">Offre de stage *</label>
                        <select class="form-select" id="offre_id" name="offre_id">
                            <option value="">Sélectionner une offre</option>
                            @if(isset($offres) && $offres->count() > 0)
                                @foreach($offres as $offre)
                                    <option value="{{ $offre->id }}" {{ old('offre_id') == $offre->id ? 'selected' : '' }}>
                                        {{ $offre->titre }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">Aucune offre disponible</option>
                            @endif
                        </select>
                        @error('offre_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Sélectionnez l'offre de stage pour ce stagiaire</small>
                    </div>
                </div>
            </div>

            <div class="row" id="secteur-row" style="display: none;">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="secteur_id" class="form-label">Secteur *</label>
                        <select class="form-select" id="secteur_id" name="secteur_id">
                            <option value="">Sélectionner un secteur</option>
                            @if(isset($secteurs) && $secteurs->count() > 0)
                                @foreach($secteurs as $secteur)
                                    <option value="{{ $secteur->id }}" {{ old('secteur_id') == $secteur->id ? 'selected' : '' }}>
                                        {{ $secteur->nom }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">Aucun secteur disponible</option>
                            @endif
                        </select>
                        @error('secteur_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Un encadrant doit toujours avoir un secteur défini</small>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('rh.users.index') }}" class="btn btn-modern btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
                <button type="submit" class="btn btn-modern btn-primary">
                    <i class="fas fa-save me-2"></i>Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
function toggleOffreField() {
    const roleSelect = document.getElementById('role');
    const offreRow = document.getElementById('offre-row');
    
    if (roleSelect.value === 'stagiaire') {
        offreRow.style.display = 'flex';
        document.getElementById('offre_id').setAttribute('required', 'required');
    } else {
        offreRow.style.display = 'none';
        document.getElementById('offre_id').removeAttribute('required');
    }
}

function toggleSecteurField() {
    const roleSelect = document.getElementById('role');
    const secteurRow = document.getElementById('secteur-row');
    
    if (roleSelect.value === 'encadrant') {
        secteurRow.style.display = 'flex';
        document.getElementById('secteur_id').setAttribute('required', 'required');
    } else {
        secteurRow.style.display = 'none';
        document.getElementById('secteur_id').removeAttribute('required');
    }
}

document.getElementById('role').addEventListener('change', function() {
    const descriptions = {
        'stagiaire': 'Étudiant en stage qui bénéficie d\'un encadrant pour son suivi et sa formation.',
        'encadrant': 'Professionnel qui encadre et supervise un ou plusieurs stagiaires. Un encadrant doit toujours avoir un secteur défini.'
    };
    
    const description = descriptions[this.value] || 'Sélectionnez un rôle pour voir la description';
    document.getElementById('role-description').textContent = description;
    
    toggleOffreField();
    toggleSecteurField();
});

// Initialiser l'affichage au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    toggleOffreField();
    toggleSecteurField();
});
</script>
@endsection
