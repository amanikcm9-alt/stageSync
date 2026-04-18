<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivitySubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ActivitySubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher le formulaire de soumission pour une activité
     */
    public function create(Activity $activity)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'stagiaire' || $activity->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        // Vérifier si une soumission existe déjà
        $submission = ActivitySubmission::where('activity_id', $activity->id)
            ->where('stagiaire_id', $user->id)
            ->first();
            
        if ($submission) {
            return redirect()->route('submissions.edit', $submission);
        }
        
        return view('stagiaire.submissions.create', compact('activity'));
    }

    /**
     * Enregistrer une nouvelle soumission
     */
    public function store(Request $request, Activity $activity)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'stagiaire' || $activity->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'description_travail' => 'required|string|max:2000',
            'commentaires_stagiaire' => 'nullable|string|max:1000',
            'fichiers' => 'nullable|array',
            'fichiers.*' => 'file|max:10240', // 10MB max par fichier
        ]);

        $fichiersJoints = [];
        
        if ($request->hasFile('fichiers')) {
            foreach ($request->file('fichiers') as $fichier) {
                $path = $fichier->store('submissions/' . $activity->id, 'public');
                $fichiersJoints[] = $path;
            }
        }

        $submission = ActivitySubmission::create([
            'activity_id' => $activity->id,
            'stagiaire_id' => $user->id,
            'description_travail' => $request->description_travail,
            'commentaires_stagiaire' => $request->commentaires_stagiaire,
            'statut' => $request->action === 'soumettre' ? 'soumis' : 'brouillon',
            'fichiers_joints' => $fichiersJoints,
        ]);

        if ($request->action === 'soumettre') {
            $submission->soumettre();
            return redirect()->route('activities.show', $activity)
                ->with('success', 'Activité soumise avec succès');
        }

        return redirect()->route('submissions.edit', $submission)
            ->with('success', 'Brouillon enregistré');
    }

    /**
     * Afficher une soumission
     */
    public function show(ActivitySubmission $submission)
    {
        $user = Auth::user();
        
        // Vérifier les droits d'accès
        if ($user->role->name === 'stagiaire' && $submission->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        if ($user->role->name === 'encadrant' && $submission->activity->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $submission->load(['activity', 'stagiaire']);
        
        return view('submissions.show', compact('submission'));
    }

    /**
     * Afficher le formulaire d'édition de soumission
     */
    public function edit(ActivitySubmission $submission)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'stagiaire' || $submission->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        if (!$submission->peutEtreModifie()) {
            return redirect()->back()->with('error', 'Cette soumission ne peut plus être modifiée');
        }
        
        $submission->load('activity');
        
        return view('stagiaire.submissions.edit', compact('submission'));
    }

    /**
     * Mettre à jour une soumission
     */
    public function update(Request $request, ActivitySubmission $submission)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'stagiaire' || $submission->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        if (!$submission->peutEtreModifie()) {
            return redirect()->back()->with('error', 'Cette soumission ne peut plus être modifiée');
        }
        
        $request->validate([
            'description_travail' => 'required|string|max:2000',
            'commentaires_stagiaire' => 'nullable|string|max:1000',
            'fichiers' => 'nullable|array',
            'fichiers.*' => 'file|max:10240',
        ]);

        $fichiersJoints = $submission->fichiers_joints ?? [];
        
        if ($request->hasFile('fichiers')) {
            foreach ($request->file('fichiers') as $fichier) {
                $path = $fichier->store('submissions/' . $submission->activity_id, 'public');
                $fichiersJoints[] = $path;
            }
        }

        $submission->update([
            'description_travail' => $request->description_travail,
            'commentaires_stagiaire' => $request->commentaires_stagiaire,
            'fichiers_joints' => $fichiersJoints,
        ]);

        if ($request->action === 'soumettre') {
            $submission->soumettre();
            return redirect()->route('activities.show', $submission->activity)
                ->with('success', 'Activité soumise avec succès');
        }

        return redirect()->route('submissions.edit', $submission)
            ->with('success', 'Brouillon mis à jour');
    }

    /**
     * Supprimer une soumission
     */
    public function destroy(ActivitySubmission $submission)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'stagiaire' || $submission->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        if (!$submission->peutEtreModifie()) {
            return redirect()->back()->with('error', 'Cette soumission ne peut plus être supprimée');
        }
        
        // Supprimer les fichiers
        if ($submission->fichiers_joints) {
            foreach ($submission->fichiers_joints as $fichier) {
                Storage::disk('public')->delete($fichier);
            }
        }
        
        $submission->delete();
        
        return redirect()->route('activities.show', $submission->activity)
            ->with('success', 'Soumission supprimée avec succès');
    }

    /**
     * Télécharger un fichier de soumission
     */
    public function telechargerFichier(ActivitySubmission $submission, $index)
    {
        $user = Auth::user();
        
        // Vérifier les droits d'accès
        if ($user->role->name === 'stagiaire' && $submission->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        if ($user->role->name === 'encadrant' && $submission->activity->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $fichiers = $submission->fichiers_joints ?? [];
        
        if (!isset($fichiers[$index])) {
            return redirect()->back()->with('error', 'Fichier non trouvé');
        }
        
        $filePath = $fichiers[$index];
        
        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->back()->with('error', 'Fichier non trouvé');
        }
        
        return Storage::disk('public')->download($filePath);
    }

    /**
     * Supprimer un fichier de soumission
     */
    public function supprimerFichier(ActivitySubmission $submission, $index)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'stagiaire' || $submission->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        if (!$submission->peutEtreModifie()) {
            return redirect()->back()->with('error', 'Cette soumission ne peut plus être modifiée');
        }
        
        $fichiers = $submission->fichiers_joints ?? [];
        
        if (!isset($fichiers[$index])) {
            return redirect()->back()->with('error', 'Fichier non trouvé');
        }
        
        $filePath = $fichiers[$index];
        
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        
        $submission->supprimerFichier($filePath);
        
        return redirect()->back()->with('success', 'Fichier supprimé avec succès');
    }

    /**
     * Actions de l'encadrant sur une soumission
     */
    public function mettreEnEvaluation(ActivitySubmission $submission)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant' || $submission->activity->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $submission->mettreEnEvaluation();
        
        return redirect()->back()->with('success', 'Soumission mise en évaluation');
    }

    public function valider(Request $request, ActivitySubmission $submission)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant' || $submission->activity->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'note' => 'required|integer|min:0|max:20',
            'feedback' => 'nullable|string|max:2000',
        ]);
        
        $submission->valider($request->note, $request->feedback);
        
        return redirect()->back()->with('success', 'Soumission validée avec succès');
    }

    public function refuser(Request $request, ActivitySubmission $submission)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant' || $submission->activity->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'justification' => 'required|string|max:1000',
        ]);
        
        $submission->refuser($request->justification);
        
        return redirect()->back()->with('success', 'Soumission refusée');
    }
}
