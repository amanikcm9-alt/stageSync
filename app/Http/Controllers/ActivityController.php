<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivitySubmission;
use App\Models\User;
use App\Models\OffreStage;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher le tableau de bord des activités selon le rôle
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name === 'stagiaire') {
            return $this->dashboardStagiaire($request);
        } elseif ($user->role->name === 'encadrant') {
            return $this->dashboardEncadrant($request);
        }
        
        return redirect()->back()->with('error', 'Accès non autorisé');
    }

    /**
     * Dashboard pour le stagiaire
     */
    public function dashboardStagiaire(Request $request)
    {
        $stagiaire = Auth::user();
        
        // Activités assignées au stagiaire
        $activities = Activity::where('stagiaire_id', $stagiaire->id)
            ->with(['encadrant', 'submissions', 'documents'])
            ->latest()
            ->get();

        // Activités proposées au stagiaire
        $proposedActivities = Activity::whereNull('stagiaire_id')
            ->where(function($query) use ($stagiaire) {
                $query->where('encadrant_id', $stagiaire->encadrant_id)
                      ->orWhere('encadrant_id', $stagiaire->encadrant_faculte_id)
                      ->orWhere('encadrant_id', $stagiaire->encadrant_entreprise_id);
            })
            ->with(['encadrant'])
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
        ];

        // Évaluations du stagiaire
        $evaluations = \App\Models\Evaluation::where('stagiaire_id', $stagiaire->id)
            ->with(['evaluateur', 'activity'])
            ->latest()
            ->get();

        // Documents et supports
        $documents = \App\Models\Document::where(function($query) use ($stagiaire) {
                $query->where('offre_stage_id', $stagiaire->offre_stage_id)
                      ->orWhere('type', 'reglement');
            })
            ->publies()
            ->latest()
            ->get();

        // Ajouter les évaluations aux stats
        $stats['evaluations'] = $evaluations->count();

        // Notifications du stagiaire
        $notifications = \App\Models\Discussion::where('receiver_id', $stagiaire->id)
            ->where('read', false)
            ->with(['sender', 'activity'])
            ->latest()
            ->take(5)
            ->get();

        // Charger les encadrants du stagiaire
        $encadrants = collect();
        if ($stagiaire->encadrant) {
            $encadrants->push($stagiaire->encadrant);
        }
        if ($stagiaire->encadrant_faculte) {
            $encadrants->push($stagiaire->encadrant_faculte);
        }
        if ($stagiaire->encadrant_entreprise) {
            $encadrants->push($stagiaire->encadrant_entreprise);
        }
        $encadrants = $encadrants->unique('id');

        // Charger l'offre de stage du stagiaire
        $offreStage = $stagiaire->offre_stage;
        
        // Logs de débogage pour l'offre de stage
        \Log::info('Dashboard Stagiaire - ID: ' . $stagiaire->id);
        \Log::info('Stagiaire: ' . $stagiaire->prenom . ' ' . $stagiaire->nom);
        \Log::info('offre_stage_id: ' . ($stagiaire->offre_stage_id ?? 'NULL'));
        
        // Si l'offre_stage_id est NULL, essayer de trouver l'offre par d'autres moyens
        if (!$offreStage && !$stagiaire->offre_stage_id) {
            \Log::info('Tentative de trouver l\'offre par d\'autres moyens...');
            
            // Méthode 1: Chercher les offres où le stagiaire est le candidat accepté
            $candidatureAcceptee = \App\Models\Candidature::where('user_id', $stagiaire->id)
                ->where('statut', 'acceptee')
                ->with('offreStage')
                ->first();
                
            if ($candidatureAcceptee && $candidatureAcceptee->offreStage) {
                $offreStage = $candidatureAcceptee->offreStage;
                \Log::info('Offre trouvée via candidature: ' . $offreStage->titre);
                
                // Mettre à jour le champ offre_stage_id du stagiaire
                $stagiaire->offre_stage_id = $offreStage->id;
                $stagiaire->save();
                \Log::info('offre_stage_id mis à jour: ' . $offreStage->id);
            }
            
            // Méthode 2: Chercher les offres créées par le RH pour ce stagiaire
            if (!$offreStage) {
                $offreDuStagiaire = \App\Models\OffreStage::whereHas('candidatures', function($query) use ($stagiaire) {
                    $query->where('user_id', $stagiaire->id)->where('statut', 'acceptee');
                })->first();
                
                if ($offreDuStagiaire) {
                    $offreStage = $offreDuStagiaire;
                    \Log::info('Offre trouvée via relation candidatures: ' . $offreStage->titre);
                    
                    // Mettre à jour le champ offre_stage_id du stagiaire
                    $stagiaire->offre_stage_id = $offreStage->id;
                    $stagiaire->save();
                    \Log::info('offre_stage_id mis à jour: ' . $offreStage->id);
                }
            }
            
            // Méthode 3: Chercher la dernière offre du stagiaire (fallback)
            if (!$offreStage) {
                $derniereOffre = \App\Models\OffreStage::latest()->first();
                if ($derniereOffre) {
                    // Vérifier si cette offre a des candidatures de ce stagiaire
                    $aCandidature = \App\Models\Candidature::where('offre_stage_id', $derniereOffre->id)
                        ->where('user_id', $stagiaire->id)
                        ->exists();
                        
                    if ($aCandidature) {
                        $offreStage = $derniereOffre;
                        \Log::info('Offre trouvée via fallback: ' . $offreStage->titre);
                        
                        // Mettre à jour le champ offre_stage_id du stagiaire
                        $stagiaire->offre_stage_id = $offreStage->id;
                        $stagiaire->save();
                        \Log::info('offre_stage_id mis à jour: ' . $offreStage->id);
                    }
                }
            }
        }
        
        if ($offreStage) {
            \Log::info('Offre de stage trouvée: ' . $offreStage->titre);
        } else {
            \Log::info('Aucune offre de stage trouvée pour ce stagiaire');
        }

        return view('stagiaire.activities.dashboard', compact('activities', 'proposedActivities', 'stats', 'documents', 'evaluations', 'notifications', 'encadrants', 'offreStage'));
    }

    /**
     * Dashboard pour l'encadrant
     */
    public function dashboardEncadrant(Request $request)
    {
        $encadrant = Auth::user();
        
        // Activités créées par l'encadrant
        $activities = Activity::where('encadrant_id', $encadrant->id)
            ->with(['stagiaire', 'submissions', 'documents'])
            ->latest()
            ->get();

        // Stagiaires suivis
        $stagiaires = User::whereHas('role', fn($q) => $q->where('name', 'stagiaire'))
            ->where(function($query) use ($encadrant) {
                $query->where('encadrant_id', $encadrant->id)
                      ->orWhere('encadrant_faculte_id', $encadrant->id)
                      ->orWhere('encadrant_entreprise_id', $encadrant->id);
            })
            ->with(['activities' => fn($q) => $q->where('encadrant_id', $encadrant->id), 'offre_stage'])
            ->get();
            
        // Notifications non lues pour l'encadrant
        $notificationsNonLues = Discussion::where('receiver_id', $encadrant->id)
            ->where('read', false)
            ->where('type', 'notification')
            ->with(['sender', 'activity'])
            ->latest()
            ->limit(10)
            ->get();
            
        // Logs de débogage pour vérifier les offres de stage
        \Log::info('Stagiaires chargés pour encadrant ' . $encadrant->id . ': ' . $stagiaires->count());
        foreach ($stagiaires as $stagiaire) {
            \Log::info('Stagiaire: ' . $stagiaire->prenom . ' ' . $stagiaire->nom . ', offre_stage_id: ' . $stagiaire->offre_stage_id);
            
            // Si le stagiaire n'a pas d'offre_stage_id, essayer de le corriger automatiquement
            if (!$stagiaire->offre_stage_id && !$stagiaire->offre_stage) {
                \Log::info('Dashboard Encadrant - Tentative de correction automatique pour le stagiaire ' . $stagiaire->id . ' (' . $stagiaire->prenom . ' ' . $stagiaire->nom . ')');
                
                // Méthode 1: Chercher via candidatures acceptées
                $candidatureAcceptee = \App\Models\Candidature::where('user_id', $stagiaire->id)
                    ->where('statut', 'accepte')
                    ->with('offreStage')
                    ->first();
                    
                if ($candidatureAcceptee && $candidatureAcceptee->offreStage) {
                    $stagiaire->offre_stage_id = $candidatureAcceptee->offreStage->id;
                    $stagiaire->save();
                    \Log::info('Correction automatique réussie: offre_stage_id = ' . $candidatureAcceptee->offreStage->id);
                    
                    // Recharger la relation pour l'affichage
                    $stagiaire->load('offre_stage');
                } else {
                    // Méthode 2: Chercher la dernière offre avec candidature
                    $derniereOffre = \App\Models\OffreStage::latest()->first();
                    if ($derniereOffre) {
                        $aCandidature = \App\Models\Candidature::where('offre_stage_id', $derniereOffre->id)
                            ->where('user_id', $stagiaire->id)
                            ->exists();
                            
                        if ($aCandidature) {
                            $stagiaire->offre_stage_id = $derniereOffre->id;
                            $stagiaire->save();
                            \Log::info('Correction automatique réussie via fallback: offre_stage_id = ' . $derniereOffre->id);
                            
                            // Recharger la relation pour l'affichage
                            $stagiaire->load('offre_stage');
                        }
                    }
                }
            }
            
            if ($stagiaire->offre_stage) {
                \Log::info('Offre de stage trouvée: ' . $stagiaire->offre_stage->titre);
            } else {
                \Log::info('Aucune offre de stage pour ce stagiaire - offre_stage_id: ' . ($stagiaire->offre_stage_id ?? 'NULL'));
                
                // Forcer le rechargement de la relation
                if ($stagiaire->offre_stage_id) {
                    \Log::info('Tentative de rechargement forcé de la relation...');
                    $stagiaire->load('offre_stage');
                    if ($stagiaire->offre_stage) {
                        \Log::info('Relation rechargée avec succès: ' . $stagiaire->offre_stage->titre);
                    } else {
                        \Log::info('Le rechargement a échoué - vérification de l\'offre dans la base...');
                        $offre = \App\Models\OffreStage::find($stagiaire->offre_stage_id);
                        if ($offre) {
                            \Log::info('Offre trouvée dans la base: ' . $offre->titre . ' mais la relation ne fonctionne pas');
                        } else {
                            \Log::info('Offre non trouvée dans la base avec ID: ' . $stagiaire->offre_stage_id);
                        }
                    }
                }
            }
        }

        // Notifications (discussions non lues reçues par l'encadrant)
        $notifications = Discussion::where('receiver_id', $encadrant->id)
            ->where('read', false)
            ->with(['sender', 'activity'])
            ->latest()
            ->get();

        // Statistiques
        $stats = [
            'total_activities' => $activities->count(),
            'en_cours' => $activities->where('statut', 'en_cours')->count(),
            'soumises' => $activities->where('statut', 'soumise')->count(),
            'validees' => $activities->where('statut', 'validee')->count(),
            'total_stagiaires' => $stagiaires->count(),
            'total_evaluations' => \App\Models\Evaluation::where('evaluateur_id', $encadrant->id)->count(),
        ];

        $evaluations = \App\Models\Evaluation::where('evaluateur_id', $encadrant->id)
            ->with('stagiaire')
            ->latest()
            ->get();

        // Documents de l'encadrant
        $documents = \App\Models\Document::where(function($query) use ($encadrant) {
                $query->where('uploaded_by', $encadrant->id)
                      ->orWhere('type', 'reglement');
            })
            ->latest()
            ->get();

        return view('encadrant.activities.dashboard', compact('activities', 'stagiaires', 'stats', 'notifications', 'evaluations', 'documents'));
    }

    /**
     * Page Mes Activités pour le stagiaire
     */
    public function mesActivites(Request $request)
    {
        $stagiaire = Auth::user();
        
        // Activités assignées au stagiaire
        $activities = Activity::where('stagiaire_id', $stagiaire->id)
            ->with(['encadrant', 'submissions', 'documents'])
            ->latest()
            ->get();

        // Activités proposées au stagiaire
        $proposedActivities = Activity::whereNull('stagiaire_id')
            ->where(function($query) use ($stagiaire) {
                $query->where('encadrant_id', $stagiaire->encadrant_id);
                if ($stagiaire->encadrant_faculte_id) {
                    $query->orWhere('encadrant_id', $stagiaire->encadrant_faculte_id);
                }
                if ($stagiaire->encadrant_entreprise_id) {
                    $query->orWhere('encadrant_id', $stagiaire->encadrant_entreprise_id);
                }
            })
            ->with(['encadrant'])
            ->latest()
            ->get();

        return view('stagiaire.activities.index', compact('activities', 'proposedActivities'));
    }

    /**
     * Page Mes Évaluations pour le stagiaire
     */
    public function mesEvaluations(Request $request)
    {
        $stagiaire = Auth::user();
        
        // Évaluations reçues par le stagiaire
        $evaluations = \App\Models\Evaluation::where('stagiaire_id', $stagiaire->id)
            ->with(['evaluateur', 'activity'])
            ->latest()
            ->get();

        return view('stagiaire.evaluations.index', compact('evaluations'));
    }

    /**
     * Page Mes Activités pour l'encadrant
     */
    public function mesActivitesEncadrant(Request $request)
    {
        $encadrant = Auth::user();
        
        // Vérification manuelle du rôle pour contourner le problème de middleware
        if (!$encadrant->role || $encadrant->role->name !== 'encadrant') {
            abort(403, 'Accès réservé aux encadrants');
        }
        
        // Activités créées par l'encadrant
        $activities = Activity::where('encadrant_id', $encadrant->id)
            ->with(['stagiaire', 'submissions', 'documents'])
            ->when($request->statut, fn($q, $s) => $q->where('statut', $s))
            ->when($request->priorite, fn($q, $p) => $q->where('priorite', $p))
            ->when($request->stagiaire_id, fn($q, $sid) => $q->where('stagiaire_id', $sid))
            ->latest()
            ->get();

        // Stagiaires suivis pour le filtre
        $stagiaires = User::whereHas('role', fn($q) => $q->where('name', 'stagiaire'))
            ->where(function($query) use ($encadrant) {
                $query->where('encadrant_id', $encadrant->id)
                      ->orWhere('encadrant_faculte_id', $encadrant->id)
                      ->orWhere('encadrant_entreprise_id', $encadrant->id);
            })
            ->get();

        return view('encadrant.activities.index', compact('activities', 'stagiaires'));
    }

    /**
     * Page Mes Évaluations pour l'encadrant
     */
    public function mesEvaluationsEncadrant(Request $request)
    {
        $encadrant = Auth::user();
        
        // Vérification manuelle du rôle pour contourner le problème de middleware
        if (!$encadrant->role || $encadrant->role->name !== 'encadrant') {
            abort(403, 'Accès réservé aux encadrants');
        }
        
        // Évaluations créées par l'encadrant
        $evaluations = \App\Models\Evaluation::where('evaluateur_id', $encadrant->id)
            ->with(['stagiaire', 'activity'])
            ->when($request->statut, fn($q, $s) => $q->where('statut', $s))
            ->when($request->stagiaire_id, fn($q, $sid) => $q->where('stagiaire_id', $sid))
            ->when($request->activity_id, fn($q, $aid) => $q->where('activity_id', $aid))
            ->latest()
            ->get();

        // Stagiaires suivis pour le filtre
        $stagiaires = User::whereHas('role', fn($q) => $q->where('name', 'stagiaire'))
            ->where(function($query) use ($encadrant) {
                $query->where('encadrant_id', $encadrant->id)
                      ->orWhere('encadrant_faculte_id', $encadrant->id)
                      ->orWhere('encadrant_entreprise_id', $encadrant->id);
            })
            ->get();

        // Activités pour le filtre
        $activities = Activity::where('encadrant_id', $encadrant->id)->get();

        return view('encadrant.evaluations.index', compact('evaluations', 'stagiaires', 'activities'));
    }

    /**
     * Afficher la liste des activités
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name === 'encadrant') {
            $activities = Activity::where('encadrant_id', $user->id)
                ->with(['stagiaire', 'submissions'])
                ->when($request->statut, fn($q, $s) => $q->where('statut', $s))
                ->when($request->priorite, fn($q, $p) => $q->where('priorite', $p))
                ->latest()
                ->paginate(15);
                
            // Récupérer les stagiaires de l'encadrant pour le filtre
            $stagiaires = User::whereHas('role', fn($q) => $q->where('name', 'stagiaire'))
                ->where(function($query) use ($user) {
                    $query->where('encadrant_id', $user->id)
                          ->orWhere('encadrant_faculte_id', $user->id)
                          ->orWhere('encadrant_entreprise_id', $user->id);
                })
                ->get();
                
            return view('encadrant.activities.index', compact('activities', 'stagiaires'));
        }
        
        return redirect()->route('activities.dashboard');
    }

    /**
     * Afficher le formulaire de proposition d'activité pour les stagiaires
     */
    public function proposeForm()
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            \Log::info('Utilisateur non connecté tentant d\'accéder à la page de proposition');
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour accéder à cette page');
        }
        
        $user = Auth::user();
        
        // Log de débogage
        \Log::info('Accès à la page de proposition', [
            'user_id' => $user->id,
            'user_role' => $user->role->name ?? 'non défini',
            'user_name' => $user->nom . ' ' . $user->prenom
        ]);
        
        // Vérifier si l'utilisateur a bien un rôle défini
        if (!$user->role) {
            \Log::warning('Utilisateur sans rôle tentant d\'accéder à la page de proposition', [
                'user_id' => $user->id
            ]);
            return redirect()->back()->with('error', 'Votre compte n\'a pas de rôle défini. Contactez l\'administrateur.');
        }
        
        if ($user->role->name !== 'stagiaire') {
            \Log::warning('Accès non autorisé - utilisateur non stagiaire', [
                'user_id' => $user->id,
                'user_role' => $user->role->name
            ]);
            return redirect()->back()->with('error', 'Accès non autorisé. Cette page est réservée aux stagiaires.');
        }
        
        return view('stagiaire.activities.propose');
    }

    /**
     * Afficher le formulaire de création d'activité
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $stagiaires = User::whereHas('role', fn($q) => $q->where('name', 'stagiaire'))
            ->where(function($query) use ($user) {
                $query->where('encadrant_id', $user->id)
                      ->orWhere('encadrant_faculte_id', $user->id)
                      ->orWhere('encadrant_entreprise_id', $user->id);
            })
            ->get();
            
        return view('encadrant.activities.create', compact('stagiaires'));
    }

    /**
     * Enregistrer une nouvelle activité
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'objectifs' => 'nullable|string',
            'priorite' => 'required|in:basse,moyenne,haute,urgente',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'date_limite' => 'nullable|date|after_or_equal:date_debut',
            'livrables_attendus' => 'nullable|string',
            'stagiaire_id' => 'nullable|exists:users,id',
        ]);

        $activity = Activity::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'objectifs' => $request->objectifs,
            'priorite' => $request->priorite,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'date_limite' => $request->date_limite,
            'livrables_attendus' => $request->livrables_attendus,
            'encadrant_id' => $user->id,
            'stagiaire_id' => $request->stagiaire_id,
            'statut' => $request->stagiaire_id ? 'assignee' : 'proposee',
        ]);

        // Créer les notifications
        $this->creerNotificationsActivite($activity, $user, 'creation');

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activité créée avec succès');
    }

    /**
     * Afficher une activité
     */
    public function show(Activity $activity)
    {
        $user = Auth::user();
        
        // Vérifier les droits d'accès
        if ($user->role->name === 'stagiaire' && $activity->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        if ($user->role->name === 'encadrant' && $activity->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $activity->load(['encadrant', 'stagiaire', 'submissions', 'documents']);
        
        return view('activities.show', compact('activity'));
    }

    /**
     * Obtenir les détails d'une activité en JSON
     */
    public function getActivityJson(Activity $activity)
    {
        $user = Auth::user();
        
        // Vérifier les droits d'accès
        if ($user->role->name === 'stagiaire' && $activity->stagiaire_id !== $user->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        if ($user->role->name === 'encadrant' && $activity->encadrant_id !== $user->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        $activity->load(['encadrant', 'stagiaire']);
        
        return response()->json([
            'id' => $activity->id,
            'titre' => $activity->titre,
            'description' => $activity->description,
            'date_debut' => $activity->date_debut ? $activity->date_debut->format('Y-m-d') : null,
            'date_limite' => $activity->date_limite ? $activity->date_limite->format('Y-m-d') : null,
            'statut' => $activity->statut,
            'priorite' => $activity->priorite,
            'encadrant' => $activity->encadrant ? [
                'id' => $activity->encadrant->id,
                'prenom' => $activity->encadrant->prenom,
                'nom' => $activity->encadrant->nom,
                'email' => $activity->encadrant->email
            ] : null,
            'stagiaire' => $activity->stagiaire ? [
                'id' => $activity->stagiaire->id,
                'prenom' => $activity->stagiaire->prenom,
                'nom' => $activity->stagiaire->nom,
                'email' => $activity->stagiaire->email
            ] : null
        ]);
    }

    /**
     * Afficher le formulaire d'édition d'activité
     */
    public function edit(Activity $activity)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant' || $activity->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $stagiaires = User::whereHas('role', fn($q) => $q->where('name', 'stagiaire'))
            ->where(function($query) use ($user) {
                $query->where('encadrant_id', $user->id)
                      ->orWhere('encadrant_faculte_id', $user->id)
                      ->orWhere('encadrant_entreprise_id', $user->id);
            })
            ->get();
            
        return view('encadrant.activities.edit', compact('activity', 'stagiaires'));
    }

    /**
     * Mettre à jour une activité
     */
    public function update(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant' || $activity->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'objectifs' => 'nullable|string',
            'priorite' => 'required|in:basse,moyenne,haute,urgente',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'date_limite' => 'nullable|date|after_or_equal:date_debut',
            'livrables_attendus' => 'nullable|string',
            'stagiaire_id' => 'nullable|exists:users,id',
        ]);

        $activity->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'objectifs' => $request->objectifs,
            'priorite' => $request->priorite,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'date_limite' => $request->date_limite,
            'livrables_attendus' => $request->livrables_attendus,
            'stagiaire_id' => $request->stagiaire_id,
            'statut' => $request->stagiaire_id ? 'assignee' : 'proposee',
        ]);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activité mise à jour avec succès');
    }

    /**
     * Supprimer une activité
     */
    public function destroy(Activity $activity)
    {
        $user = Auth::user();
        
        // Logs de débogage
        \Log::info('Tentative de suppression d\'activité', [
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'user_role' => $user->role->name,
            'activity_stagiaire_id' => $activity->stagiaire_id,
            'activity_encadrant_id' => $activity->encadrant_id
        ]);
        
        // Autoriser les encadrants à supprimer n'importe quelle activité
        if ($user->role->name === 'encadrant' && $activity->encadrant_id !== $user->id) {
            \Log::warning('Accès refusé - encadrant non propriétaire', [
                'activity_id' => $activity->id,
                'user_id' => $user->id,
                'activity_encadrant_id' => $activity->encadrant_id,
                'reason' => 'L\'encadrant n\'est pas propriétaire de l\'activité'
            ]);
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }
        
        // Autoriser les stagiaires à supprimer uniquement leurs propres activités
        if ($user->role->name === 'stagiaire' && $activity->stagiaire_id !== $user->id) {
            \Log::warning('Accès refusé - stagiaire non propriétaire', [
                'activity_id' => $activity->id,
                'user_id' => $user->id,
                'activity_stagiaire_id' => $activity->stagiaire_id,
                'reason' => 'Le stagiaire n\'est pas propriétaire de l\'activité'
            ]);
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }
        
        try {
            $activity->delete();
            
            \Log::info('Activité supprimée avec succès', [
                'activity_id' => $activity->id,
                'user_id' => $user->id,
                'user_role' => $user->role->name
            ]);
            
            // Retourner une réponse JSON selon le rôle
            if ($user->role->name === 'encadrant') {
                return response()->json([
                    'success' => true, 
                    'message' => 'Activité supprimée avec succès',
                    'redirect' => route('activities.index')
                ]);
            } else {
                return response()->json([
                    'success' => true, 
                    'message' => 'Activité supprimée avec succès',
                    'redirect' => route('stagiaire.activities.index')
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression d\'activité', [
                'activity_id' => $activity->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false, 
                'error' => 'Erreur lors de la suppression de l\'activité'
            ], 500);
        }
    }

    /**
     * Actions du stagiaire sur une activité
     */
    public function realiser(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'stagiaire' || $activity->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $activity->demarrer();
        
        // Créer les notifications pour le démarrage
        $this->creerNotificationsActivite($activity, $user, 'demarrage');

        return redirect()->back()->with('success', 'Activité démarrée. Une notification a été envoyée à l\'encadrant.');
    }

    public function refuser(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'stagiaire' || $activity->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'justification' => 'required|string|max:1000',
        ]);
        
        $activity->refuser($request->justification);
        
        // Créer une notification automatique à l'encadrant
        Discussion::create([
            'activity_id' => $activity->id,
            'sender_id' => $user->id,
            'receiver_id' => $activity->encadrant_id,
            'message' => "L'activité '{$activity->titre}' a été refusée. Justification : {$request->justification}",
            'type' => 'refus',
            'read' => false
        ]);
        
        return redirect()->back()->with('success', 'Activité refusée. Une notification a été envoyée à l\'encadrant.');
    }

    public function demanderInformation(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'stagiaire' || $activity->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);
        
        // Créer une discussion avec l'encadrant
        Discussion::create([
            'activity_id' => $activity->id,
            'sender_id' => $user->id,
            'receiver_id' => $activity->encadrant_id,
            'message' => "Demande d'information pour l'activité '{$activity->titre}' : {$request->message}",
            'type' => 'demande_info',
            'read' => false
        ]);
        
        return redirect()->back()->with('success', 'Demande d\'information envoyée à l\'encadrant');
    }

    public function proposerActivite(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'stagiaire') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'objectifs' => 'nullable|string',
        ]);

        $activity = Activity::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'objectifs' => $request->objectifs,
            'encadrant_id' => $user->encadrant_id,
            'stagiaire_id' => $user->id,
            'statut' => 'proposee',
        ]);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activité proposée avec succès');
    }

    /**
     * Actions de l'encadrant sur une activité
     */
    public function validerActivite(Activity $activity)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant' || $activity->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $activity->valider();
        
        return redirect()->back()->with('success', 'Activité validée');
    }

    public function assignerStagiaire(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        \Log::info('Tentative d\'assignation', [
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'user_role' => $user->role->name,
            'activity_encadrant_id' => $activity->encadrant_id,
            'requested_stagiaire_id' => $request->stagiaire_id
        ]);
        
        if ($user->role->name !== 'encadrant' || $activity->encadrant_id !== $user->id) {
            \Log::warning('Accès non autorisé pour assignation', [
                'activity_id' => $activity->id,
                'user_id' => $user->id,
                'user_role' => $user->role->name,
                'activity_encadrant_id' => $activity->encadrant_id
            ]);
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }
        
        $request->validate([
            'stagiaire_id' => 'required|exists:users,id',
        ]);
        
        try {
            $activity->assignerAuStagiaire($request->stagiaire_id);
            
            \Log::info('Activité assignée avec succès', [
                'activity_id' => $activity->id,
                'stagiaire_id' => $request->stagiaire_id,
                'new_statut' => $activity->statut
            ]);
            
            return response()->json(['success' => true, 'message' => 'Activité assignée au stagiaire']);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'assignation', [
                'activity_id' => $activity->id,
                'stagiaire_id' => $request->stagiaire_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'error' => 'Erreur lors de l\'assignation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function evaluerActivite(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        \Log::info('Tentative d\'évaluation', [
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'user_role' => $user->role->name,
            'activity_stagiaire_id' => $activity->stagiaire_id,
            'activity_encadrant_id' => $activity->encadrant_id
        ]);
        
        if ($user->role->name !== 'encadrant' || $activity->encadrant_id !== $user->id) {
            \Log::warning('Accès non autorisé pour évaluation', [
                'activity_id' => $activity->id,
                'user_id' => $user->id,
                'required_role' => 'encadrant',
                'user_role' => $user->role->name,
                'activity_encadrant' => $activity->encadrant_id,
                'user_id' => $user->id
            ]);
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }
        
        // Vérifier si l'activité a un stagiaire assigné
        if (!$activity->stagiaire_id) {
            \Log::warning('Tentative d\'évaluation sans stagiaire assigné', [
                'activity_id' => $activity->id,
                'user_id' => $user->id
            ]);
            return response()->json(['success' => false, 'error' => 'Aucun stagiaire assigné à cette activité'], 400);
        }
        
        $request->validate([
            'note' => 'required|integer|min:0|max:20',
            'feedback' => 'nullable|string|max:1000',
        ]);
        
        try {
            // Créer une nouvelle évaluation avec les bons champs
            $evaluation = \App\Models\Evaluation::create([
                'activity_id' => $activity->id,
                'evaluateur_id' => $user->id,
                'stagiaire_id' => $activity->stagiaire_id,
                'titre' => 'Évaluation de l\'activité ' . $activity->id,
                'note_generale' => $request->note,
                'commentaires' => $request->feedback,
                'statut' => 'validee',
            ]);
            
            \Log::info('Évaluation créée avec succès', [
                'evaluation_id' => $evaluation->id,
                'activity_id' => $activity->id,
                'stagiaire_id' => $activity->stagiaire_id,
                'note' => $request->note
            ]);
            
            // Mettre à jour le statut de l'activité si nécessaire
            if ($activity->statut !== 'validee') {
                $activity->update(['statut' => 'validee']);
                \Log::info('Statut de l\'activité mis à jour', ['activity_id' => $activity->id, 'new_statut' => 'validee']);
            }
            
            return response()->json([
                'success' => true, 
                'message' => 'Évaluation enregistrée avec succès'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de l\'évaluation', [
                'activity_id' => $activity->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'error' => 'Erreur lors de la création de l\'évaluation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function mettreAJourProgression(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'stagiaire' || $activity->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'progression' => 'required|integer|min:0|max:100',
        ]);
        
        $activity->mettreAJourProgression($request->progression);
        
        return redirect()->back()->with('success', 'Progression mise à jour');
    }

    /**
     * Récupérer les discussions d'une activité
     */
    public function getDiscussions(Activity $activity)
    {
        $user = Auth::user();
        
        // Vérifier les droits d'accès
        if (($user->role->name === 'stagiaire' && $activity->stagiaire_id !== $user->id) ||
            ($user->role->name === 'encadrant' && $activity->encadrant_id !== $user->id)) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        $discussions = $activity->discussions()->with(['sender', 'receiver'])->get();
        
        return response()->json($discussions);
    }

    /**
     * Envoyer un message dans une discussion
     */
    public function sendDiscussion(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        // Vérifier les droits d'accès
        if (($user->role->name === 'stagiaire' && $activity->stagiaire_id !== $user->id) ||
            ($user->role->name === 'encadrant' && $activity->encadrant_id !== $user->id)) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        $request->validate([
            'message' => 'required|string|max:1000',
            'type' => 'required|in:message,refus,acceptation,demande_info,evaluation'
        ]);
        
        // Déterminer le destinataire
        $receiverId = $user->role->name === 'stagiaire' ? $activity->encadrant_id : $activity->stagiaire_id;
        
        $discussion = Discussion::create([
            'activity_id' => $activity->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'type' => $request->type,
            'read' => false
        ]);
        
        return response()->json(['success' => true, 'discussion' => $discussion]);
    }

    /**
     * Marquer les discussions comme lues
     */
    public function markDiscussionsAsRead(Activity $activity)
    {
        $user = Auth::user();
        
        // Marquer tous les messages non lus destinés à l'utilisateur comme lus
        Discussion::where('activity_id', $activity->id)
            ->where('receiver_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Récupérer les discussions générales entre deux utilisateurs
     */
    public function getGeneralDiscussions(User $user)
    {
        $currentUser = Auth::user();
        
        // Vérifier que les utilisateurs peuvent communiquer (stagiaire-encadrant)
        if (!$this->canCommunicate($currentUser, $user)) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        $discussions = Discussion::where(function($query) use ($currentUser, $user) {
                $query->where('sender_id', $currentUser->id)->where('receiver_id', $user->id)
                      ->orWhere('sender_id', $user->id)->where('receiver_id', $currentUser->id);
            })
            ->whereNull('activity_id') // Discussions générales
            ->with(['sender', 'receiver'])
            ->latest()
            ->get();
        
        return response()->json($discussions);
    }

    /**
     * Envoyer un message dans une discussion générale
     */
    public function sendGeneralDiscussion(Request $request, User $user)
    {
        $currentUser = Auth::user();
        
        \Log::info('Tentative envoi message général', [
            'current_user_id' => $currentUser->id,
            'current_user_role' => $currentUser->role->name,
            'target_user_id' => $user->id,
            'target_user_role' => $user->role->name,
            'message' => $request->message
        ]);
        
        // Vérification simple de la communication
        if ($currentUser->role->name === 'stagiaire' && $user->role->name === 'encadrant') {
            if ($currentUser->encadrant_id !== $user->id) {
                \Log::error('Stagiaire ne peut communiquer qu\'avec son encadrant');
                return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
            }
        } elseif ($currentUser->role->name === 'encadrant' && $user->role->name === 'stagiaire') {
            if ($user->encadrant_id !== $currentUser->id) {
                \Log::error('Encadrant ne peut communiquer qu\'avec ses stagiaires');
                return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
            }
        } else {
            \Log::error('Rôles non compatibles pour la communication');
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }
        
        try {
            // Validation manuelle simple
            $message = $request->input('message', '');
            $type = $request->input('type', 'message');
            
            if (empty($message)) {
                return response()->json(['success' => false, 'error' => 'Message vide'], 422);
            }
            
            if (strlen($message) > 1000) {
                return response()->json(['success' => false, 'error' => 'Message trop long'], 422);
            }
            
            $allowedTypes = ['message', 'refus', 'acceptation', 'demande_info', 'evaluation'];
            if (!in_array($type, $allowedTypes)) {
                $type = 'message'; // Type par défaut
            }
            
            $discussion = Discussion::create([
                'activity_id' => null, // Discussion générale
                'sender_id' => $currentUser->id,
                'receiver_id' => $user->id,
                'message' => $message,
                'type' => $type,
                'read' => false
            ]);
            
            \Log::info('Message créé avec succès', ['discussion_id' => $discussion->id]);
            
            return response()->json(['success' => true, 'discussion' => $discussion]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur serveur', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Marquer les discussions générales comme lues
     */
    public function markGeneralDiscussionsAsRead(User $user)
    {
        $currentUser = Auth::user();
        
        // Marquer tous les messages non lus de cet utilisateur comme lus
        Discussion::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->whereNull('activity_id') // Discussions générales
            ->where('read', false)
            ->update(['read' => true]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Envoyer un message simple (méthode de secours)
     */
    public function sendSimpleDiscussion(Request $request, User $user)
    {
        try {
            $currentUser = Auth::user();
            $message = $request->input('message', '');
            
            // Validation minimale
            if (empty(trim($message))) {
                return response()->json(['success' => false, 'error' => 'Message requis']);
            }
            
            // Création directe sans validation complexe
            $discussion = new Discussion();
            $discussion->activity_id = null;
            $discussion->sender_id = $currentUser->id;
            $discussion->receiver_id = $user->id;
            $discussion->message = substr($message, 0, 1000); // Limiter la longueur
            $discussion->type = 'message';
            $discussion->read = false;
            $discussion->save();
            
            return response()->json(['success' => true, 'discussion' => $discussion]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Marquer une notification spécifique comme lue
     */
    public function markNotificationAsRead(Discussion $discussion)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est bien le destinataire
        if ($discussion->receiver_id !== $user->id) {
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }
        
        $discussion->read = true;
        $discussion->save();
        
        return response()->json(['success' => true]);
    }

    /**
     * Vérifier si deux utilisateurs peuvent communiquer
     */
    private function canCommunicate($user1, $user2)
    {
        \Log::info('Vérification communication', [
            'user1_id' => $user1->id,
            'user1_role' => $user1->role->name,
            'user2_id' => $user2->id,
            'user2_role' => $user2->role->name,
            'user1_encadrant_id' => $user1->encadrant_id ?? null,
            'user2_encadrant_id' => $user2->encadrant_id ?? null
        ]);
        
        // Un stagiaire peut communiquer avec son encadrant
        if ($user1->role->name === 'stagiaire' && $user2->role->name === 'encadrant') {
            $canCommunicate = $user1->encadrant_id === $user2->id;
            \Log::info('Test stagiaire-encadrant', ['result' => $canCommunicate]);
            return $canCommunicate;
        }
        
        // Un encadrant peut communiquer avec ses stagiaires
        if ($user1->role->name === 'encadrant' && $user2->role->name === 'stagiaire') {
            $canCommunicate = $user2->encadrant_id === $user1->id;
            \Log::info('Test encadrant-stagiaire', ['result' => $canCommunicate]);
            return $canCommunicate;
        }
        
        \Log::info('Aucune règle de communication applicable');
        return false;
    }

    /**
     * Stagiaire accepte une activité
     */
    public function accepter(Activity $activity)
    {
        $user = Auth::user();
        
        // Vérifier que l'activité est proposée au stagiaire
        if ($activity->stagiaire_id !== $user->id || $activity->statut !== 'proposee') {
            return response()->json(['success' => false, 'error' => 'Activité non accessible']);
        }

        // Mettre à jour le statut de l'activité
        $activity->update([
            'statut' => 'assignee',
            'date_debut' => now()
        ]);

        // Envoyer une notification à l'encadrant
        $this->envoyerNotification($activity->encadrant, $user, 'accepter', $activity);

        return response()->json([
            'success' => true, 
            'message' => 'Activité acceptée avec succès',
            'redirect' => route('stagiaire.dashboard')
        ]);
    }

    /**
     * Stagiaire refuse une activité
     */
    public function refuserActivite(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        // Vérifier que l'activité est proposée au stagiaire
        if ($activity->stagiaire_id !== $user->id || $activity->statut !== 'proposee') {
            return response()->json(['success' => false, 'error' => 'Activité non accessible']);
        }

        $raison = $request->input('raison', 'Aucune raison spécifiée');

        // Mettre à jour le statut de l'activité
        $activity->update([
            'statut' => 'refusee',
            'commentaires' => 'Refusé par le stagiaire. Raison: ' . $raison
        ]);

        // Envoyer une notification à l'encadrant
        $this->envoyerNotification($activity->encadrant, $user, 'refuser', $activity, $raison);

        return response()->json([
            'success' => true, 
            'message' => 'Activité refusée',
            'redirect' => route('stagiaire.dashboard')
        ]);
    }

    /**
     * Stagiaire demande des informations sur une activité
     */
    public function demanderInfo(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        // Vérifier que l'activité est accessible au stagiaire
        if ($activity->stagiaire_id !== $user->id) {
            return response()->json(['success' => false, 'error' => 'Activité non accessible']);
        }

        $question = $request->input('question', '');

        // Envoyer une notification à l'encadrant
        $this->envoyerNotification($activity->encadrant, $user, 'demander_info', $activity, $question);

        // Créer une discussion pour cette question
        Discussion::create([
            'sender_id' => $user->id,
            'receiver_id' => $activity->encadrant_id,
            'activity_id' => $activity->id,
            'message' => $question,
            'read' => false
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Question envoyée à l\'encadrant'
        ]);
    }

    /**
     * Stagiaire soumet un livrable
     */
    public function soumettreLivrable(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        // Vérifier que l'activité est assignée au stagiaire
        if ($activity->stagiaire_id !== $user->id || !in_array($activity->statut, ['assignee', 'en_cours'])) {
            return response()->json(['success' => false, 'error' => 'Activité non accessible']);
        }

        $commentaire = $request->input('commentaire', '');
        $fichier = $request->file('fichier');

        // Créer une soumission
        $submission = ActivitySubmission::create([
            'activity_id' => $activity->id,
            'stagiaire_id' => $user->id,
            'description_travail' => $commentaire,
            'statut' => 'soumis',
            'date_soumission' => now()
        ]);

        // Gérer le fichier si présent
        if ($fichier) {
            $chemin = $fichier->store('soumissions/' . $activity->id, 'public');
            $submission->update(['fichier_path' => $chemin]);
        }

        // Mettre à jour le statut de l'activité
        $activity->update([
            'statut' => 'soumise',
            'date_soumission' => now(),
            'progression' => 100
        ]);

        // Envoyer une notification à l'encadrant
        $this->envoyerNotification($activity->encadrant, $user, 'soumettre', $activity);

        return response()->json([
            'success' => true, 
            'message' => 'Livrable soumis avec succès',
            'redirect' => route('stagiaire.dashboard')
        ]);
    }

    /**
     * Stagiaire veut discuter d'une activité
     */
    public function discuter(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        // Vérifier les droits d'accès
        if ($user->role->name !== 'stagiaire' || $activity->stagiaire_id !== $user->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        $message = $request->input('message');
        
        if (empty($message)) {
            return response()->json(['error' => 'Le message ne peut pas être vide'], 422);
        }
        
        // Créer la discussion
        $discussion = Discussion::create([
            'activity_id' => $activity->id,
            'sender_id' => $user->id,
            'receiver_id' => $activity->encadrant_id,
            'message' => $message,
            'type' => 'message'
        ]);
        
        // Envoyer une notification à l'encadrant
        $this->envoyerNotification($activity->encadrant, $user, 'discuter', $activity, $message);
        
        return response()->json([
            'success' => true, 
            'message' => 'Message envoyé avec succès',
            'discussion' => $discussion
        ]);
    }
    
    /**
     * Éditer un message de discussion
     */
    public function editMessage(Request $request, Discussion $discussion)
    {
        $user = Auth::user();
        
        // Logs de débogage
        \Log::info('Tentative d\'édition de message', [
            'discussion_id' => $discussion->id,
            'sender_id' => $discussion->sender_id,
            'user_id' => $user->id,
            'user_role' => $user->role->name,
            'is_author' => $discussion->sender_id === $user->id
        ]);
        
        // Vérifier si l'utilisateur est l'auteur du message
        if ($discussion->sender_id !== $user->id) {
            \Log::warning('Accès non autorisé pour édition de message', [
                'discussion_id' => $discussion->id,
                'sender_id' => $discussion->sender_id,
                'user_id' => $user->id,
                'reason' => 'L\'utilisateur n\'est pas l\'auteur du message'
            ]);
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        $message = $request->input('message');
        
        if (empty($message)) {
            return response()->json(['error' => 'Le message ne peut pas être vide'], 422);
        }
        
        // Mettre à jour le message
        $discussion->update([
            'message' => $message,
            'edited_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Message modifié avec succès',
            'discussion' => $discussion->fresh()
        ]);
    }
    
    /**
     * Supprimer un message de discussion
     */
    public function deleteMessage(Discussion $discussion)
    {
        $user = Auth::user();
        
        // Logs de débogage
        \Log::info('Tentative de suppression de message', [
            'discussion_id' => $discussion->id,
            'sender_id' => $discussion->sender_id,
            'user_id' => $user->id,
            'user_role' => $user->role->name,
            'is_author' => $discussion->sender_id === $user->id
        ]);
        
        // Vérifier si l'utilisateur est l'auteur du message
        if ($discussion->sender_id !== $user->id) {
            \Log::warning('Accès non autorisé pour suppression de message', [
                'discussion_id' => $discussion->id,
                'sender_id' => $discussion->sender_id,
                'user_id' => $user->id,
                'reason' => 'L\'utilisateur n\'est pas l\'auteur du message'
            ]);
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        // Supprimer le message
        $discussion->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Message supprimé avec succès'
        ]);
    }

    /**
     * Envoyer une notification à un utilisateur
     */
    private function envoyerNotification($destinataire, $expediteur, $type, $activity = null, $message = null)
    {
        // Créer une notification dans la base de données
        Discussion::create([
            'sender_id' => $expediteur->id,
            'receiver_id' => $destinataire->id,
            'activity_id' => $activity ? $activity->id : null,
            'message' => $message ?? $this->genererMessageNotification($type, $activity),
            'type' => 'notification',
            'read' => false,
        ]);
    }


/**
 * Créer les notifications pour une activité
 */
private function creerNotificationsActivite($activity, $user, $action)
{
    switch ($action) {
        case 'creation':
            // Notification pour le stagiaire si l'activité lui est assignée
            if ($activity->stagiaire_id) {
                Discussion::create([
                    'sender_id' => $user->id,
                    'receiver_id' => $activity->stagiaire_id,
                    'activity_id' => $activity->id,
                    'message' => 'Nouvelle activité assignée: "' . $activity->titre . '"',
                    'type' => 'notification',
                    'read' => false,
                ]);
            }
            
            // Notification pour l'encadrant (confirmation)
            Discussion::create([
                'sender_id' => $user->id,
                'receiver_id' => $user->id,
                'activity_id' => $activity->id,
                'message' => 'Activité "' . $activity->titre . '" créée avec succès',
                'type' => 'notification',
                'read' => false,
            ]);
            break;
            
        case 'soumission':
            // Notification pour l'encadrant quand le stagiaire soumet
            if ($activity->encadrant_id) {
                Discussion::create([
                    'sender_id' => $user->id,
                    'receiver_id' => $activity->encadrant_id,
                    'activity_id' => $activity->id,
                    'message' => 'Le stagiaire a soumis une réponse pour: "' . $activity->titre . '"',
                    'type' => 'notification',
                    'read' => false,
                ]);
            }
            
            // Notification pour le stagiaire (confirmation)
            Discussion::create([
                'sender_id' => $user->id,
                'receiver_id' => $user->id,
                'activity_id' => $activity->id,
                'message' => 'Votre réponse a été soumise avec succès pour: "' . $activity->titre . '"',
                'type' => 'notification',
                'read' => false,
            ]);
            break;
    }
}

/**
 * Marquer les notifications comme lues
 */
public function marquerNotificationsLues(Request $request)
{
    $user = Auth::user();
    
    // Marquer toutes les notifications non lues de l'utilisateur comme lues
    Discussion::where('receiver_id', $user->id)
        ->where('read', false)
        ->where('type', 'notification')
        ->update(['read' => true]);
    
    return response()->json([
        'success' => true,
        'message' => 'Notifications marquées comme lues'
    ]);
}
}

