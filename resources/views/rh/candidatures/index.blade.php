@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Header compact -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1 fs-4">
                <i class="fas fa-users text-success"></i> 
                Candidatures
            </h2>
            <small class="text-muted">
                {{ $candidatures->total() }} candidature{{ $candidatures->total() > 1 ? 's' : '' }}
            </small>
        </div>
    </div>

    <!-- Filtres compacts en ligne -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('rh.candidatures.index') }}" class="row g-2">
                <!-- Ligne 1: Champs principaux -->
                <div class="col-12">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="form-floating form-floating-sm">
                                <input type="text" class="form-control form-control-sm" name="search" 
                                       placeholder="Nom, email..." value="{{ request('search') }}">
                                <label for="search" class="small">Nom, email</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating form-floating-sm">
                                <select class="form-select form-select-sm" name="statut">
                                    <option value="">Statut</option>
                                    <option value="recue" {{ request('statut') == 'recue' ? 'selected' : '' }}">Reçue</option>
                                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}">En cours</option>
                                    <option value="acceptee" {{ request('statut') == 'acceptee' ? 'selected' : '' }}">Acceptée</option>
                                    <option value="refusee" {{ request('statut') == 'refusee' ? 'selected' : '' }}">Refusée</option>
                                </select>
                                <label for="statut" class="small">Statut</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating form-floating-sm">
                                <select class="form-select form-select-sm" name="archive">
                                    <option value="">Toutes</option>
                                    <option value="false" {{ request('archive') === 'false' ? 'selected' : '' }}>Actives</option>
                                    <option value="true" {{ request('archive') === 'true' ? 'selected' : '' }}>Archives</option>
                                </select>
                                <label for="archive" class="small">Archive</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating form-floating-sm">
                                <select class="form-select form-select-sm" name="offre">
                                    <option value="">Offre</option>
                                    @foreach($offres as $id => $titre)
                                        <option value="{{ $id }}" {{ request('offre') == $id ? 'selected' : '' }}>
                                            {{ Str::limit($titre, 30) }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="offre" class="small">Offre</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                                <a href="{{ route('rh.candidatures.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times"></i> Effacer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau compact -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th class="small">Candidat</th>
                        <th class="small">Offre</th>
                        <th class="small">Date</th>
                        <th class="small">Statut</th>
                        <th class="small">Documents</th>
                        <th class="small text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidatures as $candidature)
                        <tr class="small">
                            <td>
                                <div class="fw-bold">{{ $candidature->nom }} {{ $candidature->prenom }}</div>
                                <small class="text-muted">{{ $candidature->email }}</small>
                                @if($candidature->telephone)
                                    <br><small><i class="fas fa-phone"></i> {{ $candidature->telephone }}</small>
                                @endif
                            </td>
                            <td>
                                @if($candidature->offreStage)
                                    <div class="fw-bold">{{ Str::limit($candidature->offreStage->titre, 30) }}</div>
                                    <small class="text-muted">{{ $candidature->offreStage->entreprise->nom }}</small>
                                @else
                                    <div class="fw-bold text-muted">Offre supprimée</div>
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                <small>{{ $candidature->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                @switch($candidature->statut)
                                    @case('recue')
                                        <span class="badge bg-warning">Reçue</span>
                                        @break
                                    @case('en_cours')
                                        <span class="badge bg-info">En cours</span>
                                        @break
                                    @case('acceptee')
                                        <span class="badge bg-success">Acceptée</span>
                                        @break
                                    @case('refusee')
                                        <span class="badge bg-danger">Refusée</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $candidature->statut }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if($candidature->cv_path)
                                    <a href="{{ asset('storage/' . $candidature->cv_path) }}" 
                                       target="_blank" class="btn btn-outline-primary btn-sm me-1" title="CV">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                @endif
                                @if($candidature->lettre_motivation_path)
                                    <a href="{{ asset('storage/' . $candidature->lettre_motivation_path) }}" 
                                       target="_blank" class="btn btn-outline-secondary btn-sm" title="Lettre">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('rh.candidatures.show', $candidature) }}" 
                                       class="btn btn-outline-primary btn-sm" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($candidature->statut == 'recue' || $candidature->statut == 'en_cours')
                                        <form action="{{ route('rh.candidatures.accepter', $candidature) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-outline-success btn-sm" 
                                                    title="Accepter"
                                                    onclick="return confirm('Accepter cette candidature ?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('rh.candidatures.refuser', $candidature) }}" 
                                              method="POST" 
                                              onsubmit="return confirmRefuser(this, '{{ $candidature->nom }} {{ $candidature->prenom }}')">
                                            @csrf
                                            <input type="hidden" name="motif_refus" id="motif_refus_{{ $candidature->id }}">
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Refuser">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($candidature->isArchived())
                                        <form action="{{ route('rh.candidatures.unarchive', $candidature) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-outline-warning btn-sm" 
                                                    title="Restaurer"
                                                    onclick="return confirm('Restaurer cette candidature ?')">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('rh.candidatures.archive', $candidature) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-outline-secondary btn-sm" 
                                                    title="Archiver"
                                                    onclick="return confirm('Archiver cette candidature ?')">
                                                <i class="fas fa-archive"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('rh.candidatures.destroy', $candidature) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-outline-warning btn-sm" 
                                                title="Supprimer"
                                                onclick="return confirm('Supprimer la candidature de {{ $candidature->nom }} {{ $candidature->prenom }} ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                <div class="text-muted">Aucune candidature trouvée</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination compacte -->
        @if($candidatures->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Affichage {{ $candidatures->firstItem() }}-{{ $candidatures->lastItem() }} 
                        sur {{ $candidatures->total() }} résultats
                    </small>
                    {{ $candidatures->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.75rem;
    padding: 0.5rem;
}

.table td {
    padding: 0.5rem;
    vertical-align: middle;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
}

.card-body.py-2 {
    padding: 0.5rem 1rem;
}
</style>

<script>
function confirmRefuser(form, candidatNom) {
    const motif = prompt('Veuillez entrer le motif du refus pour ' + candidatNom + ' (optionnel):');
    if (motif === null) {
        return false; // L'utilisateur a annulé
    }
    
    // Mettre le motif dans le champ caché (peut être vide)
    const motifField = form.querySelector('input[name="motif_refus"]');
    motifField.value = motif ? motif.trim() : '';
    
    return true;
}
</script>
@endsection
