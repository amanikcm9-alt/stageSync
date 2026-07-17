<?php

namespace App\Http\Controllers;

use App\Models\Secteur;
use Illuminate\Http\Request;

class AdminSecteurController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    
    //Méthode store() → Ajouter un secteur

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:secteurs,nom',
            'description' => 'nullable|string|max:1000',
            'actif' => 'boolean'
        ]);

        $secteur = Secteur::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'actif' => $request->boolean('actif', true)
        ]);

        // Si c'est une requête AJAX, retourner JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Secteur créé avec succès.',
                'secteur' => $secteur
            ]);
        }

        return redirect()
            ->route('admin.entreprises.edit', request('entreprise_id'))
            ->with('success', 'Secteur créé avec succès.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Secteur $secteur)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:secteurs,nom,' . $secteur->id,
            'description' => 'nullable|string|max:1000',
            'actif' => 'boolean'
        ]);

        $secteur->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'actif' => $request->boolean('actif', true)
        ]);

        // Si c'est une requête AJAX, retourner JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Secteur mis à jour avec succès.',
                'secteur' => $secteur
            ]);
        }

        return redirect()
            ->route('admin.entreprises.edit', request('entreprise_id'))
            ->with('success', 'Secteur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Secteur $secteur)
    {
        // Vérifier si des offres utilisent ce secteur
        if ($secteur->offres()->count() > 0) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer ce secteur car il est utilisé par des offres de stage.'
                ]);
            }
            
            return redirect()
                ->route('admin.entreprises.edit', request('entreprise_id'))
                ->with('error', 'Impossible de supprimer ce secteur car il est utilisé par des offres de stage.');
        }

        $secteur->delete();
        
        // Si c'est une requête AJAX, retourner JSON
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Secteur supprimé avec succès.'
            ]);
        }
        
        return redirect()
            ->route('admin.entreprises.edit', request('entreprise_id'))
            ->with('success', 'Secteur supprimé avec succès.');
    }
}
