@extends('layouts.app')

@section('title', 'Modifier Entreprise')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-building text-primary"></i> 
                Modifier {{ $entreprise->nom }}
            </h2>
            <small class="text-muted">Mettre à jour les informations de l'entreprise</small>
        </div>
        <div>
            <a href="{{ route('admin.entreprises.show', $entreprise) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Formulaire de modification -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.entreprises.update', $entreprise) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Informations principales -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-sm">
                            <input type="text" 
                                   class="form-control form-control-sm @error('nom') is-invalid @enderror" 
                                   id="nom" 
                                   name="nom" 
                                   value="{{ old('nom', $entreprise->nom) }}" 
                                   required>
                            <label for="nom" class="small">Nom de l'entreprise *</label>
                            @error('nom')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-sm">
                            <input type="text" 
                                   class="form-control form-control-sm @error('secteur') is-invalid @enderror" 
                                   id="secteur" 
                                   name="secteur" 
                                   value="{{ old('secteur', $entreprise->secteur) }}">
                            <label for="secteur" class="small">Secteur d'activité</label>
                            @error('secteur')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-sm">
                            <input type="email" 
                                   class="form-control form-control-sm @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $entreprise->email) }}">
                            <label for="email" class="small">Email</label>
                            @error('email')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-sm">
                            <input type="tel" 
                                   class="form-control form-control-sm @error('telephone') is-invalid @enderror" 
                                   id="telephone" 
                                   name="telephone" 
                                   value="{{ old('telephone', $entreprise->telephone) }}">
                            <label for="telephone" class="small">Téléphone</label>
                            @error('telephone')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating form-floating-sm">
                            <input type="url" 
                                   class="form-control form-control-sm @error('site_web') is-invalid @enderror" 
                                   id="site_web" 
                                   name="site_web" 
                                   value="{{ old('site_web', $entreprise->site_web) }}">
                            <label for="site_web" class="small">Site web</label>
                            @error('site_web')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Adresse -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="fs-6 fw-bold mb-3">Adresse</h6>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating form-floating-sm">
                            <input type="text" 
                                   class="form-control form-control-sm @error('adresse') is-invalid @enderror" 
                                   id="adresse" 
                                   name="adresse" 
                                   value="{{ old('adresse', $entreprise->adresse) }}">
                            <label for="adresse" class="small">Adresse complète</label>
                            @error('adresse')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Règlement Interne -->
               
                <!-- Description -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="fs-6 fw-bold mb-2">Description</h6>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      placeholder="Description de l'entreprise">{{ old('description', $entreprise->description) }}</textarea>
                            <label for="description" class="small">Description</label>
                            @error('description')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Logo -->
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="fs-6 fw-bold mb-3">Logo</h6>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            @if($entreprise->logo_path)
                                <div class="d-flex align-items-center mb-2">
                                    <img src="{{ asset('storage/' . $entreprise->logo_path) }}" 
                                         alt="Logo actuel" 
                                         class="me-2" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                    <div>
                                        <small class="text-muted">Logo actuel</small><br>
                                        <small class="text-muted">{{ basename($entreprise->logo_path) }}</small>
                                    </div>
                                </div>
                            @else
                                <div class="text-muted mb-2">
                                    <i class="fas fa-image fa-2x"></i>
                                    <small class="d-block">Aucun logo</small>
                                </div>
                            @endif
                        </div>
                        <div class="form-floating form-floating-sm">
                            <input type="file" 
                                   class="form-control @error('logo') is-invalid @enderror" 
                                   id="logo" 
                                   name="logo" 
                                   accept="image/*">
                            <label for="logo" class="small">Nouveau logo (optionnel)</label>
                            @error('logo')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Formats acceptés : JPG, PNG, GIF. Taille max : 2MB</small>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('admin.entreprises.show', $entreprise) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Gestion des Secteurs d'Activités -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-industry text-primary me-2"></i>
                Gestion des Secteurs d'Activités
            </h6>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSecteurModal">
                <i class="fas fa-plus me-1"></i> Ajouter un secteur
            </button>
        </div>
    </div>
    <div class="card-body p-4">
        <!-- Liste des secteurs existants -->
        <div class="row" id="secteursList">
            @if($secteurs = \App\Models\Secteur::orderBy('nom')->get())
                @if($secteurs->count() > 0)
                    @foreach($secteurs as $secteur)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-title mb-1">{{ $secteur->nom }}</h6>
                                            @if($secteur->description)
                                                <p class="card-text small text-muted">{{ Str::limit($secteur->description, 80) }}</p>
                                            @endif
                                            <div class="mt-2">
                                                <span class="badge bg-{{ $secteur->actif ? 'success' : 'warning' }}">
                                                    {{ $secteur->actif ? 'Actif' : 'Inactif' }}
                                                </span>
                                                <span class="badge bg-info ms-1">{{ $secteur->offres()->count() }} offre(s)</span>
                                            </div>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btn-sm" onclick="editSecteur({{ $secteur->id }}, '{{ str_replace("'", "\'", $secteur->nom) }}', '{{ str_replace("'", "\'", $secteur->description ?? '') }}', '{{ str_replace("'", "\'", $secteur->lieu ?? '') }}', {{ $secteur->actif ? 'true' : 'false' }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.secteurs.destroy', $secteur) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                @if($secteur->offres()->count() == 0)
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer ce secteur ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('⚠️ ATTENTION ! Ce secteur a {{ $secteur->offres()->count() }} offre(s) associée(s). La suppression pourrait causer des problèmes de données. Continuer quand même ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-4">
                        <i class="fas fa-industry fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucun secteur d'activité</h5>
                        <p class="text-muted">Commencez par ajouter un secteur d'activité.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSecteurModal">
                            <i class="fas fa-plus me-1"></i> Ajouter un secteur
                        </button>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Modal Ajouter Secteur -->
<div class="modal fade" id="addSecteurModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus text-primary me-2"></i>
                    Ajouter un Secteur d'Activité
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.secteurs.store') }}" method="POST" id="addSecteurForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="secteurNom" class="form-label">Nom du secteur <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="secteurNom" name="nom" required>
                        <div class="form-text">Ex: Informatique, Marketing, Finance, etc.</div>
                    </div>
                    <div class="mb-3">
                        <label for="secteurDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="secteurDescription" name="description" rows="3"></textarea>
                        <div class="form-text">Description détaillée du secteur (optionnel)</div>
                    </div>
                    <div class="mb-3">
                        <label for="secteurLieu" class="form-label">Lieu par défaut</label>
                        <input type="text" class="form-control" id="secteurLieu" name="lieu" placeholder="Ex: Tunis, Sfax, Sousse...">
                        <div class="form-text">Lieu qui sera automatiquement rempli dans les offres pour ce secteur</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="secteurActif" name="actif" value="1" checked>
                            <label class="form-check-label" for="secteurActif">
                                Secteur actif
                            </label>
                        </div>
                        <div class="form-text">Un secteur actif peut être utilisé dans les offres de stage</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="addSecteurSubmitBtn">
                        <i class="fas fa-save me-1"></i> Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Secteur -->
<div class="modal fade" id="editSecteurModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Modifier un Secteur d'Activité
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="editSecteurForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="editSecteurId" name="_secteur_id">
                    <div class="mb-3">
                        <label for="editSecteurNom" class="form-label">Nom du secteur <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editSecteurNom" name="nom" required>
                        <div class="form-text">Ex: Informatique, Marketing, Finance, etc.</div>
                    </div>
                    <div class="mb-3">
                        <label for="editSecteurDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editSecteurDescription" name="description" rows="3"></textarea>
                        <div class="form-text">Description détaillée du secteur (optionnel)</div>
                    </div>
                    <div class="mb-3">
                        <label for="editSecteurLieu" class="form-label">Lieu par défaut</label>
                        <input type="text" class="form-control" id="editSecteurLieu" name="lieu" placeholder="Ex: Tunis, Sfax, Sousse...">
                        <div class="form-text">Lieu qui sera automatiquement rempli dans les offres pour ce secteur</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editSecteurActif" name="actif" value="1">
                            <label class="form-check-label" for="editSecteurActif">
                                Secteur actif
                            </label>
                        </div>
                        <div class="form-text">Un secteur actif peut être utilisé dans les offres de stage</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Gestion des Types de Stage -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-briefcase text-primary me-2"></i>
                Gestion des Types de Stage
            </h6>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTypeStageModal">
                <i class="fas fa-plus me-1"></i> Ajouter un type
            </button>
        </div>
    </div>
    <div class="card-body p-4">
        <!-- Liste des types existants -->
        <div class="row" id="typesStagesList">
            @if($typesStages = \App\Models\TypeStage::orderBy('nom')->get())
                @if($typesStages->count() > 0)
                    @foreach($typesStages as $typeStage)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-title mb-1">{{ $typeStage->nom }}</h6>
                                            @if($typeStage->description)
                                                <p class="card-text small text-muted">{{ Str::limit($typeStage->description, 80) }}</p>
                                            @endif
                                            @if($typeStage->code)
                                                <span class="badge bg-secondary">{{ $typeStage->code }}</span>
                                            @endif
                                            <div class="mt-2">
                                                <span class="badge bg-{{ $typeStage->actif ? 'success' : 'warning' }}">
                                                    {{ $typeStage->actif ? 'Actif' : 'Inactif' }}
                                                </span>
                                                <span class="badge bg-info ms-1">{{ $typeStage->offres()->count() }} offre(s)</span>
                                            </div>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btn-sm" onclick="editTypeStage({{ $typeStage->id }}, '{{ $typeStage->nom }}', '{{ $typeStage->code ?? '' }}', '{{ $typeStage->description ?? '' }}', {{ $typeStage->actif ? 'true' : 'false' }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.type-stages.destroy', $typeStage) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                @if($typeStage->offres()->count() == 0)
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer ce type de stage ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('⚠️ ATTENTION ! Ce type de stage a {{ $typeStage->offres()->count() }} offre(s) associée(s). La suppression pourrait causer des problèmes de données. Continuer quand même ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-4">
                        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucun type de stage</h5>
                        <p class="text-muted">Commencez par ajouter un type de stage.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTypeStageModal">
                            <i class="fas fa-plus me-1"></i> Ajouter un type
                        </button>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Modal Ajouter Type Stage -->
<div class="modal fade" id="addTypeStageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus text-primary me-2"></i>
                    Ajouter un Type de Stage
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.type-stages.store') }}" method="POST" id="addTypeStageForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="typeStageNom" class="form-label">Nom du type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="typeStageNom" name="nom" required>
                        <div class="form-text">Ex: Stage technique, Stage commercial, etc.</div>
                    </div>
                   
                    <div class="mb-3">
                        <label for="typeStageDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="typeStageDescription" name="description" rows="3"></textarea>
                        <div class="form-text">Description détaillée du type (optionnel)</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="typeStageActif" name="actif" value="1" checked>
                            <label class="form-check-label" for="typeStageActif">
                                Type actif
                            </label>
                        </div>
                        <div class="form-text">Un type actif peut être utilisé dans les offres de stage</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="addTypeStageSubmitBtn">
                        <i class="fas fa-save me-1"></i> Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Type Stage -->
<div class="modal fade" id="editTypeStageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Modifier un Type de Stage
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="editTypeStageForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editTypeStageNom" class="form-label">Nom du type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editTypeStageNom" name="nom" required>
                        <div class="form-text">Ex: Stage technique, Stage commercial, etc.</div>
                    </div>
                    <div class="mb-3">
                        <label for="editTypeStageCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="editTypeStageCode" name="code" maxlength="10">
                        <div class="form-text">Code court pour le type (optionnel)</div>
                    </div>
                    <div class="mb-3">
                        <label for="editTypeStageDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editTypeStageDescription" name="description" rows="3"></textarea>
                        <div class="form-text">Description détaillée du type (optionnel)</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editTypeStageActif" name="actif" value="1">
                            <label class="form-check-label" for="editTypeStageActif">
                                Type actif
                            </label>
                        </div>
                        <div class="form-text">Un type actif peut être utilisé dans les offres de stage</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.fs-4 {
    font-size: 1.3rem;
    font-weight: 600;
}

.fs-5 {
    font-size: 1.1rem;
    font-weight: 600;
}

.fs-6 {
    font-size: 0.8rem;
    font-weight: 600;
}

.small {
    font-size: 0.7rem;
}

.btn-sm {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}

.card-body.p-4 {
    padding: 1rem !important;
}

.invalid-feedback {
    font-size: 0.7rem;
}

.form-floating-sm > .form-control {
    padding: 0.4rem 0.6rem;
    font-size: 0.8rem;
}

.form-floating-sm > label {
    font-size: 0.65rem;
}
</style>

<script>
function editSecteur(id, nom, description, lieu, actif) {
    console.log('editSecteur appelé avec:', {id, nom, description, lieu, actif});
    
    document.getElementById('editSecteurId').value = id;
    document.getElementById('editSecteurNom').value = nom;
    document.getElementById('editSecteurDescription').value = description;
    document.getElementById('editSecteurLieu').value = lieu || '';
    document.getElementById('editSecteurActif').checked = actif;
    
    // Vérifier les valeurs après assignation
    console.log('Valeurs assignées:');
    console.log('editSecteurId:', document.getElementById('editSecteurId').value);
    console.log('editSecteurNom:', document.getElementById('editSecteurNom').value);
    console.log('editSecteurLieu:', document.getElementById('editSecteurLieu').value);
    
    // Mettre à jour l'action du formulaire
    document.getElementById('editSecteurForm').action = '/admin/secteurs/' + id;
    
    // Afficher la modal
    var modal = new bootstrap.Modal(document.getElementById('editSecteurModal'));
    modal.show();
}

function editTypeStage(id, nom, code, description, actif) {
    document.getElementById('editTypeStageNom').value = nom;
    document.getElementById('editTypeStageCode').value = code;
    document.getElementById('editTypeStageDescription').value = description;
    document.getElementById('editTypeStageActif').checked = actif;
    
    // Mettre à jour l'action du formulaire
    document.getElementById('editTypeStageForm').action = '{{ route('admin.type-stages.update', ':id') }}'.replace(':id', id);
    
    // Afficher la modal
    var modal = new bootstrap.Modal(document.getElementById('editTypeStageModal'));
    modal.show();
}

// Rafraîchir la liste des secteurs après ajout/modification/suppression
function refreshSecteursList() {
    // Ne pas recharger toute la page pour éviter les problèmes d'affichage
    // La page se rechargera automatiquement via la soumission normale du formulaire
}

// Gérer la soumission du formulaire d'ajout
document.getElementById('addSecteurForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    var csrfToken = this.querySelector('input[name="_token"]').value;
    
    // Debug: afficher les données envoyées pour l'ajout
    console.log('Données du formulaire d\'ajout:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    // Debug: vérifier spécifiquement le champ lieu
    const lieuValue = formData.get('lieu');
    console.log('Valeur du champ lieu (ajout):', lieuValue);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addSecteurModal')).hide();
            this.reset();
            // La page se rechargera automatiquement via le contrôleur
        } else {
            alert(data.message || 'Erreur lors de l\'ajout du secteur');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'ajout du secteur');
    });
});

// Gérer la soumission du formulaire de modification
document.getElementById('editSecteurForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    var csrfToken = this.querySelector('input[name="_token"]').value;
    
    // Debug: afficher les données envoyées
    console.log('Données du formulaire:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    // Debug: vérifier spécifiquement le champ nom
    const nomValue = formData.get('nom');
    console.log('Valeur du champ nom:', nomValue);
    console.log('Type de la valeur du nom:', typeof nomValue);
    console.log('Longueur de la valeur du nom:', nomValue ? nomValue.length : 'null');
    
    // Ajouter le champ _method pour simuler PUT
    formData.append('_method', 'PUT');
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editSecteurModal')).hide();
            // La page se rechargera automatiquement via le contrôleur
        } else {
            if (data.errors) {
                let errorMessage = 'Erreur de validation:\n';
                for (let field in data.errors) {
                    errorMessage += `- ${field}: ${data.errors[field].join(', ')}\n`;
                }
                alert(errorMessage);
            } else {
                alert(data.message || 'Erreur lors de la modification du secteur');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la modification du secteur');
    });
});

// Gérer la soumission du formulaire d'ajout de type de stage
document.getElementById('addTypeStageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    var csrfToken = this.querySelector('input[name="_token"]').value;
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addTypeStageModal')).hide();
            this.reset();
            // La page se rechargera automatiquement via le contrôleur
        } else {
            alert(data.message || 'Erreur lors de l\'ajout du type de stage');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'ajout du type de stage');
    });
});

