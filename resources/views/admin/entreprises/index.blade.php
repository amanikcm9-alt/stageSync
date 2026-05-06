@extends('layouts.app')

@section('title', 'Gestion Entreprise')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-building text-primary"></i> 
                Gestion Entreprise
            </h2>
            <small class="text-muted">{{ $entreprise->nom ?? 'Tech Innovation Solutions' }} - Informations et règlements</small>
        </div>
        <div>
            <a href="{{ route('admin.entreprises.show', $entreprise->id ?? 1) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-eye"></i> Voir détails
            </a>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        @if($entreprise->logo_path)
                            <img src="{{ asset('storage/' . $entreprise->logo_path) }}" 
                                 alt="Logo {{ $entreprise->nom }}" 
                                 class="me-3 rounded" 
                                 style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="me-3 rounded bg-primary d-flex align-items-center justify-content-center text-white fw-bold" 
                                 style="width: 60px; height: 60px;">
                                {{ strtoupper(substr($entreprise->nom ?? 'Tech Innovation Solutions', 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-1 fs-5">{{ $entreprise->nom ?? 'Tech Innovation Solutions' }}</h5>
                            <small class="text-muted">{{ $entreprise->secteur ?? 'Technologies de l\'Information' }}</small>
                            <div>
                                <span class="badge bg-{{ $entreprise->actif ? 'success' : 'secondary' }} small">
                                    {{ $entreprise->actif ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Informations principales -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="fs-6 fw-bold mb-2">Informations</h6>
                            <div class="small">
                                <div class="mb-2">
                                    <strong class="text-muted">Secteur:</strong><br>
                                    <span class="text-dark">{{ $entreprise->secteur_activite ?? 'Non spécifié' }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-muted">Email:</strong><br>
                                    <span class="text-dark">{{ $entreprise->email ?? 'Non spécifié' }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-muted">Téléphone:</strong><br>
                                    <span class="text-dark">{{ $entreprise->telephone ?? 'Non spécifié' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fs-6 fw-bold mb-2">Adresse</h6>
                            <div class="small">
                                <div class="mb-2">
                                    <strong class="text-muted">Siège social:</strong><br>
                                    <span class="text-dark">{{ $entreprise->adresse ?? 'Non spécifié' }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-muted">Ville:</strong><br>
                                    <span class="text-dark">{{ $entreprise->ville ?? 'Non spécifié' }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-muted">Code postal:</strong><br>
                                    <span class="text-dark">{{ $entreprise->code_postal ?? 'Non spécifié' }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-muted">Pays:</strong><br>
                                    <span class="text-dark">{{ $entreprise->pays ?? 'Non spécifié' }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-muted">Site web:</strong><br>
                                    @if($entreprise->site_web)
                                        <a href="{{ $entreprise->site_web }}" target="_blank" class="text-primary">
                                            {{ $entreprise->site_web }}
                                        </a>
                                    @else
                                        <span class="text-muted">Non spécifié</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body p-3">
                            <h6 class="fs-6 fw-bold mb-3">Actions</h6>
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.entreprises.edit', $entreprise->id ?? 1) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit"></i> Modifier l'entreprise
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

.badge.small {
    font-size: 0.65rem;
    padding: 0.2rem 0.4rem;
}

.card-body.p-3 {
    padding: 0.8rem !important;
}
</style>
@endsection
