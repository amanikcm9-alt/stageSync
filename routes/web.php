<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminAllUsersController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\AdminAssignmentController;
use App\Http\Controllers\AdminActivityController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\OffreStageController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\RH\NotificationController;
use App\Http\Controllers\RHUserController;
use App\Http\Controllers\FirstConnectionController;
use App\Http\Controllers\TestEmailController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ActivitySubmissionController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\StagiaireDashboardController;


// AUTH
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Première connexion pour les nouveaux stagiaires
Route::get('/premiere-connexion', [FirstConnectionController::class, 'show'])->name('first.connection');
Route::post('/premiere-connexion/effacer', [FirstConnectionController::class, 'clear'])->name('first.connection.clear');

// Test email (développement)
Route::get('/test-email', [TestEmailController::class, 'sendTest'])->name('test.email');


// CRUD Admin pour gérer les rôles uniquement
Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    
    // CRUD pour les utilisateurs RH et Admin
    Route::get('users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    
    // Paramètres de la plateforme
    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [AdminSettingController::class, 'store'])->name('settings.store');
    
    // Gestion des entreprises
    Route::get('entreprises', [EntrepriseController::class, 'index'])->name('entreprises.index');
    Route::get('entreprises/create', [EntrepriseController::class, 'create'])->name('entreprises.create');
    Route::post('entreprises', [EntrepriseController::class, 'store'])->name('entreprises.store');
    Route::get('entreprises/{entreprise}', [EntrepriseController::class, 'show'])->name('entreprises.show');
    Route::get('entreprises/{entreprise}/edit', [EntrepriseController::class, 'edit'])->name('entreprises.edit');
    Route::put('entreprises/{entreprise}', [EntrepriseController::class, 'update'])->name('entreprises.update');
    Route::delete('entreprises/{entreprise}', [EntrepriseController::class, 'destroy'])->name('entreprises.destroy');
    Route::post('entreprises/{entreprise}/activer', [EntrepriseController::class, 'activer'])->name('entreprises.activer');
    Route::get('entreprises/{entreprise}/reglement', [EntrepriseController::class, 'showReglement'])->name('entreprises.reglement');
    Route::post('entreprises/{entreprise}/desactiver', [EntrepriseController::class, 'desactiver'])->name('entreprises.desactiver');
});

// Dashboard principal - Statistiques (redirigé selon le rôle)
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = auth()->user();
    
    // Redirection selon le rôle
    switch($user->role->name) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'rh':
            return redirect()->route('rh.dashboard');
        case 'encadrant':
            return redirect()->route('encadrant.dashboard');
        case 'stagiaire':
            return redirect()->route('stagiaire.dashboard');
        default:
            return redirect('/login');
    }
})->name('dashboard');

// Dashboard Admin
Route::middleware(['auth', 'role:admin'])->get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// Dashboard RH
Route::middleware(['auth', 'role:rh'])->get('/rh/dashboard', function () {
    return view('rh.dashboard');
})->name('rh.dashboard');







// Dashboard Encadrant/Stagiaire
Route::get('/encadrant/dashboard', function(){ 
    $user = auth()->user();
    
    // Activités créées par l'encadrant
    $activities = \App\Models\Activity::where('encadrant_id', $user->id)
        ->with(['stagiaire', 'submissions', 'documents'])
        ->latest()
        ->get();

    // Stagiaires suivis
    $stagiaires = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'stagiaire'))
        ->where(function($query) use ($user) {
            $query->where('encadrant_id', $user->id)
                  ->orWhere('encadrant_faculte_id', $user->id)
                  ->orWhere('encadrant_entreprise_id', $user->id);
        })
        ->with(['activities' => fn($q) => $q->where('encadrant_id', $user->id)])
        ->get();

    // Évaluations créées par l'encadrant
    $evaluations = \App\Models\Evaluation::where('evaluateur_id', $user->id)
        ->with(['stagiaire', 'activity'])
        ->latest()
        ->get();

    // Documents publiés par l'encadrant
    $documents = \App\Models\Document::where('uploaded_by', $user->id)
        ->with(['activity'])
        ->latest()
        ->get();

    // Statistiques
    $stats = [
        'total_activities' => $activities->count(),
        'en_cours' => $activities->where('statut', 'en_cours')->count(),
        'soumises' => $activities->where('statut', 'soumise')->count(),
        'validees' => $activities->where('statut', 'validee')->count(),
        'total_stagiaires' => $stagiaires->count(),
        'total_evaluations' => $evaluations->count(),
        'evaluations_validees' => $evaluations->where('statut', 'validee')->count(),
        'total_documents' => $documents->count(),
    ];
    
    return view('encadrant.activities.dashboard', compact('activities', 'stagiaires', 'evaluations', 'documents', 'stats')); 
})->middleware('role:encadrant')->name('encadrant.dashboard');

