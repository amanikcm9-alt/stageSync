@extends('layouts.public')

@section('title', 'Postuler - ' . $offre->titre)

@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header compact -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-1 fs-5">
                        <i class="fas fa-paper-plane"></i> Postuler à cette offre
                    </h5>
                    <small class="opacity-75">{{ $offre->titre }}</small>
                </div>
                <div class="card-body p-3">
                    <!-- Détails de l'offre -->
                    <div class="alert alert-info py-2 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fs-6 fw-bold">{{ $offre->titre }}</h6>
                                <small class="text-muted">
                                    <strong>{{ $offre->entreprise->nom }}</strong><br>
                                    <i class="fas fa-map-marker-alt"></i> {{ $offre->lieu }} | 
                                    <i class="fas fa-clock"></i> {{ $offre->duree }} semaines
                                </small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> {{ $offre->date_debut->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('candidatures.store', $offre) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Informations personnelles -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-sm">
                                    <input type="text" 
                                           class="form-control form-control-sm @error('nom') is-invalid @enderror" 
                                           id="nom" 
                                           name="nom" 
                                           placeholder="Nom"
                                           value="{{ old('nom') }}" 
                                           required>
                                    <label for="nom" class="small">Nom *</label>
                                    @error('nom')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-sm">
                                    <input type="text" 
                                           class="form-control form-control-sm @error('prenom') is-invalid @enderror" 
                                           id="prenom" 
                                           name="prenom" 
                                           placeholder="Prénom"
                                           value="{{ old('prenom') }}" 
                                           required>
                                    <label for="prenom" class="small">Prénom *</label>
                                    @error('prenom')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-sm">
                                    <input type="email" 
                                           class="form-control form-control-sm @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           placeholder="Email"
                                           value="{{ old('email') }}" 
                                           required>
                                    <label for="email" class="small">Email *</label>
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
                                           placeholder="Téléphone"
                                           value="{{ old('telephone') }}" 
                                           required>
                                    <label for="telephone" class="small">Téléphone *</label>
                                    @error('telephone')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Formation -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-sm">
                                    <input type="text" 
                                           class="form-control form-control-sm @error('etablissement') is-invalid @enderror" 
                                           id="etablissement" 
                                           name="etablissement" 
                                           placeholder="Établissement"
                                           value="{{ old('etablissement') }}">
                                    <label for="etablissement" class="small">Établissement</label>
                                    @error('etablissement')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-sm">
                                    <input type="text" 
                                           class="form-control form-control-sm @error('formation') is-invalid @enderror" 
                                           id="formation" 
                                           name="formation" 
                                           placeholder="Formation"
                                           value="{{ old('formation') }}">
                                    <label for="formation" class="small">Formation</label>
                                    @error('formation')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Adresse -->
                        <div class="mb-3">
                            <div class="form-floating form-floating-sm">
                                <textarea class="form-control form-control-sm @error('adresse') is-invalid @enderror" 
                                          id="adresse" 
                                          name="adresse" 
                                          placeholder="Votre adresse complète"
                                          style="height: 60px"
                                          required>{{ old('adresse') }}</textarea>
                                <label for="adresse" class="small">Adresse *</label>
                                @error('adresse')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- CV -->
                        <div class="mb-3">
                            <label for="cv" class="form-label small fw-bold">CV (PDF, max 2MB) *</label>
                            <input type="file" 
                                   class="form-control form-control-sm @error('cv') is-invalid @enderror" 
                                   id="cv" 
                                   name="cv" 
                                   accept=".pdf" 
                                   required>
                            @error('cv')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format PDF uniquement, taille maximale 2MB</small>
                        </div>

                        <!-- Lettre de motivation -->
                        <div class="mb-3">
                            <label for="lettre_motivation" class="form-label small fw-bold">Lettre de motivation *</label>
                            <textarea class="form-control form-control-sm @error('lettre_motivation') is-invalid @enderror" 
                                      id="lettre_motivation" 
                                      name="lettre_motivation" 
                                      rows="4" 
                                      placeholder="Présentez-vous et expliquez pourquoi vous êtes intéressé par cette offre..."
                                      required>{{ old('lettre_motivation') }}</textarea>
                            @error('lettre_motivation')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('offres.show', $offre) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Retour à l'offre
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-paper-plane"></i> Envoyer ma candidature
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.fs-5 {
    font-size: 1.25rem;
    font-weight: 600;
}

.fs-6 {
    font-size: 0.875rem;
    font-weight: 600;
}

.form-floating-sm > .form-control {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.form-floating-sm > label {
    font-size: 0.75rem;
}

.card-body.p-3 {
    padding: 1rem !important;
}

.card-header.py-3 {
    padding: 0.75rem 1rem !important;
}

.alert.py-2 {
    padding: 0.5rem 1rem !important;
}

.small {
    font-size: 0.75rem;
}

.btn-sm {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.invalid-feedback {
    font-size: 0.75rem;
}
</style>
@endsection
