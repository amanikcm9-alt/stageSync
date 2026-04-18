@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Paramètres de la Plateforme</h1>

    @if(session('admin_settings_updated'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Paramètres mis à jour avec succès!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.store') }}" method="POST" class="row g-3">
        @csrf

        <div class="col-12">
            <h4>Paramètres Généraux</h4>
        </div>

        <div class="col-md-6">
            <label for="site_name" class="form-label">Nom du site</label>
            <input type="text" name="site_name" id="site_name" class="form-control" 
                   value="{{ $settings['site_name'] }}" required>
        </div>

        <div class="col-md-6">
            <label for="site_email" class="form-label">Email du site</label>
            <input type="email" name="site_email" id="site_email" class="form-control" 
                   value="{{ $settings['site_email'] }}" required>
        </div>

        <div class="col-12 mt-4">
            <h4>Sécurité</h4>
        </div>

        <div class="col-md-4">
            <label for="password_min_length" class="form-label">Longueur min. mot de passe</label>
            <input type="number" name="password_min_length" id="password_min_length" class="form-control" 
                   value="{{ $settings['password_min_length'] }}" min="6" max="20" required>
        </div>

        <div class="col-md-4">
            <label for="session_timeout" class="form-label">Délai d'expiration (minutes)</label>
            <input type="number" name="session_timeout" id="session_timeout" class="form-control" 
                   value="{{ $settings['session_timeout'] }}" min="5" max="1440" required>
        </div>

        <div class="col-md-4">
            <label for="max_file_size" class="form-label">Taille max. fichier (KB)</label>
            <input type="number" name="max_file_size" id="max_file_size" class="form-control" 
                   value="{{ $settings['max_file_size'] }}" min="1024" max="10240" required>
        </div>

        <div class="col-12 mt-4">
            <h4>Authentification</h4>
        </div>

        <div class="col-md-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="allow_registration" id="allow_registration" 
                       {{ $settings['allow_registration'] ? 'checked' : '' }}>
                <label class="form-check-label" for="allow_registration">
                    Autoriser l'inscription
                </label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="email_verification" id="email_verification" 
                       {{ $settings['email_verification'] ? 'checked' : '' }}>
                <label class="form-check-label" for="email_verification">
                    Vérification email requise
                </label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="two_factor_auth" id="two_factor_auth" 
                       {{ $settings['two_factor_auth'] ? 'checked' : '' }}>
                <label class="form-check-label" for="two_factor_auth">
                    Authentification 2 facteurs
                </label>
            </div>
        </div>

        <div class="col-12 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer les paramètres
            </button>
            <a href="{{ url('/admin/dashboard') }}" class="btn btn-secondary">Retour</a>
        </div>
    </form>
</div>

<style>
/* Réduction de la taille des polices pour les settings admin */
.container {
    font-size: 0.9rem;
}

h1 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.form-label {
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.form-control {
    font-size: 0.85rem;
    padding: 0.5rem 0.75rem;
}

.form-check-label {
    font-size: 0.85rem;
    font-weight: 400;
}

.btn {
    font-size: 0.8rem;
    padding: 0.5rem 1rem;
}

.alert {
    font-size: 0.85rem;
    padding: 0.75rem 1rem;
}

.form-check {
    padding: 0.5rem 0;
}

.col-md-6, .col-md-4, .col-12 {
    padding: 0.5rem;
}

.row.g-3 {
    margin: 0;
}

.row.g-3 > * {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}
</style>
@endsection
