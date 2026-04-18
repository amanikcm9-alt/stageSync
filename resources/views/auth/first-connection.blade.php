@extends('layouts.auth')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Bienvenue Stagiaire !
                </h4>
            </div>
            <div class="card-body p-4">
                @if(session('first_connection'))
                    <div class="alert alert-success border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-3 fs-4"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Félicitations !</h6>
                                <p class="mb-0">Votre candidature a été acceptée et votre compte stagiaire a été créé.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="text-center mb-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-user-graduate fs-2 text-primary"></i>
                    </div>
                    <h5 class="text-primary">Vos Coordonnées de Connexion</h5>
                </div>

                <div class="bg-light rounded p-3 mb-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label text-muted">
                                <i class="fas fa-envelope me-1"></i> Email
                            </label>
                            <div class="form-control bg-white">
                                <strong>{{ session('user_email') ?? Auth::user()->email }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">
                                <i class="fas fa-key me-1"></i> Mot de passe
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       id="password-display" 
                                       class="form-control bg-white" 
                                       value="{{ session('user_password') ?? '•••••••••' }}" 
                                       readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                            @if(session('user_password'))
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Conservez ce mot de passe en lieu sûr
                                </small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Me Connecter Maintenant
                    </a>
                    
                    @if(!Auth::check())
                        <a href="{{ route('login') }}?reset=true" class="btn btn-outline-secondary">
                            <i class="fas fa-key me-2"></i>
                            Changer mon mot de passe
                        </a>
                    @endif
                </div>

                <div class="alert alert-info mt-4">
                    <h6 class="alert-heading">
                        <i class="fas fa-lightbulb me-2"></i>Conseils Importants
                    </h6>
                    <ul class="mb-0">
                        <li>Changez votre mot de passe lors de la première connexion</li>
                        <li>Conservez vos identifiants en sécurité</li>
                        <li>Contactez le RH en cas de problème de connexion</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById('password-display');
    const passwordIcon = document.getElementById('password-icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}
</script>
@endsection
