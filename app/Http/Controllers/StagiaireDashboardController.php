<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Document;
use App\Models\OffreStage;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StagiaireDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
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
            'evaluations' => $activities->where('statut', 'validee')->count(),
        ];

        // Documents et supports
        $documents = Document::where(function($query) use ($stagiaire) {
                $query->where('offre_stage_id', $stagiaire->offre_stage_id)
                      ->orWhere('type', 'reglement');
            })
            ->publies()
            ->latest()
            ->get();

        // Encadrants disponibles
        $encadrants = User::where('role_id', 2)
            ->select('id', 'nom', 'prenom')
            ->get();

        // Offre de stage du stagiaire
        $offreStage = null;
        if ($stagiaire->offre_stage_id) {
            $offreStage = OffreStage::find($stagiaire->offre_stage_id);
        }

        // Notifications non lues
        $notifications = [];
        try {
            $notifications = Notification::where('destinataire_id', $stagiaire->id)
                ->whereNull('date_lecture')
                ->with(['sender'])
                ->latest()
                ->get();
        } catch (\Exception $e) {
            // En cas d'erreur, on retourne un tableau vide
            $notifications = [];
        }

        return view('stagiaire.activities.dashboard', compact('activities', 'stats', 'encadrants', 'documents', 'notifications', 'offreStage'));
    }

    /**
     * Afficher la liste des activités du stagiaire
     */
    public function activities(Request $request)
    {
        $stagiaire = Auth::user();
        
        // Activités assignées au stagiaire
        $activities = Activity::where('stagiaire_id', $stagiaire->id)
            ->with(['encadrant', 'submissions', 'documents'])
            ->latest()
            ->get();

        // Activités proposées par les encadrants
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
}
