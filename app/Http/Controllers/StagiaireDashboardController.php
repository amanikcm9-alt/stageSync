<?php

namespace App\Http\Controllers;

// Importation des modèles nécessaires pour interagir avec la base de données
use App\Models\Activity;
use App\Models\Document;
use App\Models\OffreStage;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StagiaireDashboardController extends Controller
{
    // Constructeur : définit les règles d'accès au contrôleur
    public function __construct()
    {
        // Oblige l'utilisateur à être authentifié (connecté) pour accéder à ces fonctions
        $this->middleware('auth');
    }

    // Affiche la page d'accueil (Dashboard) du stagiaire
    public function index(Request $request)
    {
        // Récupère les informations de l'utilisateur actuellement connecté
        $stagiaire = Auth::user();
        
        // Récupère les activités assignées au stagiaire avec leurs relations (optimisation de la base de données)
        $activities = Activity::where('stagiaire_id', $stagiaire->id)
            ->whereNotIn('statut', ['archivée', 'demander_info']) // Exclure les activités archivées et en attente d'info
            ->with(['encadrant', 'submissions', 'documents']) // Charge aussi l'encadrant, les rendus et les docs
            ->latest() // Trie par les plus récentes
            ->get(); // Exécute la requête

        // Prépare un tableau de statistiques pour les compteurs de l'interface
        $stats = [
            'total' => $activities->count(), // Nombre total d'activités
            'en_cours' => $activities->where('statut', 'en_cours')->count(), // Activités non terminées
            'soumises' => $activities->where('statut', 'soumise')->count(), // En attente de correction
            'validees' => $activities->where('statut', 'validee')->count(), // Activités réussies
            'evaluations' => $activities->where('statut', 'validee')->count(), // Utilisé pour le suivi des notes
        ];

        // Récupère les documents utiles (soit liés à son stage, soit le règlement général)
        $documents = Document::where(function($query) use ($stagiaire) {
                $query->where('offre_stage_id', $stagiaire->offre_stage_id)
                      ->orWhere('type', 'reglement');
            })
            ->publies() // Filtre pour ne prendre que les documents officiellement publiés
            ->latest()
            ->get();

        // Récupère la liste de tous les utilisateurs ayant le rôle 'encadrant' (ID rôle 2)
        $encadrants = User::where('role_id', 2)
            ->select('id', 'nom', 'prenom')
            ->get();

        // Cherche les détails de l'offre de stage actuelle du stagiaire
        $offreStage = null;
        if ($stagiaire->offre_stage_id) {
            $offreStage = OffreStage::find($stagiaire->offre_stage_id);
        }

        // Tente de récupérer les notifications non lues du stagiaire
        $notifications = [];
        try {
            $notifications = Notification::where('destinataire_id', $stagiaire->id)
                ->whereNull('date_lecture') // Uniquement celles qui n'ont pas encore été lues
                ->with(['sender']) // Inclut l'expéditeur de la notification
                ->latest()
                ->get();
        } catch (\Exception $e) {
            // Si la table notification a un souci, on renvoie un tableau vide pour ne pas faire planter la page
            $notifications = [];
        }

        // Renvoie la vue 'dashboard' en lui passant toutes les variables calculées ci-dessus
        return view('stagiaire.activities.dashboard', compact('activities', 'stats', 'encadrants', 'documents', 'notifications', 'offreStage'));
    }

    // Affiche la liste complète et détaillée des activités
    public function activities(Request $request)
    {
        $stagiaire = Auth::user();
        
        // Récupère les activités actives (pas les simples propositions ni les archives terminées)
        $activities = Activity::where('stagiaire_id', $stagiaire->id)
            ->whereNotIn('statut', ['proposee', 'terminee', 'archivée'])
            ->where(function($query) {
                $query->whereNull('date_fin') // Activités sans date de fin
                      ->orWhere('date_fin', '>=', now()->toDateString()); // Ou pas encore expirées
            })
            ->with(['encadrant', 'submissions', 'documents'])
            ->latest()
            ->get();

        // Récupère les activités que les encadrants proposent au stagiaire (id non encore assigné)
        $proposedActivities = Activity::whereNull('stagiaire_id')
            ->where(function($query) use ($stagiaire) {
                $query->where('encadrant_id', $stagiaire->encadrant_id);
                // Inclut aussi les propositions des encadrants spécifiques (faculté ou entreprise)
                if ($stagiaire->encadrant_faculte_id) {
                    $query->orWhere('encadrant_id', $stagiaire->encadrant_faculte_id);
                }
                if ($stagiaire->encadrant_entreprise_id) {
                    $query->orWhere('encadrant_id', $stagiaire->encadrant_entreprise_id);
                }
            })
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now()->toDateString());
            })
            ->with(['encadrant'])
            ->latest()
            ->get();

        // Récupère les activités que le stagiaire a lui-même suggérées
        $myProposedActivities = Activity::where('stagiaire_id', $stagiaire->id)
            ->where('statut', 'proposee')
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now()->toDateString());
            })
            ->with(['encadrant'])
            ->latest()
            ->get();

        // Fusionne les propositions reçues et envoyées dans une seule collection
        $allProposedActivities = $proposedActivities->concat($myProposedActivities);

        // Affiche la vue 'index' avec les listes d'activités
        return view('stagiaire.activities.index', compact('activities', 'allProposedActivities'));
    }

    // Affiche le formulaire pour créer une nouvelle proposition d'activité
    public function proposeActivity()
    {
        $stagiaire = Auth::user();
        $encadrants = [];
        
        // Identifie les encadrants rattachés à ce stagiaire précis
        if ($stagiaire->encadrant_id) {
            $encadrants[] = User::find($stagiaire->encadrant_id);
        }
        if ($stagiaire->encadrant_faculte_id) {
            $encadrants[] = User::find($stagiaire->encadrant_faculte_id);
        }
        if ($stagiaire->encadrant_entreprise_id) {
            $encadrants[] = User::find($stagiaire->encadrant_entreprise_id);
        }
        
        // Nettoie la liste (enlève les vides et les doublons si un encadrant remplit deux rôles)
        $encadrants = collect($encadrants)->filter()->unique('id')->values();
        
        return view('stagiaire.activities.propose', compact('encadrants'));
    }

    // Enregistre la proposition du stagiaire dans la base de données
    public function storeProposedActivity(Request $request)
    {
        $stagiaire = Auth::user();
        
        // Vérifie que les données saisies par le stagiaire sont correctes
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'objectifs' => 'nullable|string',
            'date_limite' => 'nullable|date|after:today', // La date doit être dans le futur
            'encadrant_id' => 'required|exists:users,id', // L'encadrant doit exister en base
        ]);
        
        // Crée l'enregistrement dans la table 'activities'
        $activity = Activity::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'objectifs' => $request->objectifs,
            'date_limite' => $request->date_limite,
            'encadrant_id' => $request->encadrant_id,
            'stagiaire_id' => $stagiaire->id,
            'statut' => 'proposee', // Statut initial en attente
        ]);
        
        // Redirige vers la liste avec un message de succès
        return redirect()->route('stagiaire.activities.index')
            ->with('success', 'Activité proposée avec succès. En attente de validation par l\'encadrant.');
    }

    // Permet au stagiaire de refuser une proposition faite par un encadrant
    public function refuseActivity(Request $request, Activity $activity)
    {
        // Écrit dans les logs du serveur pour le suivi technique
        \Log::info('TEST DEBUG - refuseActivity APPELÉE');
        
        $stagiaire = Auth::user();
        
        // Sécurité : Vérifie que l'activité est bien destinée à ce stagiaire
        if ($activity->stagiaire_id && $activity->stagiaire_id !== $stagiaire->id) {
            return response()->json(['error' => 'Vous n\'êtes pas autorisé à refuser cette activité.'], 403);
        }
        
        // Sécurité : Vérifie si le statut actuel permet encore le refus
        if (in_array($activity->statut, ['terminee', 'validee', 'refusee'])) {
            return response()->json(['error' => "Cette activité ne peut plus être refusée."], 400);
        }
        
        // Valide que le stagiaire a bien donné une raison au refus
        $request->validate([
            'raison' => 'required|string|max:500'
        ]);
        
        // Met à jour manuellement la table pour enregistrer le refus et le commentaire
        $updated = \DB::table('activities')
            ->where('id', $activity->id)
            ->update([
                'statut' => 'refusee',
                'commentaires' => $request->raison,
                'updated_at' => now()
            ]);
            
        // Log pour confirmer le succès de l'opération en base
        \Log::info('TEST - Mise à jour terminée. Résultat: ' . ($updated ? 'SUCCÈS' : 'ÉCHEC'));
        
        // Gère la réponse selon si c'est une requête AJAX (JSON) ou un formulaire classique
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'L\'activité a été refusée avec succès.'
            ]);
        } else {
            return redirect()->back()->with('success', 'Activité refusée avec succès.');
        }
    }
}