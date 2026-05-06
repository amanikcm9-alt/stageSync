<?php

namespace App\Http\Controllers;

use App\Models\Secteur;
use Illuminate\Http\Request;

class SecteurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $secteurs = Secteur::withCount('offres')
            ->orderBy('nom')
            ->get();
        
        return view('rh.secteurs.index', compact('secteurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('rh.secteurs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Secteur créé avec succès.',
                'secteur' => $secteur
            ]);
        }

        return redirect()
            ->route('rh.secteurs.index')
            ->with('success', 'Secteur créé avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Secteur $secteur)
    {
        return view('rh.secteurs.edit', compact('secteur'));
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
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Secteur mis à jour avec succès.',
                'secteur' => $secteur
            ]);
        }

        return redirect()
            ->route('rh.secteurs.index')
            ->with('success', 'Secteur mis à jour avec succès.');
    }

    /**
     * Archive the specified resource.
     */
    public function archive(Secteur $secteur)
    {
        $secteur->archiver();
        
        return redirect()
            ->route('rh.secteurs.index')
            ->with('success', 'Secteur archivé avec succès.');
    }

    /**
     * Restore the specified resource.
     */
    public function restore(Secteur $secteur)
    {
        $secteur->desarchiver();
        
        return redirect()
            ->route('rh.secteurs.index')
            ->with('success', 'Secteur restauré avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Secteur $secteur)
    {
        // Vérifier si des offres utilisent ce secteur
        if ($secteur->offres()->count() > 0) {
            return redirect()
                ->route('rh.secteurs.index')
                ->with('error', 'Impossible de supprimer ce secteur car il est utilisé par des offres de stage.');
        }

        $secteur->delete();
        
        return redirect()
            ->route('rh.secteurs.index')
            ->with('success', 'Secteur supprimé avec succès.');
    }
}