Route::get('/stagiaire/dashboard', function(){ 
    $user = auth()->user();
    
    // Activités assignées au stagiaire
    $activities = \App\Models\Activity::where('stagiaire_id', $user->id)
        ->with(['encadrant', 'submissions', 'documents'])
        ->latest()
        ->get();

    // Activités proposées par les encadrants
    $proposedActivities = \App\Models\Activity::whereNull('stagiaire_id')
        ->where(function($query) use ($user) {
            $query->where('encadrant_id', $user->encadrant_id);
            if ($user->encadrant_faculte_id) {
                $query->orWhere('encadrant_id', $user->encadrant_faculte_id);
            }
            if ($user->encadrant_entreprise_id) {
                $query->orWhere('encadrant_id', $user->encadrant_entreprise_id);
            }
        })
        ->with(['encadrant'])
        ->latest()
        ->get();

    // Informations du stage
    $stage = null;
    if ($user->offre_stage_id) {
        $stage = \App\Models\OffreStage::with(['entreprise', 'candidatures' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->find($user->offre_stage_id);
    }

    // Charger les informations de l'encadrant avec les relations
    $user->load(['encadrant', 'encadrant_faculte', 'encadrant_entreprise']);

    // Évaluations du stagiaire
    $evaluations = \App\Models\Evaluation::where('stagiaire_id', $user->id)
        ->with(['encadrant', 'activity'])
        ->latest()
        ->get();

    // Statistiques
    $stats = [
        'total' => $activities->count(),
        'en_cours' => $activities->where('statut', 'en_cours')->count(),
        'soumises' => $activities->where('statut', 'soumise')->count(),
        'validees' => $activities->where('statut', 'validee')->count(),
        'en_retard' => $activities->filter(fn($a) => $a->estEnRetard())->count(),
        'proposed' => $proposedActivities->count(),
        'evaluations' => $evaluations->count(),
    ];

    // Documents et supports
    $documents = \App\Models\Document::where(function($query) use ($user) {
            $query->whereHas('activity', function($q) use ($user) {
                $q->where('offre_stage_id', $user->offre_stage_id);
            })
            ->orWhere('type', 'reglement');
        })
        ->publies()
        ->latest()
        ->get();

    // Notifications non lues
    $notifications = [];
    try {
        $notifications = \App\Models\Notification::where('destinataire_id', $user->id)
            ->whereNull('date_lecture')
            ->with(['sender'])
            ->latest()
            ->get();
    } catch (\Exception $e) {
        // En cas d'erreur, on retourne un tableau vide
        $notifications = [];
    }
    
    return view('stagiaire.activities.dashboard', compact('activities', 'proposedActivities', 'stage', 'evaluations', 'stats', 'documents', 'notifications')); 
})->middleware('role:stagiaire')->name('stagiaire.dashboard');

// Route pour sauvegarder le planning du stagiaire
Route::post('/stagiaire/planning', function(Request $request) {
    $user = auth()->user();
    $user->planning = $request->input('planning');
    $user->save();
    
    return response()->json(['success' => true, 'message' => 'Planning sauvegardé avec succès']);
})->middleware('auth')->name('stagiaire.planning');

// Route pour afficher la page Mon Stage du stagiaire
Route::get('/stagiaire/stage', function(Request $request) {
    $user = auth()->user();
    
    // Charger les informations de l'encadrant avec les relations
    $user->load(['encadrant', 'encadrant_faculte', 'encadrant_entreprise']);
    
    return view('stagiaire.stage.show', compact('user'));
})->middleware('role:stagiaire')->name('stagiaire.stage');



// Demander un lien de reset (formulaire email)
Route::get('/forgot-password', function () {
    return view('auth.passwords.email');
})->name('password.request');

// Envoyer le lien par email
Route::post('/password/email', [AuthController::class, 'sendResetLink'])->name('password.email');

// Formulaire de reset avec TOKEN (IMPORTANT)
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');

// Enregistrer le nouveau mot de passe
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');
Route::post('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password.update')->middleware('auth');

// Modifier profil pour tous les utilisateurs
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

// Routes publiques (accessibles à tous)
Route::get('/', [PublicController::class, 'accueil'])->name('accueil');
Route::get('/offres', [PublicController::class, 'offres'])->name('offres');
Route::get('/offres/{offre}', [PublicController::class, 'showOffre'])->name('offres.show');
Route::get('/offres/{offre}/details', [PublicController::class, 'getOffreDetails'])->name('offres.details');
Route::get('/offres/disponibles', [PublicController::class, 'getOffresDisponibles'])->name('offres.disponibles');
Route::get('/entreprises', [PublicController::class, 'entreprises'])->name('entreprises');
Route::get('/entreprises/{entreprise}', [PublicController::class, 'showEntreprise'])->name('entreprises.show');
Route::get('/apropos', [PublicController::class, 'apropos'])->name('apropos');

// Routes candidatures (publiques)
Route::prefix('candidature')->name('candidatures.')->group(function () {
    Route::get('/{offre}', [PublicController::class, 'formCandidature'])->name('create');
    Route::post('/{offre}', [PublicController::class, 'soumettreCandidature'])->name('store');
});

// Espace candidat
Route::prefix('candidat')->name('candidat.')->group(function () {
    Route::get('/dashboard', [CandidatController::class, 'dashboard'])->name('dashboard');
    Route::get('/candidature/{id}', [CandidatController::class, 'candidature'])->name('candidature');
    Route::get('/notifications', [CandidatController::class, 'notifications'])->name('notifications');
});

// ROUTES ADMIN - Gestion des offres et candidatures (Admin uniquement)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Gestion des offres de stage
    Route::get('offres', [OffreStageController::class, 'index'])->name('offres');
    Route::get('offres/create', [OffreStageController::class, 'create'])->name('offres.create');
    Route::post('offres', [OffreStageController::class, 'store'])->name('offres.store');
    Route::get('offres/{offre}', [OffreStageController::class, 'show'])->name('offres.show');
    Route::get('offres/{offre}/edit', [OffreStageController::class, 'edit'])->name('offres.edit');
    Route::put('offres/{offre}', [OffreStageController::class, 'update'])->name('offres.update');
    Route::delete('offres/{offre}', [OffreStageController::class, 'destroy'])->name('offres.destroy');
    
    // Actions sur les offres
    Route::post('offres/{offre}/publier', [OffreStageController::class, 'publier'])->name('offres.publier');
    Route::post('offres/{offre}/cloturer', [OffreStageController::class, 'cloturer'])->name('offres.cloturer');
    
    // Gestion des candidatures
    Route::get('candidatures', [CandidatureController::class, 'index'])->name('candidatures.index');
    Route::get('candidatures/{candidature}', [CandidatureController::class, 'show'])->name('candidatures.show');
    Route::post('candidatures/{candidature}/accepter', [CandidatureController::class, 'accepter'])->name('candidatures.accepter');
    Route::post('candidatures/{candidature}/refuser', [CandidatureController::class, 'refuser'])->name('candidatures.refuser');
    Route::post('candidatures/{candidature}/entretien', [CandidatureController::class, 'planifierEntretien'])->name('candidatures.entretien');
    Route::post('candidatures/{candidature}/archive', [CandidatureController::class, 'archive'])->name('candidatures.archive');
    Route::post('candidatures/{candidature}/unarchive', [CandidatureController::class, 'unarchive'])->name('candidatures.unarchive');
    Route::delete('candidatures/{candidature}', [CandidatureController::class, 'destroy'])->name('candidatures.destroy');
    
   // *************************
    // Gestion des entreprises
    Route::get('entreprises', [EntrepriseController::class, 'index'])->name('entreprises.index');
    Route::get('entreprises/create', [EntrepriseController::class, 'create'])->name('entreprises.create');
    Route::post('entreprises', [EntrepriseController::class, 'store'])->name('entreprises.store');
    Route::get('entreprises/{entreprise}', [EntrepriseController::class, 'show'])->name('entreprises.show');
    Route::get('entreprises/{entreprise}/edit', [EntrepriseController::class, 'edit'])->name('entreprises.edit');
    Route::put('entreprises/{entreprise}', [EntrepriseController::class, 'update'])->name('entreprises.update');
    Route::delete('entreprises/{entreprise}', [EntrepriseController::class, 'destroy'])->name('entreprises.destroy');
    Route::post('entreprises/{entreprise}/activer', [EntrepriseController::class, 'activer'])->name('entreprises.activer');
    Route::get('entreprises/{entreprise}/reglement', [EntrepriseController::class, 'showReglement'])->name('entreprises.reglement');
    Route::post('entreprises/{entreprise}/desactiver', [EntrepriseController::class, 'desactiver'])->name('entreprises.desactiver');
    //************************
    // Gestion des secteurs (admin)
    Route::post('secteurs', function(\Illuminate\Http\Request $request) {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:secteurs,nom',
            'description' => 'nullable|string|max:1000',
            'lieu' => 'nullable|string|max:255',
            'actif' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ]);
            }
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Debug: afficher les données reçues pour création secteur admin
        \Log::info('Données reçues pour création secteur admin:', [
            'nom' => $request->nom,
            'description' => $request->description,
            'lieu' => $request->lieu,
            'actif' => $request->boolean('actif', true),
            'all_data' => $request->all()
        ]);

        $secteur = \App\Models\Secteur::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'lieu' => $request->lieu,
            'actif' => $request->boolean('actif', true)
        ]);

        // Si c'est une requête AJAX, retourner JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Secteur créé avec succès.',
                'secteur' => $secteur
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Secteur créé avec succès.');
    })->name('secteurs.store');

    Route::put('secteurs/{secteur}', function(\Illuminate\Http\Request $request, \App\Models\Secteur $secteur) {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:secteurs,nom,' . $secteur->id,
            'description' => 'nullable|string|max:1000',
            'lieu' => 'nullable|string|max:255',
            'actif' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ]);
            }
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Debug: afficher les données reçues pour mise à jour secteur admin
        \Log::info('Données reçues pour mise à jour secteur admin:', [
            'nom' => $request->nom,
            'description' => $request->description,
            'lieu' => $request->lieu,
            'actif' => $request->boolean('actif', true),
            'all_data' => $request->all()
        ]);

        $secteur->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'lieu' => $request->lieu,
            'actif' => $request->boolean('actif', true)
        ]);

        // Si c'est une requête AJAX, retourner JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Secteur mis à jour avec succès.',
                'secteur' => $secteur
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Secteur mis à jour avec succès.');
    })->name('secteurs.update');

    Route::delete('secteurs/{secteur}', function(\App\Models\Secteur $secteur) {
        // Vérifier si des offres utilisent ce secteur
        if ($secteur->offres()->count() > 0) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer ce secteur car il est utilisé par des offres de stage.'
                ]);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Impossible de supprimer ce secteur car il est utilisé par des offres de stage.');
        }

        $secteur->delete();
        
        // Si c'est une requête AJAX, retourner JSON
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Secteur supprimé avec succès.'
            ]);
        }
        
        return redirect()
            ->back()
            ->with('success', 'Secteur supprimé avec succès.');
    })->name('secteurs.destroy');

    
    // Gestion des types de stage (admin)
    Route::post('type-stages', function(\Illuminate\Http\Request $request) {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:type_stages,nom',
            'code' => 'nullable|string|max:10|unique:type_stages,code,NULL,id',
            'description' => 'nullable|string|max:1000',
            'lieu' => 'nullable|string|max:255',
            'actif' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ]);
            }
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $typeStage = \App\Models\TypeStage::create([
            'nom' => $request->nom,
            'code' => $request->code,
            'description' => $request->description,
            'actif' => $request->boolean('actif', true)
        ]);

        // Si c'est une requête AJAX, retourner JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Type de stage créé avec succès.',
                'type_stage' => $typeStage
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Type de stage créé avec succès.');
    })->name('type-stages.store');

    Route::put('type-stages/{typeStage}', function(\Illuminate\Http\Request $request, \App\Models\TypeStage $typeStage) {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:type_stages,nom,' . $typeStage->id,
            'code' => 'nullable|string|max:10|unique:type_stages,code,' . $typeStage->id . ',id',
            'description' => 'nullable|string|max:1000',
            'lieu' => 'nullable|string|max:255',
            'actif' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ]);
            }
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $typeStage->update([
            'nom' => $request->nom,
            'code' => $request->code,
            'description' => $request->description,
            'actif' => $request->boolean('actif', true)
        ]);

        // Si c'est une requête AJAX, retourner JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Type de stage mis à jour avec succès.',
                'type_stage' => $typeStage
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Type de stage mis à jour avec succès.');
    })->name('type-stages.update');

    Route::delete('type-stages/{typeStage}', function(\App\Models\TypeStage $typeStage) {
        // Vérifier si des offres utilisent ce type de stage
        if ($typeStage->offres()->count() > 0) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer ce type de stage car il est utilisé par des offres de stage.'
                ]);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Impossible de supprimer ce type de stage car il est utilisé par des offres de stage.');
        }

        $typeStage->delete();
        
        // Si c'est une requête AJAX, retourner JSON
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Type de stage supprimé avec succès.'
            ]);
        }
        
        return redirect()
            ->back()
            ->with('success', 'Type de stage supprimé avec succès.');
    })->name('type-stages.destroy');
});

