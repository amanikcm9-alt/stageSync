<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EncadrantDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
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
}
