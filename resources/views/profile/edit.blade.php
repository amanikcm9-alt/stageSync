<!--
 * VUE REDONDANTE - NON NÉCESSAIRE
 * Raison : Fonctionnalité peut être intégrée dans chaque dashboard
 * Alternative : Chaque contrôleur peut gérer son propre profil
 * Date de mise en commentaire : 16/04/2026
 -->
@extends('layouts.app')

@section('content')
<!--
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Modifier mon profil
                    </h4>
                </div>
                <div class="card-body p-4">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Photo de profil -->
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                @if(auth()->user()->photo_path)
                                    @if(str_starts_with(auth()->user()->photo_path, 'images/'))
                                        <img src="{{ asset(auth()->user()->photo_path) }}" 
                                             alt="Photo de profil" 
                                             class="rounded-circle border border-3 border-white shadow"
                                             style="width: 120px; height: 120px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('storage/' . auth()->user()->photo_path) }}" 
                                             alt="Photo de profil" 
                                             class="rounded-circle border border-3 border-white shadow"
                                             style="width: 120px; height: 120px; object-fit: cover;">
                                    @endif
                                @else
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border border-3 border-white shadow"
                                         style="width: 120px; height: 120px;">
                                        <i class="fas fa-user fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <label for="photo" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-camera me-2"></i>
                                    Changer la photo
                                </label>
                                <input type="file" name="photo" id="photo" class="d-none" accept="image/*">
                                <small class="d-block text-muted mt-1">
                                    Formats acceptés : JPEG, PNG, JPG, GIF (max 2MB)
                                </small>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom :</label>
                                <input type="text" name="nom" id="nom" value="{{ old('nom', auth()->user()->nom) }}" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label for="prenom" class="form-label">Prénom :</label>
                                <input type="text" name="prenom" id="prenom" value="{{ old('prenom', auth()->user()->prenom) }}" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email :</label>
                                <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">Mot de passe :</label>
                                <input type="password" name="password" id="password" class="form-control">
                                <small class="form-text text-muted">Laisser vide pour ne pas changer</small>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Enregistrer les modifications
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Retour
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.querySelector('.rounded-circle');
            if (img && img.tagName === 'IMG') {
                img.src = e.target.result;
            }
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection