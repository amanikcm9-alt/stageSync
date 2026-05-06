<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Activity;
use App\Models\User;
use App\Models\OffreStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des évaluations
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Evaluation::with(['stagiaire', 'encadrant', 'activity']);
        
        // Filtrer selon le rôle
        if ($user->role->name === 'encadrant') {
            $query->where('encadrant_id', $user->id);
        } elseif ($user->role->name === 'stagiaire') {
            $query->where('stagiaire_id', $user->id);
        }
        
        // Filtrer par type
        if ($request->type) {
            $query->where('type', $request->type);
        }
        
        // Filtrer par statut
        if ($request->statut) {
            $query->where('statut', $request->statut);
        }
        
        $evaluations = $query->latest()->paginate(15);
        
        return view('evaluations.index', compact('evaluations'));
    }

    /**
     * Afficher le formulaire de création d'évaluation
     */
    public function create(Request $request)
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
        
        $activities = Activity::where('encadrant_id', $user->id)->get();
        
        $types = [
            'activite' => 'Évaluation d\'activité',
            'generale' => 'Évaluation générale',
            'finale' => 'Évaluation finale',
        ];
        
        return view('evaluations.create', compact('stagiaires', 'activities', 'types'));
    }

    /**
     * Enregistrer une nouvelle évaluation
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'stagiaire_id' => 'required|exists:users,id',
            'type' => 'required|in:activite,generale,finale',
            'activity_id' => 'nullable|exists:activities,id',
            'note_competence' => 'nullable|integer|min:0|max:20',
            'note_travail' => 'nullable|integer|min:0|max:20',
            'note_attitude' => 'nullable|integer|min:0|max:20',
            'appreciation' => 'nullable|string|max:2000',
            'points_forts' => 'nullable|string|max:1000',
            'points_amelioration' => 'nullable|string|max:1000',
            'commentaires' => 'nullable|string|max:1000',
            'date_evaluation' => 'required|date',
        ]);

        // Validation : si type = activite, activity_id est requis
        if ($request->type === 'activite' && !$request->activity_id) {
            return redirect()->back()
                ->with('error', 'L\'activité est requise pour une évaluation d\'activité')
                ->withInput();
        }

        $evaluation = Evaluation::create([
            'stagiaire_id' => $request->stagiaire_id,
            'encadrant_id' => $user->id,
            'activity_id' => $request->activity_id,
            'offre_stage_id' => $request->offre_stage_id,
            'type' => $request->type,
            'note_competence' => $request->note_competence,
            'note_travail' => $request->note_travail,
            'note_attitude' => $request->note_attitude,
            'appreciation' => $request->appreciation,
            'points_forts' => $request->points_forts,
            'points_amelioration' => $request->points_amelioration,
            'commentaires' => $request->commentaires,
            'date_evaluation' => $request->date_evaluation,
            'statut' => 'brouillon',
        ]);

        return redirect()->route('evaluations.show', $evaluation)
            ->with('success', 'Évaluation créée avec succès');
    }

    /**
     * Afficher une évaluation
     */
    public function show(Evaluation $evaluation)
    {
        $user = Auth::user();
        
        // Vérifier les droits d'accès
        if ($user->role->name === 'stagiaire' && $evaluation->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        if ($user->role->name === 'encadrant' && $evaluation->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $evaluation->load(['stagiaire', 'encadrant', 'activity']);
        
        return view('evaluations.show', compact('evaluation'));
    }

    /**
     * Afficher le formulaire d'édition d'évaluation
     */
    public function edit(Evaluation $evaluation)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant' || $evaluation->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        if ($evaluation->statut === 'validee') {
            return redirect()->back()->with('error', 'Cette évaluation est déjà validée');
        }
        
        $stagiaires = User::whereHas('role', fn($q) => $q->where('name', 'stagiaire'))
            ->where(function($query) use ($user) {
                $query->where('encadrant_id', $user->id)
                      ->orWhere('encadrant_faculte_id', $user->id)
                      ->orWhere('encadrant_entreprise_id', $user->id);
            })
            ->get();
        
        $activities = Activity::where('encadrant_id', $user->id)->get();
        
        $types = [
            'activite' => 'Évaluation d\'activité',
            'generale' => 'Évaluation générale',
            'finale' => 'Évaluation finale',
        ];
        
        return view('evaluations.edit', compact('evaluation', 'stagiaires', 'activities', 'types'));
    }

    /**
     * Mettre à jour une évaluation
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant' || $evaluation->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        if ($evaluation->statut === 'validee') {
            return redirect()->back()->with('error', 'Cette évaluation est déjà validée');
        }
        
        $request->validate([
            'note_competence' => 'nullable|integer|min:0|max:20',
            'note_travail' => 'nullable|integer|min:0|max:20',
            'note_attitude' => 'nullable|integer|min:0|max:20',
            'appreciation' => 'nullable|string|max:2000',
            'points_forts' => 'nullable|string|max:1000',
            'points_amelioration' => 'nullable|string|max:1000',
            'commentaires' => 'nullable|string|max:1000',
            'date_evaluation' => 'required|date',
        ]);

        $evaluation->update([
            'note_competence' => $request->note_competence,
            'note_travail' => $request->note_travail,
            'note_attitude' => $request->note_attitude,
            'appreciation' => $request->appreciation,
            'points_forts' => $request->points_forts,
            'points_amelioration' => $request->points_amelioration,
            'commentaires' => $request->commentaires,
            'date_evaluation' => $request->date_evaluation,
        ]);

        return redirect()->route('evaluations.show', $evaluation)
            ->with('success', 'Évaluation mise à jour avec succès');
    }

    /**
     * Supprimer une évaluation
     */
    public function destroy(Evaluation $evaluation)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant' || $evaluation->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        if ($evaluation->statut === 'validee') {
            return redirect()->back()->with('error', 'Cette évaluation est déjà validée');
        }
        
        $evaluation->delete();
        
        return redirect()->route('evaluations.index')
            ->with('success', 'Évaluation supprimée avec succès');
    }

    /**
     * Finaliser une évaluation
     */
    public function finaliser(Evaluation $evaluation)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant' || $evaluation->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $evaluation->finaliser();
        
        return redirect()->route('evaluations.show', $evaluation)
            ->with('success', 'Évaluation finalisée avec succès');
    }

    /**
     * Valider une évaluation
     */
    public function valider(Evaluation $evaluation)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['admin', 'rh'])) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $evaluation->valider();
        
        return redirect()->route('evaluations.show', $evaluation)
            ->with('success', 'Évaluation validée avec succès');
    }

    /**
     * Évaluer une activité (depuis la soumission)
     */
    public function evaluerActivite(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant' || $activity->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'note' => 'required|integer|min:0|max:20',
            'feedback' => 'nullable|string|max:2000',
        ]);

        // Créer ou mettre à jour l'évaluation
        $evaluation = Evaluation::updateOrCreate([
            'activity_id' => $activity->id,
            'stagiaire_id' => $activity->stagiaire_id,
            'encadrant_id' => $user->id,
            'type' => 'activite',
        ], [
            'note_globale' => $request->note,
            'appreciation' => $request->feedback,
            'date_evaluation' => now(),
            'statut' => 'validee',
        ]);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Évaluation enregistrée avec succès');
    }

    /**
     * Dashboard des évaluations pour le stagiaire
     */
    public function dashboardStagiaire()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'stagiaire') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $evaluations = Evaluation::where('stagiaire_id', $user->id)
            ->validees()
            ->with(['encadrant', 'activity'])
            ->latest()
            ->get();
        
        // Statistiques
        $stats = [
            'total' => $evaluations->count(),
            'activite' => $evaluations->where('type', 'activite')->count(),
            'generale' => $evaluations->where('type', 'generale')->count(),
            'finale' => $evaluations->where('type', 'finale')->count(),
            'moyenne_generale' => Evaluation::getMoyenneGenerale($user->id),
            'moyenne_activite' => Evaluation::getMoyenneGenerale($user->id, 'activite'),
            'derniere_evaluation' => Evaluation::getDerniereEvaluation($user->id),
        ];
        
        return view('stagiaire.evaluations.dashboard', compact('evaluations', 'stats'));
    }

    /**
     * Dashboard des évaluations pour l'encadrant
     */
    public function dashboardEncadrant()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $evaluations = Evaluation::where('encadrant_id', $user->id)
            ->with(['stagiaire', 'activity'])
            ->latest()
            ->get();
        
        $stagiaires = User::whereHas('role', fn($q) => $q->where('name', 'stagiaire'))
            ->where(function($query) use ($user) {
                $query->where('encadrant_id', $user->id)
                      ->orWhere('encadrant_faculte_id', $user->id)
                      ->orWhere('encadrant_entreprise_id', $user->id);
            })
            ->get();
        
        // Statistiques
        $stats = [
            'total' => $evaluations->count(),
            'brouillon' => $evaluations->where('statut', 'brouillon')->count(),
            'finalisees' => $evaluations->where('statut', 'finalisee')->count(),
            'validees' => $evaluations->where('statut', 'validee')->count(),
            'stagiaires_evalues' => $evaluations->pluck('stagiaire_id')->unique()->count(),
        ];
        
        return view('encadrant.evaluations.dashboard', compact('evaluations', 'stagiaires', 'stats'));
    }

    /**
     * API pour obtenir les statistiques d'évaluation
     */
    public function apiStats(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name === 'stagiaire') {
            $stats = [
                'moyenne_generale' => Evaluation::getMoyenneGenerale($user->id),
                'moyenne_activite' => Evaluation::getMoyenneGenerale($user->id, 'activite'),
                'total_evaluations' => Evaluation::where('stagiaire_id', $user->id)->validees()->count(),
            ];
        } elseif ($user->role->name === 'encadrant') {
            $stats = [
                'total_evaluations' => Evaluation::where('encadrant_id', $user->id)->count(),
                'evaluations_validees' => Evaluation::where('encadrant_id', $user->id)->validees()->count(),
                'stagiaires_evalues' => Evaluation::where('encadrant_id', $user->id)
                    ->pluck('stagiaire_id')->unique()->count(),
            ];
        } else {
            $stats = [];
        }
        
        return response()->json($stats);
    }

    /**
     * Créer une évaluation de l'organisation
     */
    public function createOrganisation(Request $request)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un stagiaire
        if ($user->role->name !== 'stagiaire') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        return view('evaluations.organisation.create', [
            'user' => $user,
            'evaluation_type' => 'organisation'
        ]);
    }

    /**
     * Créer une évaluation de l'encadrant
     */
    public function createEncadrant(Request $request)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un stagiaire
        if ($user->role->name !== 'stagiaire') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        return view('evaluations.encadrant.create', [
            'user' => $user,
            'evaluation_type' => 'encadrant'
        ]);
    }

    /**
     * Créer une auto-évaluation
     */
    public function createAuto(Request $request)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un stagiaire
        if ($user->role->name !== 'stagiaire') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        return view('evaluations.auto.create', [
            'user' => $user,
            'evaluation_type' => 'auto'
        ]);
    }

    /**
     * Créer une évaluation de stagiaire (pour l'encadrant)
     */
    public function createStagiaire(Request $request)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un encadrant
        if ($user->role->name !== 'encadrant') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        // Récupérer les stagiaires de l'encadrant
        $stagiaires = User::whereHas('role', fn($q) => $q->where('name', 'stagiaire'))
            ->where(function($query) use ($user) {
                $query->where('encadrant_id', $user->id)
                      ->orWhere('encadrant_faculte_id', $user->id)
                      ->orWhere('encadrant_entreprise_id', $user->id);
            })
            ->get();

        return view('evaluations.stagiaire.create', [
            'user' => $user,
            'stagiaires' => $stagiaires,
            'type' => 'stagiaire'
        ]);
    }

    /**
     * Afficher les auto-évaluations des stagiaires
     */
    public function indexAutoEvaluations(Request $request)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un encadrant
        if ($user->role->name !== 'encadrant') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        // Récupérer les stagiaires de l'encadrant
        $stagiaires = User::whereHas('role', fn($q) => $q->where('name', 'stagiaire'))
            ->where(function($query) use ($user) {
                $query->where('encadrant_id', $user->id)
                      ->orWhere('encadrant_faculte_id', $user->id)
                      ->orWhere('encadrant_entreprise_id', $user->id);
            })
            ->get();

        // Récupérer les auto-évaluations (évaluateur_id == stagiaire_id) pour ces stagiaires
        $autoEvaluations = Evaluation::whereIn('stagiaire_id', $stagiaires->pluck('id'))
            ->whereColumn('evaluateur_id', 'stagiaire_id') // Auto-évaluation = l'évaluateur est le stagiaire lui-même
            ->with(['stagiaire', 'evaluateur'])
            ->latest()
            ->get();

        return view('evaluations.auto.index', [
            'user' => $user,
            'autoEvaluations' => $autoEvaluations,
            'stagiaires' => $stagiaires
        ]);
    }
}