// ROUTES RH - Gestion des offres et candidatures (RH uniquement)
Route::middleware(['auth', 'role:rh'])->prefix('rh')->name('rh.')->group(function () {
    // Gestion des stagiaires et encadrants uniquement
    Route::get('users', [RHUserController::class, 'index'])->name('users.index');
    Route::get('users/create', [RHUserController::class, 'create'])->name('users.create');
    Route::post('users', [RHUserController::class, 'store'])->name('users.store');
    Route::get('users/{user}', [RHUserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [RHUserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [RHUserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [RHUserController::class, 'destroy'])->name('users.destroy');
    Route::post('users/{user}/activate', [RHUserController::class, 'activate'])->name('users.activate');
    Route::post('users/{user}/deactivate', [RHUserController::class, 'deactivate'])->name('users.deactivate');
    
    // Gestion des affectations
    Route::get('assignments', [RHUserController::class, 'assignmentsIndex'])->name('assignments.index');
    Route::get('assignments/create', [RHUserController::class, 'assignmentsCreate'])->name('assignments.create');
    Route::post('assignments', [RHUserController::class, 'assignmentsStore'])->name('assignments.store');
    Route::get('assignments/{assignment}/edit', [RHUserController::class, 'assignmentsEdit'])->name('assignments.edit');
    Route::put('assignments/{assignment}', [RHUserController::class, 'assignmentsUpdate'])->name('assignments.update');
    Route::delete('assignments/{assignment}', [RHUserController::class, 'assignmentsDestroy'])->name('assignments.destroy');
    
        
    // Gestion des offres de stage
    Route::get('offres', [OffreStageController::class, 'index'])->name('offres.index');
    Route::get('offres', [OffreStageController::class, 'index'])->name('offres'); // Alias pour rétrocompatibilité
    Route::get('offres/create', [OffreStageController::class, 'create'])->name('offres.create');
    Route::post('offres', [OffreStageController::class, 'store'])->name('offres.store');
    Route::get('offres/{offre}', [OffreStageController::class, 'show'])->name('offres.show');
    Route::get('offres/{offre}/edit', [OffreStageController::class, 'edit'])->name('offres.edit');
    Route::put('offres/{offre}', [OffreStageController::class, 'update'])->name('offres.update');
    Route::delete('offres/{offre}', [OffreStageController::class, 'destroy'])->name('offres.destroy');
    
    // Actions sur les offres
    Route::post('offres/{offre}/publier', [OffreStageController::class, 'publier'])->name('offres.publier');
    Route::post('offres/{offre}/cloturer', [OffreStageController::class, 'cloturer'])->name('offres.cloturer');
    
    // Gestion des candidatures
    Route::get('candidatures', [CandidatureController::class, 'index'])->name('candidatures.index');
    Route::get('candidatures/{candidature}', [CandidatureController::class, 'show'])->name('candidatures.show');
    Route::post('candidatures/{candidature}/accepter', [CandidatureController::class, 'accepter'])->name('candidatures.accepter');
    Route::post('candidatures/{candidature}/refuser', [CandidatureController::class, 'refuser'])->name('candidatures.refuser');
    Route::post('candidatures/{candidature}/entretien', [CandidatureController::class, 'planifierEntretien'])->name('candidatures.entretien');
    Route::post('candidatures/{candidature}/archive', [CandidatureController::class, 'archive'])->name('candidatures.archive');
    Route::post('candidatures/{candidature}/unarchive', [CandidatureController::class, 'unarchive'])->name('candidatures.unarchive');
    Route::delete('candidatures/{candidature}', [CandidatureController::class, 'destroy'])->name('candidatures.destroy');
    // Gestion des entreprises (lecture seule pour RH)
    Route::get('entreprises', [EntrepriseController::class, 'index'])->name('entreprises.index');
    Route::get('entreprises/{entreprise}', [EntrepriseController::class, 'show'])->name('entreprises.show');
    
    // Gestion des affectations (RH) - Interface intelligente
    Route::get('affectation', [\App\Http\Controllers\RHAssignmentController::class, 'index'])->name('rh.affectation.index');
    Route::get('affectation/{stagiaireId}/encadrants', [\App\Http\Controllers\RHAssignmentController::class, 'showEncadrants'])->name('rh.affectation.encadrants');
    Route::post('affectation/{stagiaireId}/assign', [\App\Http\Controllers\RHAssignmentController::class, 'assign'])->name('rh.affectation.assign');
    Route::get('api/affectation/{stagiaireId}/encadrants', [\App\Http\Controllers\RHAssignmentController::class, 'getEncadrantsForStagiaire'])->name('api.affectation.encadrants');
    
    // Gestion des entretiens
    Route::get('entretiens', [\App\Http\Controllers\EntretienController::class, 'index'])->name('entretiens.index');
    Route::get('entretiens/{entretien}', [\App\Http\Controllers\EntretienController::class, 'show'])->name('entretiens.show');
    Route::post('entretiens/{entretien}/evaluer', [\App\Http\Controllers\EntretienController::class, 'evaluer'])->name('entretiens.evaluer');

    // Anciennes routes (maintenues pour compatibilité)
    Route::get('affectations', [\App\Http\Controllers\AdminAssignmentController::class, 'index'])->name('affectations.index');
    Route::get('affectations/{id}', [\App\Http\Controllers\AdminAssignmentController::class, 'show'])->name('affectations.show');
    Route::get('affectations/{id}/edit', [\App\Http\Controllers\AdminAssignmentController::class, 'edit'])->name('affectations.edit');
    Route::post('affectations', [\App\Http\Controllers\AdminAssignmentController::class, 'store'])->name('affectations.store');
    Route::put('affectations/{id}', [\App\Http\Controllers\AdminAssignmentController::class, 'update'])->name('affectations.update');
    Route::delete('affectations/{id}', [\App\Http\Controllers\AdminAssignmentController::class, 'destroy'])->name('affectations.destroy');
    Route::post('affectations/bulk', [\App\Http\Controllers\AdminAssignmentController::class, 'bulkAssign'])->name('affectations.bulk');

    // Gestion des secteurs
    Route::get('secteurs', function() {
        $secteurs = \App\Models\Secteur::withCount('offres')
            ->orderBy('nom')
            ->get();
        
        return view('rh.secteurs.index', compact('secteurs'));
    })->name('secteurs.index');
    
    // API pour récupérer les lieux des secteurs
    Route::get('api/secteurs/{secteurId}/lieu', function($secteurId) {
        $secteur = \App\Models\Secteur::find($secteurId);
        
        if ($secteur) {
            return response()->json([
                'success' => true,
                'lieu' => $secteur->lieu
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Secteur non trouvé'
        ]);
    });
    
    Route::get('secteurs/create', function() {
        return view('rh.secteurs.create');
    })->name('secteurs.create');
    
    Route::post('secteurs', function(\Illuminate\Http\Request $request) {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:secteurs,nom',
            'description' => 'nullable|string|max:1000',
            'lieu' => 'nullable|string|max:255',
            'actif' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $secteur = \App\Models\Secteur::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'lieu' => $request->lieu,
            'actif' => $request->boolean('actif', true)
        ]);

        return redirect()
            ->route('rh.secteurs.index')
            ->with('success', 'Secteur créé avec succès.');
    })->name('secteurs.store');
    
    Route::get('secteurs/{secteur}/edit', function(\App\Models\Secteur $secteur) {
        return view('rh.secteurs.edit', compact('secteur'));
    })->name('secteurs.edit');
    
    Route::put('secteurs/{secteur}', function(\Illuminate\Http\Request $request, \App\Models\Secteur $secteur) {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:secteurs,nom,' . $secteur->id,
            'description' => 'nullable|string|max:1000',
            'lieu' => 'nullable|string|max:255',
            'actif' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $secteur->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'lieu' => $request->lieu,
            'actif' => $request->boolean('actif', true)
        ]);

        return redirect()
            ->route('rh.secteurs.index')
            ->with('success', 'Secteur mis à jour avec succès.');
    })->name('secteurs.update');
    
    Route::post('secteurs/{secteur}/archive', function(\App\Models\Secteur $secteur) {
        $secteur->archiver();
        
        return redirect()
            ->route('rh.secteurs.index')
            ->with('success', 'Secteur archivé avec succès.');
    })->name('secteurs.archive');
    
    Route::post('secteurs/{secteur}/restore', function(\App\Models\Secteur $secteur) {
        $secteur->desarchiver();
        
        return redirect()
            ->route('rh.secteurs.index')
            ->with('success', 'Secteur restauré avec succès.');
    })->name('secteurs.restore');
    
    Route::delete('secteurs/{secteur}', function(\App\Models\Secteur $secteur) {
        // Vérifier si des offres utilisent ce secteur
        if ($secteur->offres()->count() > 0) {
            return redirect()
                ->route('rh.secteurs.index')
                ->with('error', 'Impossible de supprimer ce secteur car il est utilisé par des offres de stage.');
        }

        $secteur->delete();
        
        return redirect()
            ->route('rh.secteurs.index')
            ->with('success', 'Secteur supprimé avec succès.');
    })->name('secteurs.destroy');
    
    // Gestion des types de stage
    Route::get('type-stages', [TypeStageController::class, 'index'])->name('type-stages.index');
    Route::get('type-stages/create', [TypeStageController::class, 'create'])->name('type-stages.create');
    Route::post('type-stages', [TypeStageController::class, 'store'])->name('type-stages.store');
    Route::get('type-stages/{typeStage}/edit', [TypeStageController::class, 'edit'])->name('type-stages.edit');
    Route::put('type-stages/{typeStage}', [TypeStageController::class, 'update'])->name('type-stages.update');
    Route::post('type-stages/{typeStage}/archive', [TypeStageController::class, 'archive'])->name('type-stages.archive');
    Route::post('type-stages/{typeStage}/restore', [TypeStageController::class, 'restore'])->name('type-stages.restore');
    Route::delete('type-stages/{typeStage}', [TypeStageController::class, 'destroy'])->name('type-stages.destroy');
    
    // Notifications SMS
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\RH\NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/renvoyer', [\App\Http\Controllers\RH\NotificationController::class, 'renvoyer'])->name('renvoyer');
    });
});

