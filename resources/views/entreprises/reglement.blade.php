@extends('layouts.app')

@section('title', 'Règlement Interne - ' . $entreprise->nom)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-file-contract text-primary"></i> 
                Règlement Interne
            </h2>
            <small class="text-muted">{{ $entreprise->nom }}</small>
        </div>
        <div>
            <a href="{{ route('entreprises.show', $entreprise) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour à l'entreprise
            </a>
        </div>
    </div>

    <!-- Règlement -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 fs-5">
                <i class="fas fa-building me-2"></i>
                {{ $entreprise->nom }}
            </h5>
            <small class="d-block">{{ $entreprise->secteur }}</small>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                            <div>
                                <h6 class="mb-1">Règlement Interne Partagé</h6>
                                <p class="mb-0 text-muted">Ce règlement est partagé avec les stagiaires de l'entreprise.</p>
                            </div>
                        </div>
                        
                        <div class="reglement-content">
                            {!! nl2br(e($entreprise->reglement_interne)) !!}
                        </div>
                        
                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                Mis à jour le : {{ $entreprise->updated_at->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="text-center mt-4">
        <a href="{{ route('entreprises.show', $entreprise) }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Retour à l'entreprise
        </a>
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

.reglement-content {
    font-size: 1rem;
    line-height: 1.6;
    white-space: pre-wrap;
    background-color: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.card-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.card-body.p-4 {
    padding: 1.5rem !important;
}

.alert-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%);
    border: 1px solid #b3e5fc;
}

.alert .fa-2x {
    font-size: 2rem;
}
</style>
@endsection
