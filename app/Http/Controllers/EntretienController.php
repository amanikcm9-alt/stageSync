<?php

namespace App\Http\Controllers;

use App\Models\Entretien;
use App\Models\Candidature;
use Illuminate\Http\Request;

class EntretienController extends Controller
{
    /**
     * Afficher la liste des entretiens
     */
    public function index(Request $request)
    {
        $query = Entretien::with(['candidature.offreStage.entreprise', 'evaluateur']);

        // Par défaut, afficher uniquement les entretiens dont la candidature n'est ni acceptée ni refusée
        if (!$request->filled('statut')) {
            $query->whereHas('candidature', function($q) {
                $q->whereNotIn('statut', ['accepte', 'refuse']);
            });
        } else {
            $query->where('statut', $request->statut);
        }

        // Filtre par évaluation
        if ($request->filled('evaluation')) {
            if ($request->evaluation === 'non_evalue') {
                $query->nonEvalue();
            } elseif ($request->evaluation === 'evalue') {
                $query->evalue();
            }
        }

        // Filtres de date
        if ($request->filled('date_filter')) {
            if ($request->date_filter === 'commencees') {
                // Déjà commencées (date <= aujourd'hui)
                $query->where('date_entretien', '<=', now()->format('Y-m-d'));
            } elseif ($request->date_filter === 'non_terminees') {
                // Pas encore terminées (date >= aujourd'hui)
                $query->where('date_entretien', '>=', now()->format('Y-m-d'));
            }
        }

        $entretiens = $query->orderBy('date_entretien', 'desc')
                           ->orderBy('heure_entretien', 'desc')
                           ->paginate(15);

        $statuts = [
            'planifie' => 'Planifié',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé'
        ];

        return view('rh.entretiens.index', compact('entretiens', 'statuts'));
    }

    /**
     * Afficher les détails d'un entretien
     */
    public function show(Entretien $entretien)
    {
        $entretien->load(['candidature.offreStage.entreprise', 'evaluateur']);
        
        return view('rh.entretiens.show', compact('entretien'));
    }

    /**
     * Évaluer un entretien
     */
    public function evaluer(Request $request, Entretien $entretien)
    {
        $request->validate([
            'note_evaluation' => 'required|numeric|min:0|max:20',
            'commentaires_evaluation' => 'required|string|max:2000',
            'decision' => 'required|in:accepter,refuser,attente'
        ]);

        // Mettre à jour l'évaluation
        $entretien->update([
            'note_evaluation' => $request->note_evaluation,
            'commentaires_evaluation' => $request->commentaires_evaluation,
            'evaluated_by' => auth()->id(),
            'evaluated_at' => now()
        ]);

        // Appliquer la décision sur la candidature
        $candidature = $entretien->candidature;
        
        if ($request->decision === 'accepter') {
            // Mettre à jour la candidature comme acceptée
            $candidature->update([
                'statut' => 'accepte',
                'date_decision' => now(),
                'commentaire' => 'Accepté suite à entretien. Note: ' . $request->note_evaluation . '/20'
            ]);

            // Mettre à jour le statut de l'offre
            $offre = $candidature->offreStage;
            if ($offre) {
                $offre->update(['statut' => 'affectee']);
            }

            // Supprimer l'entretien de la liste après décision
            $entretien->delete();

            return redirect()->route('rh.candidatures.index')
                ->with('success', 'Candidature acceptée avec succès ! Entretien supprimé de la liste.');

        } elseif ($request->decision === 'refuser') {
            // Mettre à jour la candidature comme refusée
            $candidature->update([
                'statut' => 'refuse',
                'date_decision' => now(),
                'commentaire' => 'Refusé suite à entretien. Note: ' . $request->note_evaluation . '/20'
            ]);

            // Supprimer l'entretien de la liste après décision
            $entretien->delete();

            return redirect()->route('rh.candidatures.index')
                ->with('success', 'Candidature refusée avec succès ! Entretien supprimé de la liste.');

        } else {
            // En attente - ne change pas le statut de la candidature
            return redirect()->route('rh.entretiens.show', $entretien)
                ->with('success', 'Évaluation enregistrée. Candidature mise en attente.');
        }
    }

    /**
     * Planifier un entretien
     */
    public function planifier(Request $request, Candidature $candidature)
    {
        $request->validate([
            'date_entretien' => 'required|date|after:today',
            'heure_entretien' => 'required|date_format:H:i',
            'lieu_entretien' => 'required|string|max:255',
            'notes_entretien' => 'nullable|string|max:1000'
        ]);

        // Créer l'entretien
        $entretien = Entretien::create([
            'candidature_id' => $candidature->id,
            'date_entretien' => $request->date_entretien,
            'heure_entretien' => $request->date_entretien . ' ' . $request->heure_entretien,
            'lieu_entretien' => $request->lieu_entretien,
            'notes_entretien' => $request->notes_entretien,
            'statut' => Entretien::STATUT_PLANIFIE
        ]);

        // Mettre à jour la candidature avec les infos d'entretien
        $candidature->update([
            'date_entretien' => $request->date_entretien,
            'heure_entretien' => $request->date_entretien . ' ' . $request->heure_entretien,
            'lieu_entretien' => $request->lieu_entretien,
            'notes_entretien' => $request->notes_entretien
        ]);

        return redirect()->route('rh.candidatures.index')
            ->with('success', 'Entretien planifié avec succès !');
    }

    /**
     * Marquer un entretien comme terminé
     */
    public function terminer(Entretien $entretien)
    {
        // Supprimer l'entretien de la liste après clôture
        $entretien->delete();

        return redirect()->route('rh.candidatures.index')
            ->with('success', 'Entretien clôturé et supprimé de la liste.');
    }

    /**
     * Supprimer un entretien
     */
    public function destroy(Entretien $entretien)
    {
        $entretien->delete();

        return redirect()->route('rh.candidatures.index')
            ->with('success', 'Entretien supprimé avec succès.');
    }
}
