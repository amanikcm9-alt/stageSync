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
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <h6 class="fs-6 fw-bold mb-2">Règlement Interne</h6>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control @error('reglement_interne') is-invalid @enderror" 
                                      id="reglement_interne" 
                                      name="reglement_interne" 
                                      rows="6" 
                                      placeholder="Entrez le règlement interne de l'entreprise...">{{ old('reglement_interne', $entreprise->reglement_interne) }}</textarea>
                            <label for="reglement_interne" class="small">Règlement Interne</label>
                            @error('reglement_interne')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="partager_reglement_stagiaires" 
                                   name="partager_reglement_stagiaires" 
                                   value="1" 
                                   {{ old('partager_reglement_stagiaires', $entreprise->partager_reglement_stagiaires) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="partager_reglement_stagiaires">
                                Partager ce règlement avec les stagiaires
                            </label>
                        </div>
                    </div>
                </div>

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
@endsection
