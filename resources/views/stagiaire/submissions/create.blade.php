@extends('layouts.app')

@section('title', 'Soumettre un livrable')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-upload text-primary"></i> 
                Soumettre un livrable
            </h2>
            <small class="text-muted">
                {{ $activity->titre }}
            </small>
        </div>
        <div>
            <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour à l'activité
            </a>
        </div>
    </div>

    <!-- Formulaire de soumission -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-upload me-2"></i>
                Nouvelle soumission
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('submissions.store', $activity) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Informations sur l'activité -->
                <div class="alert alert-info mb-3">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>
                        Détails de l'activité
                    </h6>
                    <p class="mb-0">
                        <strong>Activité:</strong> {{ $activity->titre }}<br>
                        <strong>Encadrant:</strong> {{ $activity->encadrant->prenom }} {{ $activity->encadrant->nom }}<br>
                        @if($activity->date_limite)
                        <strong>Date limite:</strong> {{ $activity->date_limite->format('d/m/Y H:i') }}
                            @if($activity->estEnRetard())
                            <span class="badge bg-danger ms-2">En retard</span>
                            @endif
                        @endif
                    </p>
                </div>

                <!-- Commentaire -->
                <div class="mb-3">
                    <label for="commentaire" class="form-label">
                        <i class="fas fa-comment me-2"></i>
                        Commentaire <span class="text-muted">(optionnel)</span>
                    </label>
                    <textarea name="commentaire" id="commentaire" class="form-control" rows="4" 
                              placeholder="Décrivez votre travail, les difficultés rencontrées, etc.">{{ old('commentaire') }}</textarea>
                    @error('commentaire')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Fichiers -->
                <div class="mb-3">
                    <label for="fichiers" class="form-label">
                        <i class="fas fa-paperclip me-2"></i>
                        Fichiers <span class="text-muted">(optionnel)</span>
                    </label>
                    <input type="file" name="fichiers[]" id="fichiers" class="form-control" multiple 
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,.jpg,.jpeg,.png,.gif">
                    <div class="form-text">
                        Formats acceptés: PDF, Word, Excel, PowerPoint, Images, Archives (max 10MB par fichier)
                    </div>
                    @error('fichiers.*')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Soumissions précédentes -->
                @if($activity->submissions->count() > 0)
                <div class="mb-3">
                    <h6 class="text-muted mb-2">
                        <i class="fas fa-history me-2"></i>
                        Soumissions précédentes
                    </h6>
                    <div class="list-group">
                        @foreach($activity->submissions as $submission)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Soumission du {{ $submission->created_at->format('d/m/Y H:i') }}</h6>
                                    <p class="mb-1 small text-muted">
                                        @if($submission->commentaire)
                                        {{ Str::limit($submission->commentaire, 100) }}
                                        @else
                                        <em>Aucun commentaire</em>
                                        @endif
                                    </p>
                                    @if($submission->fichiers)
                                    <div class="small">
                                        <strong>Fichiers:</strong>
                                        @foreach(json_decode($submission->fichiers) as $fichier)
                                        <span class="badge bg-secondary me-1">{{ basename($fichier) }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <span class="badge bg-{{ $submission->statut_color }} text-white">
                                        {{ $submission->statut_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Boutons -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>
                        Soumettre le livrable
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Vérification des fichiers
document.getElementById('fichiers').addEventListener('change', function(e) {
    const files = e.target.files;
    const maxSize = 10 * 1024 * 1024; // 10MB
    const allowedTypes = [
        'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain', 'application/zip', 'application/x-rar-compressed',
        'image/jpeg', 'image/png', 'image/gif'
    ];
    
    let validFiles = true;
    const fileList = [];
    
    for (let file of files) {
        if (file.size > maxSize) {
            alert(`Le fichier "${file.name}" dépasse la taille maximale de 10MB.`);
            validFiles = false;
            break;
        }
        
        if (!allowedTypes.includes(file.type)) {
            alert(`Le fichier "${file.name}" n'est pas dans un format accepté.`);
            validFiles = false;
            break;
        }
        
        fileList.push(file.name);
    }
    
    if (validFiles && files.length > 0) {
        console.log('Fichiers valides:', fileList);
    }
});

// Confirmation avant soumission
document.querySelector('form').addEventListener('submit', function(e) {
    const commentaire = document.getElementById('commentaire').value.trim();
    const fichiers = document.getElementById('fichiers').files;
    
    if (!commentaire && fichiers.length === 0) {
        e.preventDefault();
        if (confirm('Vous n\'avez ajouté ni commentaire ni fichier. Voulez-vous quand même soumettre ?')) {
            e.target.submit();
        }
    }
});
</script>
@endsection
