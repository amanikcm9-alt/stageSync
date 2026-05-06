@extends('layouts.app')

@section('title', 'Modifier un Secteur')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-industry text-primary me-2"></i>
                Modifier un Secteur
            </h4>
            <small class="text-muted">Modifier les informations du secteur : {{ $secteur->nom }}</small>
        </div>
        <div>
            <a href="{{ route('rh.secteurs.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations du Secteur</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rh.secteurs.update', $secteur) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du secteur <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom', $secteur->nom) }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Ex: Informatique, Marketing, Finance, etc.</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $secteur->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Description détaillée du secteur d'activité (optionnel)</div>
                        </div>

                        <!-- Statut -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1" 
                                       @if(old('actif', $secteur->actif)) checked @endif>
                                <label class="form-check-label" for="actif">
                                    Secteur actif
                                </label>
                            </div>
                            <div class="form-text">Un secteur actif peut être utilisé dans les offres de stage</div>
                        </div>

                        <!-- Informations d'archivage -->
                        @if($secteur->estArchive())
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Attention :</strong> Ce secteur est archivé depuis le {{ $secteur->archived_at->format('d/m/Y H:i') }}.
                                Vous pouvez le restaurer en activant le statut ci-dessus.
                            </div>
                        @endif

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rh.secteurs.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations et actions -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Informations
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Statut actuel :</dt>
                        <dd class="col-sm-8">
                            @if($secteur->estActif())
                                <span class="badge bg-success">Actif</span>
                            @elseif($secteur->estArchive())
                                <span class="badge bg-secondary">Archivé</span>
                            @else
                                <span class="badge bg-warning">Inactif</span>
                            @endif
                        </dd>
                        
                        @if($secteur->created_at)
                            <dt class="col-sm-4">Créé le :</dt>
                            <dd class="col-sm-8">{{ $secteur->created_at->format('d/m/Y H:i') }}</dd>
                        @endif
                        
                        @if($secteur->updated_at)
                            <dt class="col-sm-4">Modifié le :</dt>
                            <dd class="col-sm-8">{{ $secteur->updated_at->format('d/m/Y H:i') }}</dd>
                        @endif
                        
                        @if($secteur->archived_at)
                            <dt class="col-sm-4">Archivé le :</dt>
                            <dd class="col-sm-8">{{ $secteur->archived_at->format('d/m/Y H:i') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-tools me-1"></i>
                        Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    @if($secteur->estArchive())
                        <form action="{{ route('rh.secteurs.restore', $secteur) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Restaurer ce secteur ?')">
                                <i class="fas fa-undo me-1"></i> Restaurer
                            </button>
                        </form>
                    @else
                        <form action="{{ route('rh.secteurs.archive', $secteur) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Archiver ce secteur ?')">
                                <i class="fas fa-archive me-1"></i> Archiver
                            </button>
                        </form>
                    @endif
                    
                    @if($secteur->offres->count() == 0)
                        <form action="{{ route('rh.secteurs.destroy', $secteur) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Supprimer définitivement ce secteur ? Cette action est irréversible.')">
                                <i class="fas fa-trash me-1"></i> Supprimer
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-1"></i>
                            Ce secteur ne peut pas être supprimé car il est utilisé par {{ $secteur->offres->count() }} offre(s) de stage.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-1"></i>
                        Utilisation
                    </h6>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-primary mb-1">{{ $secteur->offres->count() }}</h3>
                    <small class="text-muted">Offre(s) de stage utilisant ce secteur</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
