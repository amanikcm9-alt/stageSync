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

        // Statistiques
        $stats = [
            'total' => $activities->count(),
            'en_cours' => $activities->where('statut', 'en_cours')->count(),
            'soumises' => $activities->where('statut', 'soumise')->count(),
            'validees' => $activities->where('statut', 'validee')->count(),
            'en_retard' => $activities->filter(fn($a) => $a->estEnRetard())->count(),
        ];

        // Documents et supports
        $documents = \App\Models\Document::where(function($query) use ($stagiaire) {
                $query->where('offre_stage_id', $stagiaire->offre_stage_id)
                      ->orWhere('type', 'reglement');
            })
            ->publies()
            ->latest()
            ->get();

        return view('stagiaire.activities.dashboard', compact('activities', 'stats', 'documents'));
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
            ->with(['activities' => fn($q) => $q->where('encadrant_id', $encadrant->id)])
            ->get();

        // Statistiques
        $stats = [
            'total_activities' => $activities->count(),
            'en_cours' => $activities->where('statut', 'en_cours')->count(),
            'soumises' => $activities->where('statut', 'soumise')->count(),
            'validees' => $activities->where('statut', 'validee')->count(),
            'total_stagiaires' => $stagiaires->count(),
        ];

        return view('encadrant.activities.dashboard', compact('activities', 'stagiaires', 'stats'));
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
                
            return view('encadrant.activities.index', compact('activities'));
        }
        
        return redirect()->route('activities.dashboard');
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
        
        if ($user->role->name !== 'encadrant' || $activity->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $activity->delete();
        
        return redirect()->route('activities.index')
            ->with('success', 'Activité supprimée avec succès');
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
        
        // Créer une notification automatique à l'encadrant
        Discussion::create([
            'activity_id' => $activity->id,
            'sender_id' => $user->id,
            'receiver_id' => $activity->encadrant_id,
            'message' => "Le stagiaire a commencé l'activité '{$activity->titre}'",
            'type' => 'acceptation',
            'read' => false
        ]);
        
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
        
        if ($user->role->name !== 'encadrant' || $activity->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'stagiaire_id' => 'required|exists:users,id',
        ]);
        
        $activity->assignerAuStagiaire($request->stagiaire_id);
        
        return redirect()->back()->with('success', 'Activité assignée au stagiaire');
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
}