// Route directe pour la proposition d'activité (stagiaires)
Route::middleware(['auth'])->get('/activities/propose', [ActivityController::class, 'proposeForm'])->name('activities.propose');

// Sprint 3 - Activités et suivi
Route::middleware(['auth'])->prefix('activities')->name('activities.')->group(function () {
    Route::get('/', [ActivityController::class, 'index'])->name('index');
    Route::get('/create', [ActivityController::class, 'create'])->name('create');
    Route::post('/', [ActivityController::class, 'store'])->name('store');
    Route::get('/{activity}', [ActivityController::class, 'show'])->name('show');
    Route::get('/{activity}/edit', [ActivityController::class, 'edit'])->name('edit');
    Route::put('/{activity}', [ActivityController::class, 'update'])->name('update');
    Route::delete('/{activity}', [ActivityController::class, 'destroy'])->name('destroy');
    
    // Actions stagiaire
    Route::post('/{activity}/realiser', [ActivityController::class, 'realiser'])->name('realiser');
    Route::post('/{activity}/refuser', [ActivityController::class, 'refuser'])->name('refuser');
    Route::post('/{activity}/demander-info', [ActivityController::class, 'demanderInformation'])->name('demander-info');
    Route::post('/proposer', [ActivityController::class, 'proposerActivite'])->name('proposer');
    Route::post('/{activity}/progression', [ActivityController::class, 'mettreAJourProgression'])->name('progression');
    
    // Actions encadrant
    Route::post('/{activity}/valider', [ActivityController::class, 'validerActivite'])->name('valider');
    Route::post('/{activity}/assigner', [ActivityController::class, 'assignerStagiaire'])->name('assigner');
    Route::post('/{activity}/evaluer', [ActivityController::class, 'evaluerActivite'])->name('evaluer');
    
    // API pour les discussions
    Route::get('/{activity}/json', [ActivityController::class, 'getActivityJson'])->name('json');
    Route::get('/{activity}/supports', [DocumentController::class, 'supportsActivite'])->name('supports');
    
    // Discussions
    Route::get('/{activity}/discussions', [ActivityController::class, 'getDiscussions'])->name('discussions.index');
    Route::post('/{activity}/discussions', [ActivityController::class, 'sendDiscussion'])->name('discussions.store');
    Route::post('/{activity}/discussions/read', [ActivityController::class, 'markDiscussionsAsRead'])->name('discussions.read');
    
    // Discussions générales
    Route::get('/discussions/{user}', [ActivityController::class, 'getGeneralDiscussions'])->name('discussions.general.index');
    Route::post('/discussions/{user}', [ActivityController::class, 'sendGeneralDiscussion'])->name('discussions.general.store');
    Route::post('/discussions/{user}/read', [ActivityController::class, 'markGeneralDiscussionsAsRead'])->name('discussions.general.read');
    
    // Route de secours pour les discussions
    Route::post('/simple-discussion/{user}', [ActivityController::class, 'sendSimpleDiscussion'])->name('discussions.simple.store');
    
    // Route pour marquer une notification spécifique comme lue
    Route::post('/discussions/mark-read/{discussion}', [ActivityController::class, 'markNotificationAsRead'])->name('discussions.mark-read');
});

