<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/*
 * CLASS REDONDANTE - NON NÉCESSAIRE
 * Raison : Les affectations encadrant-stagiaire sont gérées par le RH, pas l'admin
 * Alternative : RHUserController peut gérer les affectations
 * Date de mise en commentaire : 16/04/2026
 */
class AdminAssignmentController extends Controller
{
    
    // Lister toutes les affectations encadrant-stagiaire
    public function index()
    {
        $query = User::whereHas('role', function($q) {
            $q->where('name', 'stagiaire');
        })->with('encadrant', 'role');

        // Filtres
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtre par encadrant
        if (request('encadrant_id')) {
            $query->where('encadrant_id', request('encadrant_id'));
        }

        // Filtre par statut d'affectation
        if (request('assignment_status')) {
            if (request('assignment_status') === 'assigned') {
                $query->whereNotNull('encadrant_id');
            } else {
                $query->whereNull('encadrant_id');
            }
        }

        $stagiaires = $query->paginate(15);
        $encadrants = User::whereHas('role', function($q) {
            $q->where('name', 'encadrant');
        })->get();

        return view('rh.assignments.index', compact('stagiaires', 'encadrants'));
    }

    // Formulaire de création d'affectation
    public function create()
    {
        $stagiaires = User::whereHas('role', function($q) {
            $q->where('name', 'stagiaire');
        })->get();
        
        $encadrants = User::whereHas('role', function($q) {
            $q->where('name', 'encadrant');
        })->get();

        return view('rh.assignments.create', compact('stagiaires', 'encadrants'));
    }

    // Créer une affectation
    public function store(Request $request)
    {
        $request->validate([
            'stagiaire_id' => 'required|exists:users,id',
            'encadrant_id' => 'required|exists:users,id',
        ]);

        $stagiaire = User::findOrFail($request->stagiaire_id);
        
        if ($stagiaire->role->name !== 'stagiaire') {
            return redirect()->back()
                ->with('error', 'L\'utilisateur sélectionné n\'est pas un stagiaire');
        }

        $stagiaire->update([
            'encadrant_id' => $request->encadrant_id,
        ]);

        return redirect()->route('rh.assignments.index')
            ->with('success', 'Affectation créée avec succès');
    }

    // Modifier une affectation
    public function edit(User $stagiaire)
    {
        if ($stagiaire->role->name !== 'stagiaire') {
            abort(404);
        }

        $encadrants = User::whereHas('role', function($q) {
            $q->where('name', 'encadrant');
        })->get();

        return view('rh.assignments.edit', compact('stagiaire', 'encadrants'));
    }

    // Mettre à jour une affectation
    public function update(Request $request, User $stagiaire)
    {
        if ($stagiaire->role->name !== 'stagiaire') {
            abort(404);
        }

        $request->validate([
            'encadrant_id' => 'required|exists:users,id',
        ]);

        $stagiaire->update([
            'encadrant_id' => $request->encadrant_id,
        ]);

        return redirect()->route('rh.assignments.index')
            ->with('success', 'Affectation mise à jour avec succès');
    }

    // Supprimer une affectation
    public function destroy(User $stagiaire)
    {
        if ($stagiaire->role->name !== 'stagiaire') {
            abort(404);
        }

        $stagiaire->update([
            'encadrant_id' => null,
        ]);

        return redirect()->route('rh.assignments.index')
            ->with('success', 'Affectation supprimée avec succès');
    }

    // Affectation en masse
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'stagiaire_ids' => 'required|array',
            'stagiaire_ids.*' => 'exists:users,id',
            'encadrant_id' => 'required|exists:users,id',
        ]);

        $stagiaires = User::whereIn('id', $request->stagiaire_ids)
            ->whereHas('role', function($q) {
                $q->where('name', 'stagiaire');
            })->get();

        foreach ($stagiaires as $stagiaire) {
            $stagiaire->update(['encadrant_id' => $request->encadrant_id]);
        }

        return redirect()->route('rh.assignments.index')
            ->with('success', count($stagiaires) . ' stagiaires affectés avec succès');
    }
    */
}
