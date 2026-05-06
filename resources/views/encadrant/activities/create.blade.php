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
        <div class="col-lg-8">
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
                                <label for="titre" class="form-label">Titre de l'activité *</label>
                                <input type="text" class="form-control" id="titre" name="titre" 
                                       value="{{ old('titre') }}" required maxlength="255">
                                @error('titre')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description *</label>
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
                                    <option value="">Choisir une priorité</option>
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
                                <label for="stagiaire_id" class="form-label">Stagiaire (optionnel)</label>
                                <select class="form-select" id="stagiaire_id" name="stagiaire_id">
                                    <option value="">Ne pas assigner maintenant (activité proposée)</option>
                                    @foreach($stagiaires as $stagiaire)
                                        <option value="{{ $stagiaire->id }}" {{ old('stagiaire_id') == $stagiaire->id ? 'selected' : '' }}>
                                            {{ $stagiaire->prenom }} {{ $stagiaire->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('stagiaire_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Laissez vide pour proposer l'activité à tous vos stagiaires</small>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('encadrant.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour au dashboard
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo me-2"></i>Réinitialiser
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Créer l'activité
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Aide -->
            <div class="card border-0 shadow-sm mb-3">
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
            
            <!-- Statistiques -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Vos activités
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="row">
                            <div class="col-6">
                                <h4 class="text-primary">{{ auth()->user()->activities->count() }}</h4>
                                <small class="text-muted">Total</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success">{{ auth()->user()->activities->where('statut', 'proposee')->count() }}</h4>
                                <small class="text-muted">Proposées</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
