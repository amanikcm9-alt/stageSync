<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Candidature;
use App\Models\OffreStage;
use Illuminate\Http\Request;

/**
 * RHAssignmentController - Gestion des affectations encadrant-stagiaire par le RH
 * Rôle : Permettre au RH de gérer les affectations entre encadrants et stagiaires
 */
class RHAssignmentController extends Controller
{
    /**
     * Étape 1: Filtrer automatiquement les stagiaires acceptés sans encadrant
     */
    public function index()
    {
        // Uniquement les stagiaires acceptés sans encadrant
        $query = User::whereHas('role', function($q) {
                $q->where('name', 'stagiaire');
            })
            ->whereHas('candidature', function($q) {
                $q->where('statut', 'accepte');
            })
            ->whereNull('encadrant_id')
            ->with(['candidature.offreStage.secteur', 'candidature.offreStage.typeStage']);

        // Filtre de recherche
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $stagiaires = $query->paginate(10);

        return view('rh.affectation.index', compact('stagiaires'));
    }

    /**
     * Étape 2: Afficher les encadrants filtrés pour un stagiaire
     */
    public function showEncadrants($stagiaireId)
    {
        $stagiaire = User::whereHas('role', function($q) {
                $q->where('name', 'stagiaire');
            })
            ->whereHas('candidature', function($q) {
                $q->where('statut', 'accepte');
            })
            ->whereNull('encadrant_id')
            ->with(['candidature.offreStage.secteur', 'candidature.offreStage.typeStage'])
            ->findOrFail($stagiaireId);

        // Filtrer intelligemment les encadrants
        $encadrantsQuery = User::whereHas('role', function($q) {
                $q->where('name', 'encadrant');
            })
            ->with(['secteur', 'stagiairesAffectes']);

        // Filtrer par secteur du stagiaire
        if ($stagiaire->candidature->offreStage->secteur_id) {
            $encadrantsQuery->where('secteur_id', $stagiaire->candidature->offreStage->secteur_id);
        }

        $encadrants = $encadrantsQuery->get()->map(function($encadrant) {
            // Étape 3: Afficher des infos utiles
            return [
                'id' => $encadrant->id,
                'nom' => $encadrant->nom,
                'prenom' => $encadrant->prenom,
                'email' => $encadrant->email,
                'secteur' => $encadrant->secteur ? $encadrant->secteur->nom : 'Non défini',
                'specialite' => $encadrant->secteur ? $encadrant->secteur->nom : 'Non défini',
                'nombre_stagiaires' => $encadrant->stagiairesAffectes->count(),
                'disponibilite' => $encadrant->stagiairesAffectes->count() < 5 ? 'Disponible' : 'Charge élevée',
                'couleur_disponibilite' => $encadrant->stagiairesAffectes->count() < 5 ? 'success' : 'warning',
            ];
        });

        return view('rh.affectation.encadrants', compact('stagiaire', 'encadrants'));
    }

    /**
     * Affecter un encadrant à un stagiaire
     */
    public function assign(Request $request, $stagiaireId)
    {
        $request->validate([
            'encadrant_id' => 'required|exists:users,id',
        ]);

        $stagiaire = User::whereHas('role', function($q) {
                $q->where('name', 'stagiaire');
            })
            ->whereHas('candidature', function($q) {
                $q->where('statut', 'accepte');
            })
            ->whereNull('encadrant_id')
            ->findOrFail($stagiaireId);

        $encadrant = User::whereHas('role', function($q) {
                $q->where('name', 'encadrant');
            })->findOrFail($request->encadrant_id);

        // Vérifier la compatibilité secteur
        if ($stagiaire->candidature->offreStage->secteur_id && 
            $encadrant->secteur_id && 
            $stagiaire->candidature->offreStage->secteur_id !== $encadrant->secteur_id) {
            
            return redirect()->back()
                ->with('error', 'L\'encadrant sélectionné n\'est pas dans le même secteur que l\'offre du stagiaire.');
        }

        $stagiaire->update([
            'encadrant_id' => $encadrant->id,
        ]);

        // Mettre à jour le statut de la candidature vers 'affecté'
        $candidature = $stagiaire->candidature;
        if ($candidature) {
            $candidature->update(['statut' => 'affecté']);
        }

        return redirect()->route('rh.affectation.index')
            ->with('success', "Le stagiaire {$stagiaire->nom} {$stagiaire->prenom} a été affecté à {$encadrant->nom} {$encadrant->prenom} avec succès.");
    }

    /**
     * API pour récupérer les encadrants filtrés (AJAX)
     */
    public function getEncadrantsForStagiaire($stagiaireId)
    {
        $stagiaire = User::whereHas('role', function($q) {
                $q->where('name', 'stagiaire');
            })
            ->whereHas('candidature', function($q) {
                $q->where('statut', 'accepte');
            })
            ->whereNull('encadrant_id')
            ->with(['candidature.offreStage.secteur'])
            ->findOrFail($stagiaireId);

        $encadrantsQuery = User::whereHas('role', function($q) {
                $q->where('name', 'encadrant');
            })
            ->with(['secteur', 'stagiairesAffectes']);

        // Filtrer par secteur du stagiaire
        if ($stagiaire->candidature->offreStage->secteur_id) {
            $encadrantsQuery->where('secteur_id', $stagiaire->candidature->offreStage->secteur_id);
        }

        $encadrants = $encadrantsQuery->get()->map(function($encadrant) {
            return [
                'id' => $encadrant->id,
                'nom' => $encadrant->nom,
                'prenom' => $encadrant->prenom,
                'email' => $encadrant->email,
                'secteur' => $encadrant->secteur ? $encadrant->secteur->nom : 'Non défini',
                'specialite' => $encadrant->secteur ? $encadrant->secteur->nom : 'Non défini',
                'nombre_stagiaires' => $encadrant->stagiairesAffectes->count(),
                'disponibilite' => $encadrant->stagiairesAffectes->count() < 5 ? 'Disponible' : 'Charge élevée',
                'couleur_disponibilite' => $encadrant->stagiairesAffectes->count() < 5 ? 'success' : 'warning',
            ];
        });

        return response()->json($encadrants);
    }
}