// Actions des stagiaires sur les activités
Route::middleware(['auth', 'role:stagiaire'])->prefix('stagiaire')->name('stagiaire.')->group(function () {
    Route::get('/dashboard', [StagiaireDashboardController::class, 'index'])->name('dashboard');
    Route::get('/activities', [StagiaireDashboardController::class, 'activities'])->name('activities.index');
    Route::get('/activities/propose', [StagiaireDashboardController::class, 'proposeActivity'])->name('activities.propose');
    Route::post('/activities/propose', [StagiaireDashboardController::class, 'submitProposal'])->name('activities.submit');
    Route::get('/evaluations', [ActivityController::class, 'mesEvaluations'])->name('evaluations.index');
    
    // Routes pour les évaluations du stagiaire
    Route::get('/evaluations/organisation/create', [EvaluationController::class, 'createOrganisation'])->name('evaluations.organisation.create');
    Route::get('/evaluations/encadrant/create', [EvaluationController::class, 'createEncadrant'])->name('evaluations.encadrant.create');
    Route::get('/evaluations/auto/create', [EvaluationController::class, 'createAuto'])->name('evaluations.auto.create');
});

// Actions des stagiaires sur les activités (routes POST)
Route::middleware(['auth', 'role:stagiaire'])->prefix('activities')->name('activities.')->group(function () {
    Route::post('/{activity}/accepter', [ActivityController::class, 'accepter'])->name('accepter');
    Route::post('/{activity}/refuser', [StagiaireDashboardController::class, 'refuseActivity'])->name('refuser');
    Route::post('/{activity}/demander-info', [StagiaireDashboardController::class, 'requestInfo'])->name('demander-info');
    Route::post('/{activity}/soumettre-livrable', [StagiaireDashboardController::class, 'submitDeliverable'])->name('soumettre-livrable');
    Route::post('/{activity}/discuter', [ActivityController::class, 'discuter'])->name('discuter');
});

