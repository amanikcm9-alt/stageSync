<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion des Stages')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Navigation Styles */
        .main-navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            border-radius: 8px;
            margin: 0 0.25rem;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link.active {
            color: white !important;
            background: rgba(255,255,255,0.15);
        }

        .navbar-nav .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: white;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .navbar-nav .nav-link.active::before {
            width: 80%;
        }

        /* Dropdown Menu */
        .dropdown-menu {
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            margin-top: 0.5rem;
            padding: 0.5rem;
        }

        .dropdown-item {
            color: #333;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            color: #667eea;
            transform: translateX(5px);
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        /* User Dropdown */
        .user-dropdown .dropdown-toggle {
            color: white !important;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50px;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .user-dropdown .dropdown-toggle:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.875rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .user-avatar-img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .avatar-initials {
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-name {
            color: white;
            font-weight: 500;
            font-size: 0.875rem;
            margin-left: 0.5rem;
        }

        .user-avatar:hover,
        .user-avatar-img:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        }

        /* Main Content */
        .main-content {
            min-height: 100vh;
            background: #f8f9fa;
        }

        /* Page Header */
        .page-header {
            background: white;
            padding: 2rem 0;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
        }

        /* Content Cards */
        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            padding: 2rem;
            margin-bottom: 2rem;
            border: none;
            transition: all 0.3s ease;
        }

        .content-card:hover {
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        /* Statistics Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-icon.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
        }

        .stat-icon.info {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            color: white;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            line-height: 1;
        }

        .stat-label {
            color: #6c757d;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        /* Table Styles */
        .table-modern {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .table-modern .table {
            margin-bottom: 0;
        }

        .table-modern .table th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: none;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table-modern .table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f5;
        }

        .table-modern .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Button Styles */
        .btn-modern {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Main Content Styles */
        .main-content {
            margin-top: 0;
            padding-top: 0;
        }

        /* Force navbar to stay at top */
        .main-navbar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1030 !important;
        }

        /* Add padding to body to account for fixed navbar */
        body {
            padding-top: 56px !important;
        }

        /* Ensure main content starts after navbar */
        .main-content {
            margin-top: 0 !important;
            padding-top: 20px !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-nav .nav-link {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Principale -->
    <nav class="navbar navbar-expand-lg navbar-dark main-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/dashboard') }}">
                <i class="fas fa-graduation-cap"></i>
                <span>Gestion Stages</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                    @php 
                        $user = Auth::user()->load('role'); 
                    @endphp
                    
                    <!-- Navigation Admin -->
                    @if($user->role->name === 'admin')
                        <ul class="navbar-nav me-auto">
                            <!-- Dashboard -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ url('/admin/dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            
                            <!-- Gestion Utilisateurs -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-users me-2"></i>Utilisateurs
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.users.create') }}">
                                            <i class="fas fa-user-plus"></i>
                                            <span>Ajouter RH/Admin</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                            <i class="fas fa-list"></i>
                                            <span>Tous les Utilisateurs</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            
                                                        
                                                        
                            <!-- Entreprises -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admin/entreprises*') ? 'active' : '' }}" href="{{ route('admin.entreprises.index') }}">
                                    <i class="fas fa-building me-2"></i>Entreprises
                                </a>
                            </li>
                            
                            <!-- Paramètres -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admin/settings*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                                    <i class="fas fa-cog me-2"></i>Paramètres
                                </a>
                            </li>
                        </ul>
                    @endif
                    
                    <!-- Navigation RH -->
                    @if($user->role->name === 'rh')
                        <ul class="navbar-nav me-auto">
                            <!-- Dashboard -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('rh/dashboard') ? 'active' : '' }}" href="{{ route('rh.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            
                            <!-- Gestion Utilisateurs -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-users me-2"></i>Utilisateurs
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('rh.users.index') }}">
                                            <i class="fas fa-list"></i>
                                            <span>Liste Utilisateurs</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('rh.users.create') }}">
                                            <i class="fas fa-user-plus"></i>
                                            <span>Ajouter Encadrant / Stagiaire</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            
                            <!-- Affectations -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('rh/assignments*') ? 'active' : '' }}" href="{{ route('rh.assignments.index') }}">
                                    <i class="fas fa-link me-2"></i>Affectations
                                </a>
                            </li>
                            
                            <!-- Offres -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('rh/offres*') ? 'active' : '' }}" href="{{ route('rh.offres') }}">
                                    <i class="fas fa-briefcase me-2"></i>Offres
                                </a>
                            </li>
                            
                            <!-- Candidatures -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('rh/candidatures*') ? 'active' : '' }}" href="{{ route('rh.candidatures.index') }}">
                                    <i class="fas fa-users me-2"></i>Candidatures
                                </a>
                            </li>
                            
                            <!-- Entreprises -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('rh/entreprises*') ? 'active' : '' }}" href="{{ route('rh.entreprises.index') }}">
                                    <i class="fas fa-building me-2"></i>Entreprises
                                </a>
                            </li>
                        </ul>
                    @endif
                    
                    <!-- Navigation Encadrant -->
                    @if($user->role->name === 'encadrant')
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('encadrant/dashboard') ? 'active' : '' }}" href="{{ route('encadrant.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('encadrant/activities*') ? 'active' : '' }}" href="{{ route('encadrant.activities.index') }}">
                                    <i class="fas fa-tasks me-2"></i>Mes Activités
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('encadrant/evaluations*') ? 'active' : '' }}" href="{{ route('encadrant.evaluations.index') }}">
                                    <i class="fas fa-clipboard-check me-2"></i>Mes Évaluations
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-calendar me-2"></i>Planning
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-chart-line me-2"></i>Rapports
                                </a>
                            </li>
                        </ul>
                    @endif
                    
                    <!-- Navigation Stagiaire -->
                    @if($user->role->name === 'stagiaire')
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('stagiaire/dashboard') ? 'active' : '' }}" href="{{ route('stagiaire.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('stagiaire/activities*') ? 'active' : '' }}" href="{{ route('stagiaire.activities.index') }}">
                                    <i class="fas fa-tasks me-2"></i>Mes Activités
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('stagiaire/evaluations*') ? 'active' : '' }}" href="{{ route('stagiaire.evaluations.index') }}">
                                    <i class="fas fa-clipboard-check me-2"></i>Mes Évaluations
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#planningModal">
                                    <i class="fas fa-calendar me-2"></i>Planning
                                </a>
                            </li>
                            <li class="nav-item">
                                @if(auth()->user()->offre_stage_id)
                                    <a class="nav-link" href="{{ route('stagiaire.stage') }}">
                                        <i class="fas fa-briefcase me-2"></i>Mon Stage
                                    </a>
                                @endif
                            </li>
                        </ul>
                    @endif
                @endauth

                <!-- User Menu -->
                @auth
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown user-dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                @if($user->photo_path)
                                    @if(str_starts_with($user->photo_path, 'images/'))
                                        <img src="{{ asset($user->photo_path) }}" 
                                             alt="Photo de profil" 
                                             class="user-avatar-img"
                                             style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('storage/' . $user->photo_path) }}" 
                                             alt="Photo de profil" 
                                             class="user-avatar-img"
                                             style="width: 32px; height: 32px; object-fit: cover;">
                                    @endif
                                @else
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->prenom ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->nom ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <span class="user-name">{{ $user->prenom ?? '' }} {{ $user->nom ?? '' }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user"></i>
                                        <span>Profil</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ url('/logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item" type="submit">
                                            <i class="fas fa-sign-out-alt"></i>
                                            <span>Déconnexion</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Messages flash -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Modal Planning -->
    @if(auth()->check() && auth()->user()->role->name === 'stagiaire')
    <div class="modal fade" id="planningModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-alt text-primary"></i> Mon Planning de Stage
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="planningModalText" class="form-label">Mon planning de stage</label>
                                <textarea class="form-control" id="planningModalText" name="planning" rows="8" placeholder="Décrivez votre planning de stage hebdomadaire...&#10;Ex:&#10;Lundi: 9h-17h - Travail sur le projet X&#10;Mardi: 9h-17h - Réunion équipe + développement&#10;...">{{ auth()->user()->planning ?? '' }}</textarea>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" onclick="sauvegarderPlanningModal()">
                                <i class="fas fa-save"></i> Sauvegarder mon planning
                            </button>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-info">Conseils pour votre planning</h6>
                            <ul class="small text-muted">
                                <li>Décrivez vos horaires par jour</li>
                                <li>Mentionnez les tâches principales</li>
                                <li>Prévoyez des temps pour les réunions</li>
                                <li>Indiquez les jours de télétravail si applicable</li>
                                <li>Soyez réaliste dans vos objectifs</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function sauvegarderPlanningModal() {
        const planning = document.getElementById('planningModalText').value;
        
        fetch('/stagiaire/planning', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({planning})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Votre planning a été sauvegardé avec succès !');
                bootstrap.Modal.getInstance(document.getElementById('planningModal')).hide();
            } else {
                alert('Erreur lors de la sauvegarde du planning.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la sauvegarde du planning.');
        });
    }
    </script>
    @endif
    
    <!-- Scripts spécifiques aux pages -->
    @yield('scripts')
</body>
</html>
