<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * RHAssignmentController - Gestion des affectations encadrant-stagiaire par le RH
 * Rôle : Permettre au RH de gérer les affectations entre encadrants et stagiaires
 */
class RHAssignmentController extends Controller
{
    /**
     * Lister toutes les affectations encadrant-stagiaire
     */
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

    /**
     * Formulaire de création d'affectation
     */
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

    /**
     * Créer une affectation
     */
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

    /**
     * Afficher les détails d'une affectation
     */
    public function show($id)
    {
        $stagiaire = User::findOrFail($id);
        
        if ($stagiaire->role->name !== 'stagiaire') {
            abort(404);
        }

        $stagiaire->load('encadrant', 'role');
        
        return view('rh.assignments.show', compact('stagiaire'));
    }

    /**
     * Modifier une affectation
     */
    public function edit($id)
    {
        $stagiaire = User::findOrFail($id);
        
        if ($stagiaire->role->name !== 'stagiaire') {
            abort(404);
        }

        $encadrants = User::whereHas('role', function($q) {
            $q->where('name', 'encadrant');
        })->get();

        return view('rh.assignments.edit', compact('stagiaire', 'encadrants'));
    }

    /**
     * Mettre à jour une affectation
     */
    public function update(Request $request, $id)
    {
        $stagiaire = User::findOrFail($id);
        
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

    /**
     * Supprimer une affectation
     */
    public function destroy($id)
    {
        $stagiaire = User::findOrFail($id);
        
        if ($stagiaire->role->name !== 'stagiaire') {
            abort(404);
        }

        $stagiaire->update([
            'encadrant_id' => null,
        ]);

        return redirect()->route('rh.assignments.index')
            ->with('success', 'Affectation supprimée avec succès');
    }
}
