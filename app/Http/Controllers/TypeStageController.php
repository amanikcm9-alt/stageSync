<?php

namespace App\Http\Controllers;

use App\Models\TypeStage;
use Illuminate\Http\Request;

class TypeStageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $typeStages = TypeStage::withCount('offres')
            ->orderBy('nom')
            ->get();
        
        return view('rh.type-stages.index', compact('typeStages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('rh.type-stages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:type_stages,nom',
            'code' => 'nullable|string|max:50|unique:type_stages,code|alpha_dash',
            'description' => 'nullable|string|max:1000',
            'actif' => 'boolean'
        ]);

        TypeStage::create([
            'nom' => $request->nom,
            'code' => $request->code,
            'description' => $request->description,
            'actif' => $request->boolean('actif', true)
        ]);

        return redirect()
            ->route('rh.type-stages.index')
            ->with('success', 'Type de stage créé avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeStage $typeStage)
    {
        return view('rh.type-stages.edit', compact('typeStage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeStage $typeStage)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:type_stages,nom,' . $typeStage->id,
            'code' => 'nullable|string|max:50|unique:type_stages,code,' . $typeStage->id . '|alpha_dash',
            'description' => 'nullable|string|max:1000',
            'actif' => 'boolean'
        ]);

        $typeStage->update([
            'nom' => $request->nom,
            'code' => $request->code,
            'description' => $request->description,
            'actif' => $request->boolean('actif', true)
        ]);

        return redirect()
            ->route('rh.type-stages.index')
            ->with('success', 'Type de stage mis à jour avec succès.');
    }

    /**
     * Archive the specified resource.
     */
    public function archive(TypeStage $typeStage)
    {
        $typeStage->archiver();
        
        return redirect()
            ->route('rh.type-stages.index')
            ->with('success', 'Type de stage archivé avec succès.');
    }

    /**
     * Restore the specified resource.
     */
    public function restore(TypeStage $typeStage)
    {
        $typeStage->desarchiver();
        
        return redirect()
            ->route('rh.type-stages.index')
            ->with('success', 'Type de stage restauré avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeStage $typeStage)
    {
        // Vérifier si des offres utilisent ce type de stage
        if ($typeStage->offres()->count() > 0) {
            return redirect()
                ->route('rh.type-stages.index')
                ->with('error', 'Impossible de supprimer ce type de stage car il est utilisé par des offres de stage.');
        }

        $typeStage->delete();
        
        return redirect()
            ->route('rh.type-stages.index')
            ->with('success', 'Type de stage supprimé avec succès.');
    }
}
