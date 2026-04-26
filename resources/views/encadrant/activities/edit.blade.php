@extends('layouts.app')

@section('title', 'Modifier une activité')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title">Modifier une activité</h1>
        <p class="page-subtitle">Mettre à jour les informations de l'activité</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Modifier l'activité
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('activities.update', $activity) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informations générales -->
                        <div class="mb-4">
                            <h6 class="text-warning mb-3">Informations générales</h6>
                            
                            <div class="mb-3">
                                <label for="titre" class="form-label">Titre de l'activité *</label>
                                <input type="text" class="form-control" id="titre" name="titre" 
                                       value="{{ old('titre', $activity->titre) }}" required maxlength="255">
                                @error('titre')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description', $activity->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="objectifs" class="form-label">Objectifs pédagogiques</label>
                                <textarea class="form-control" id="objectifs" name="objectifs" rows="3">{{ old('objectifs', $activity->objectifs) }}</textarea>
                                @error('objectifs')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="livrables_attendus" class="form-label">Livrables attendus</label>
                                <textarea class="form-control" id="livrables_attendus" name="livrables_attendus" rows="3">{{ old('livrables_attendus', $activity->livrables_attendus) }}</textarea>
                                @error('livrables_attendus')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Planification -->
                        <div class="mb-4">
                            <h6 class="text-warning mb-3">Planification</h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_debut" class="form-label">Date de début</label>
                                        <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                               value="{{ old('date_debut', $activity->date_debut ? $activity->date_debut->format('Y-m-d') : '') }}">
                                        @error('date_debut')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_fin" class="form-label">Date de fin</label>
                                        <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                               value="{{ old('date_fin', $activity->date_fin ? $activity->date_fin->format('Y-m-d') : '') }}">
                                        @error('date_fin')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="date_limite" class="form-label">Date limite de rendu</label>
                                <input type="date" class="form-control" id="date_limite" name="date_limite" 
                                       value="{{ old('date_limite', $activity->date_limite ? $activity->date_limite->format('Y-m-d') : '') }}">
                                @error('date_limite')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Priorité -->
                        <div class="mb-4">
                            <h6 class="text-warning mb-3">Priorité</h6>
                            
                            <div class="mb-3">
                                <label for="priorite" class="form-label">Niveau de priorité *</label>
                                <select class="form-select" id="priorite" name="priorite" required>
                                    <option value="">Choisir une priorité</option>
                                    <option value="basse" {{ old('priorite', $activity->priorite) == 'basse' ? 'selected' : '' }}>Basse</option>
                                    <option value="moyenne" {{ old('priorite', $activity->priorite) == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                                    <option value="haute" {{ old('priorite', $activity->priorite) == 'haute' ? 'selected' : '' }}>Haute</option>
                                    <option value="urgente" {{ old('priorite', $activity->priorite) == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                </select>
                                @error('priorite')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Assignation -->
                        <div class="mb-4">
                            <h6 class="text-warning mb-3">Assignation</h6>
                            
                            <div class="mb-3">
                                <label for="stagiaire_id" class="form-label">Stagiaire (optionnel)</label>
                                <select class="form-select" id="stagiaire_id" name="stagiaire_id">
                                    <option value="">Ne pas assigner maintenant (activité proposée)</option>
                                    @foreach($stagiaires as $stagiaire)
                                        <option value="{{ $stagiaire->id }}" {{ old('stagiaire_id', $activity->stagiaire_id) == $stagiaire->id ? 'selected' : '' }}>
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
                            <a href="{{ route('activities.show', $activity) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour à l'activité
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo me-2"></i>Réinitialiser
                                </button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-2"></i>Mettre à jour l'activité
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Informations sur l'activité -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations sur l'activité
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Statut actuel</h6>
                    <div class="mb-3">
                        <span class="badge bg-{{ $activity->statut_color }}">
                            {{ $activity->statut_label }}
                        </span>
                    </div>
                    
                    @if($activity->stagiaire)
                        <h6 class="text-primary">Stagiaire assigné</h6>
                        <p class="mb-2">
                            <strong>{{ $activity->stagiaire->prenom }} {{ $activity->stagiaire->nom }}</strong>
                        </p>
                        <small class="text-muted">{{ $activity->stagiaire->email }}</small>
                    @endif
                    
                    <h6 class="text-primary mt-3">Dates importantes</h6>
                    <ul class="small">
                        <li><strong>Créée le:</strong> {{ $activity->created_at->format('d/m/Y') }}</li>
                        @if($activity->date_debut)
                            <li><strong>Début:</strong> {{ $activity->date_debut->format('d/m/Y') }}</li>
                        @endif
                        @if($activity->date_limite)
                            <li><strong>Limite:</strong> {{ $activity->date_limite->format('d/m/Y') }}</li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <!-- Aide -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>Aide
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Conseils pour modifier une activité</h6>
                    <ul class="small">
                        <li>Vous pouvez modifier tous les champs</li>
                        <li>Les changements seront notifiés au stagiaire</li>
                        <li>Les dates doivent respecter la chronologie</li>
                        <li>La priorité affecte l'ordre d'affichage</li>
                    </ul>
                    
                    <h6 class="text-primary mt-3">Impact des modifications</h6>
                    <ul class="small">
                        <li><strong>Titre/Description:</strong> Visible immédiatement</li>
                        <li><strong>Dates:</strong> Notifications envoyées</li>
                        <li><strong>Priorité:</strong> Changement d'ordre</li>
                        <li><strong>Stagiaire:</strong> Réassignation</li>
                    </ul>
                </div>
            </div>
            
            <!-- Actions rapides -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-2"></i>Voir l'activité
                        </a>
                        @if($activity->stagiaire)
                            <a href="#" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-envelope me-2"></i>Contacter le stagiaire
                            </a>
                        @endif
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>Supprimer l'activité
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette activité ? Cette action est irréversible.')) {
        window.location.href = "{{ route('activities.destroy', $activity) }}";
    }
}
</script>
@endsection