// Routes pour la gestion des discussions
Route::middleware(['auth'])->prefix('discussions')->name('discussions.')->group(function () {
    Route::get('/{activity}/messages', [ActivityController::class, 'getDiscussions'])->name('messages');
    Route::post('/{message}/edit', [ActivityController::class, 'editMessage'])->name('edit');
    Route::delete('/{message}/delete', [ActivityController::class, 'deleteMessage'])->name('delete');
});

// Actions des encadrants sur les activités
Route::middleware(['auth'])->prefix('encadrant')->name('encadrant.')->group(function () {
    Route::get('/dashboard', [ActivityController::class, 'dashboard'])->name('dashboard');
    Route::get('/activities', [ActivityController::class, 'mesActivitesEncadrant'])->name('activities.index');
    Route::get('/evaluations', [ActivityController::class, 'mesEvaluationsEncadrant'])->name('evaluations.index');
    
    // Routes pour les évaluations de l'encadrant
    Route::get('/evaluations/create', [EvaluationController::class, 'createStagiaire'])->name('evaluations.create');
    Route::get('/evaluations/auto', [EvaluationController::class, 'indexAutoEvaluations'])->name('evaluations.auto.index');
});

// Soumissions d'activités
Route::middleware(['auth'])->prefix('submissions')->name('submissions.')->group(function () {
    Route::get('/create/{activity}', [ActivitySubmissionController::class, 'create'])->name('create');
    Route::post('/{activity}', [ActivitySubmissionController::class, 'store'])->name('store');
    Route::get('/{submission}', [ActivitySubmissionController::class, 'show'])->name('show');
    Route::get('/{submission}/edit', [ActivitySubmissionController::class, 'edit'])->name('edit');
    Route::put('/{submission}', [ActivitySubmissionController::class, 'update'])->name('update');
    Route::delete('/{submission}', [ActivitySubmissionController::class, 'destroy'])->name('destroy');
    
    // Fichiers
    Route::get('/{submission}/fichier/{index}', [ActivitySubmissionController::class, 'telechargerFichier'])->name('telecharger');
    Route::delete('/{submission}/fichier/{index}', [ActivitySubmissionController::class, 'supprimerFichier'])->name('supprimer-fichier');
    
    // Évaluation
    Route::post('/{submission}/mettre-en-evaluation', [ActivitySubmissionController::class, 'mettreEnEvaluation'])->name('mettre-en-evaluation');
    Route::post('/{submission}/valider', [ActivitySubmissionController::class, 'valider'])->name('valider');
    Route::post('/{submission}/refuser', [ActivitySubmissionController::class, 'refuser'])->name('refuser');
});

