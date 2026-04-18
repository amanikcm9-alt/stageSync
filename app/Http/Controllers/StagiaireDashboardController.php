<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Document;
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
            'en_retard' => $activities->filter(fn($a) => $a->estEnRetard())->count(),
        ];

        // Documents et supports
        $documents = Document::where(function($query) use ($stagiaire) {
                $query->where('offre_stage_id', $stagiaire->offre_stage_id)
                      ->orWhere('type', 'reglement');
            })
            ->publies()
            ->latest()
            ->get();

        return view('stagiaire.activities.dashboard', compact('activities', 'stats', 'documents'));
    }
}
