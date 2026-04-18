@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">
                <i class="fas fa-edit text-primary"></i> 
                Modifier l'Offre
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-clock"></i> 
                Créée le {{ $offre->created_at->format('d/m/Y') }} | 
                Dernière modification : {{ $offre->updated_at->format('d/m/Y H:i') }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.offres.show', $offre) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye"></i> Voir
            </a>
            <a href="{{ route('admin.offres') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <form method="POST" action="{{ route('admin.offres.update', $offre) }}">
        @csrf
        @method('PUT')
        
        <!-- Informations principales -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Informations Principales
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Titre de l'offre *</label>
                        <input type="text" 
                               class="form-control @error('titre') ? 'is-invalid' : ''" 
                               name="titre" 
                               placeholder="Ex: Développeur Web Junior" 
                               value="{{ old('titre', $offre->titre) }}"
                               required>
                        @error('titre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Secteur d'activité *</label>
                        <select class="form-select @error('secteur') ? 'is-invalid' : ''" name="secteur" required>
                            <option value="">Choisir un secteur...</option>
                            @foreach($secteurs as $key => $secteur)
                                <option value="{{ $key }}" {{ old('secteur', $offre->secteur) == $key ? 'selected' : '' }}>
                                    {{ $secteur }}
                                </option>
                            @endforeach
                        </select>
                        @error('secteur')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lieu du stage *</label>
                        <input type="text" 
                               class="form-control @error('lieu') ? 'is-invalid' : ''" 
                               name="lieu" 
                               placeholder="Ex: Paris (75)" 
                               value="{{ old('lieu', $offre->lieu) }}"
                               required>
                        @error('lieu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Durée (semaines) *</label>
                        <input type="number" 
                               class="form-control @error('duree_semaines') ? 'is-invalid' : ''" 
                               name="duree_semaines" 
                               placeholder="12" 
                               min="1" 
                               max="52"
                               value="{{ old('duree_semaines', $offre->duree_semaines) }}"
                               required>
                        @error('duree_semaines')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Rémunération (€/mois)</label>
                        <input type="number" 
                               class="form-control @error('remuneration') ? 'is-invalid' : ''" 
                               name="remuneration" 
                               placeholder="800.00" 
                               min="0" 
                               max="9999.99"
                               step="0.01"
                               value="{{ old('remuneration', $offre->remuneration) }}">
                        @error('remuneration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                        <textarea class="form-control @error('description') ? 'is-invalid' : ''" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="Décrivez l'offre, le contexte, les avantages..."
                                  required>{{ old('description', $offre->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Décrivez en détail le poste, l'environnement de travail, les bénéfices pour le stagiaire...</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Missions principales *</label>
                        <textarea class="form-control @error('missions') ? 'is-invalid' : ''" 
                                  name="missions" 
                                  rows="4" 
                                  placeholder="Listez les tâches et responsabilités..."
                                  required>{{ old('missions', $offre->missions) }}</textarea>
                        @error('missions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Listez les missions principales, les tâches quotidiennes, les projets à réaliser...</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dates et entreprise -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-calendar"></i> Période et Entreprise
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date de début</label>
                        <input type="date" 
                               class="form-control @error('date_debut') ? 'is-invalid' : ''" 
                               name="date_debut" 
                               value="{{ old('date_debut', $offre->date_debut ? $offre->date_debut->format('Y-m-d') : '') }}">
                        @error('date_debut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Laissez vide pour "immédiat"</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date de fin</label>
                        <input type="date" 
                               class="form-control @error('date_fin') ? 'is-invalid' : ''" 
                               name="date_fin" 
                               value="{{ old('date_fin', $offre->date_fin ? $offre->date_fin->format('Y-m-d') : '') }}">
                        @error('date_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Date limite de candidature</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Entreprise *</label>
                        <select class="form-select @error('entreprise_id') ? 'is-invalid' : ''" name="entreprise_id" required>
                            <option value="">Choisir une entreprise...</option>
                            @foreach($entreprises as $entreprise)
                                <option value="{{ $entreprise->id }}" {{ old('entreprise_id', $offre->entreprise_id) == $entreprise->id ? 'selected' : '' }}>
                                    {{ $entreprise->nom }} ({{ $entreprise->ville }})
                                </option>
                            @endforeach
                        </select>
                        @error('entreprise_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                        <select class="form-select @error('statut') ? 'is-invalid' : ''" name="statut" required>
                            <option value="">Choisir un statut...</option>
                            <option value="brouillon" {{ old('statut', $offre->statut) == 'brouillon' ? 'selected' : '' }}>
                                📝 Brouillon (non visible publiquement)
                            </option>
                            <option value="publiee" {{ old('statut', $offre->statut) == 'publiee' ? 'selected' : '' }}>
                                ✅ Publiée (visible publiquement)
                            </option>
                            <option value="cloturee" {{ old('statut', $offre->statut) == 'cloturee' ? 'selected' : '' }}>
                                ❌ Clôturée (plus de candidatures)
                            </option>
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <strong>Brouillon :</strong> Enregistrement sans publication<br>
                            <strong>Publiée :</strong> Visible immédiatement par les candidats<br>
                            <strong>Clôturée :</strong> Plus recevable pour les candidatures
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avertissement candidatures -->
        @if($offre->candidatures()->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Attention
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <strong>{{ $offre->candidatures()->count() }} candidature{{ $offre->candidatures()->count() > 1 ? 's' : '' }}</strong> 
                        sont associées à cette offre. La modification de certaines informations peut impacter les candidats existants.
                    </p>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('admin.offres.show', $offre) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <form method="POST" action="{{ route('admin.offres.destroy', $offre) }}" 
                              class="d-inline" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Styles professionnels -->
<style>
.card {
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 10px 10px 0 0 !important;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #ced4da;
}

.form-control:focus, .form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.btn {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.text-muted {
    color: #6c757d !important;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
}

textarea.form-control {
    resize: vertical;
}

@media (max-width: 768px) {
    .btn {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }
}
</style>
@endsection