// Documents et supports
Route::middleware(['auth'])->prefix('documents')->name('documents.')->group(function () {
    Route::get('/', [DocumentController::class, 'index'])->name('index');
    Route::get('/create', [DocumentController::class, 'create'])->name('create');
    Route::post('/', [DocumentController::class, 'store'])->name('store');
    Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
    Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('edit');
    Route::put('/{document}', [DocumentController::class, 'update'])->name('update');
    Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
    
    // Actions
    Route::get('/{document}/telecharger', [DocumentController::class, 'telecharger'])->name('telecharger');
    Route::get('/reglements', [DocumentController::class, 'reglements'])->name('reglements');
    Route::get('/reglements/create', [DocumentController::class, 'createReglement'])->name('reglements.create');
    Route::post('/reglements', [DocumentController::class, 'storeReglement'])->name('reglements.store');
    
    // API
    Route::get('/{document}/check-lu', [DocumentController::class, 'checkLu'])->name('check-lu');
    Route::post('/{document}/marquer-lu', [DocumentController::class, 'marquerLu'])->name('marquer-lu');
});

// Évaluations
Route::middleware(['auth'])->prefix('evaluations')->name('evaluations.')->group(function () {
    Route::get('/', [EvaluationController::class, 'index'])->name('index');
    Route::get('/create', [EvaluationController::class, 'create'])->name('create');
    Route::post('/', [EvaluationController::class, 'store'])->name('store');
    Route::get('/{evaluation}', [EvaluationController::class, 'show'])->name('show');
    Route::get('/{evaluation}/edit', [EvaluationController::class, 'edit'])->name('edit');
    Route::put('/{evaluation}', [EvaluationController::class, 'update'])->name('update');
    Route::delete('/{evaluation}', [EvaluationController::class, 'destroy'])->name('destroy');
    
    // Actions
    Route::post('/{evaluation}/finaliser', [EvaluationController::class, 'finaliser'])->name('finaliser');
    Route::post('/{evaluation}/valider', [EvaluationController::class, 'valider'])->name('valider');
    
    // API
    Route::get('/stats', [EvaluationController::class, 'apiStats'])->name('stats');
});