// Le formulaire de modification de type de stage soumet normalement (sans AJAX)
// La soumission normale gérera la redirection et les messages de succès/erreur

// Solution de secours : soumettre le formulaire normalement si AJAX ne fonctionne pas
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si Bootstrap est chargé
    if (typeof bootstrap === 'undefined') {
        console.warn('Bootstrap n\'est pas chargé, les modales ne fonctionneront pas');
        return;
    }
    
    // Si le formulaire AJAX échoue, permettre la soumission normale
    var addForm = document.getElementById('addSecteurForm');
    var editForm = document.getElementById('editSecteurForm');
    var addTypeForm = document.getElementById('addTypeStageForm');
    var editTypeForm = document.getElementById('editTypeStageForm');
    
    // Timeout pour forcer la soumission normale si AJAX prend trop de temps
    var addSubmitBtn = document.getElementById('addSecteurSubmitBtn');
    if (addSubmitBtn) {
        addSubmitBtn.addEventListener('click', function(e) {
            // Forcer la soumission normale si nécessaire
            setTimeout(function() {
                if (!e.defaultPrevented) {
                    addForm.submit();
                }
            }, 100);
        });
    }
    
    var addTypeSubmitBtn = document.getElementById('addTypeStageSubmitBtn');
    if (addTypeSubmitBtn) {
        addTypeSubmitBtn.addEventListener('click', function(e) {
            // Forcer la soumission normale si nécessaire
            setTimeout(function() {
                if (!e.defaultPrevented) {
                    addTypeForm.submit();
                }
            }, 100);
        });
    }
});
</script>
@endsection
