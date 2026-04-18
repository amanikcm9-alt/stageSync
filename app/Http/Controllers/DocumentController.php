<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Activity;
use App\Models\OffreStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des documents
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Document::with(['uploader', 'activity'])
            ->latest();
        
        // Filtrer selon le rôle
        if ($user->role->name === 'stagiaire') {
            $query->where(function($q) use ($user) {
                $q->where('offre_stage_id', $user->offre_stage_id)
                  ->orWhere('type', 'reglement')
                  ->orWhere('uploaded_by', $user->id);
            });
        } elseif ($user->role->name === 'encadrant') {
            $query->where(function($q) use ($user) {
                $q->where('uploaded_by', $user->id)
                  ->orWhereHas('activity', fn($a) => $a->where('encadrant_id', $user->id))
                  ->orWhere('type', 'reglement');
            });
        }
        
        // Filtrer par type
        if ($request->type) {
            $query->where('type', $request->type);
        }
        
        $documents = $query->paginate(15);
        
        return view('documents.index', compact('documents'));
    }

    /**
     * Afficher le formulaire de création de document
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Seuls les encadrants peuvent créer des supports
        if ($user->role->name !== 'encadrant' && $user->role->name !== 'admin') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $activities = [];
        if ($user->role->name === 'encadrant') {
            $activities = Activity::where('encadrant_id', $user->id)->get();
        }
        
        $types = [
            'support' => 'Support pédagogique',
            'documentation' => 'Documentation',
            'fiche_jointe' => 'Fiche jointe',
        ];
        
        return view('documents.create', compact('activities', 'types'));
    }

    /**
     * Enregistrer un nouveau document
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'encadrant' && $user->role->name !== 'admin') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:support,documentation,fiche_jointe',
            'fichier' => 'nullable|file|max:10240', // 10MB
            'url' => 'nullable|url',
            'activity_id' => 'nullable|exists:activities,id',
        ]);

        $filePath = null;
        
        if ($request->hasFile('fichier')) {
            $filePath = $request->file('fichier')->store('documents', 'public');
        }

        $document = Document::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'type' => $request->type,
            'fichier_path' => $filePath,
            'url' => $request->url,
            'uploaded_by' => $user->id,
            'activity_id' => $request->activity_id,
            'statut' => 'publie',
        ]);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document publié avec succès');
    }

    /**
     * Afficher un document
     */
    public function show(Document $document)
    {
        $user = Auth::user();
        
        // Marquer comme lu si c'est un règlement
        if ($document->type === 'reglement' && $user->role->name === 'stagiaire') {
            $document->marquerCommeLu($user->id);
        }
        
        $document->load(['uploader', 'activity']);
        
        return view('documents.show', compact('document'));
    }

    /**
     * Afficher le formulaire d'édition de document
     */
    public function edit(Document $document)
    {
        $user = Auth::user();
        
        if ($user->id !== $document->uploaded_by && $user->role->name !== 'admin') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $activities = [];
        if ($user->role->name === 'encadrant') {
            $activities = Activity::where('encadrant_id', $user->id)->get();
        }
        
        $types = [
            'support' => 'Support pédagogique',
            'documentation' => 'Documentation',
            'fiche_jointe' => 'Fiche jointe',
        ];
        
        return view('documents.edit', compact('document', 'activities', 'types'));
    }

    /**
     * Mettre à jour un document
     */
    public function update(Request $request, Document $document)
    {
        $user = Auth::user();
        
        if ($user->id !== $document->uploaded_by && $user->role->name !== 'admin') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'url' => 'nullable|url',
            'activity_id' => 'nullable|exists:activities,id',
        ]);

        $document->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'url' => $request->url,
            'activity_id' => $request->activity_id,
        ]);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document mis à jour avec succès');
    }

    /**
     * Supprimer un document
     */
    public function destroy(Document $document)
    {
        $user = Auth::user();
        
        if ($user->id !== $document->uploaded_by && $user->role->name !== 'admin') {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        // Supprimer le fichier
        if ($document->fichier_path) {
            Storage::disk('public')->delete($document->fichier_path);
        }
        
        $document->delete();
        
        return redirect()->route('documents.index')
            ->with('success', 'Document supprimé avec succès');
    }

    /**
     * Télécharger un document
     */
    public function telecharger(Document $document)
    {
        $user = Auth::user();
        
        // Vérifier les droits d'accès
        if (!$document->estAccessible()) {
            return redirect()->back()->with('error', 'Document non accessible');
        }
        
        if (!$document->estTelechargeable()) {
            return redirect()->back()->with('error', 'Ce document n\'est pas téléchargeable');
        }
        
        if (!Storage::disk('public')->exists($document->fichier_path)) {
            return redirect()->back()->with('error', 'Fichier non trouvé');
        }
        
        return Storage::disk('public')->download($document->fichier_path, $document->titre);
    }

    /**
     * Consulter les règlements internes
     */
    public function reglements()
    {
        $user = Auth::user();
        
        $reglements = Document::reglements()
            ->publies()
            ->with(['uploader'])
            ->latest()
            ->get();
        
        // Marquer les règlements comme lus pour le stagiaire
        if ($user->role->name === 'stagiaire') {
            foreach ($reglements as $reglement) {
                $reglement->marquerCommeLu($user->id);
            }
        }
        
        return view('documents.reglements', compact('reglements'));
    }

    /**
     * Afficher les supports d'une activité
     */
    public function supportsActivite(Activity $activity)
    {
        $user = Auth::user();
        
        // Vérifier les droits d'accès
        if ($user->role->name === 'stagiaire' && $activity->stagiaire_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        if ($user->role->name === 'encadrant' && $activity->encadrant_id !== $user->id) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $supports = Document::supports()
            ->where('activity_id', $activity->id)
            ->publies()
            ->with(['uploader'])
            ->latest()
            ->get();
        
        return view('documents.supports-activite', compact('activity', 'supports'));
    }

    /**
     * Publier un règlement interne (RH/Admin)
     */
    public function createReglement()
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['admin', 'rh'])) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        return view('documents.create-reglement');
    }

    public function storeReglement(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['admin', 'rh'])) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'contenu' => 'required|string',
            'fichier' => 'nullable|file|max:10240', // 10MB
        ]);

        $filePath = null;
        
        if ($request->hasFile('fichier')) {
            $filePath = $request->file('fichier')->store('reglements', 'public');
        }

        $document = Document::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'type' => 'reglement',
            'fichier_path' => $filePath,
            'url' => null,
            'uploaded_by' => $user->id,
            'statut' => 'publie',
        ]);

        return redirect()->route('documents.reglements')
            ->with('success', 'Règlement publié avec succès');
    }

    /**
     * API pour vérifier si un document est lu
     */
    public function checkLu(Document $document)
    {
        $user = Auth::user();
        
        $estLu = $document->estLu($user->id);
        $dateLecture = $document->getDateLecture($user->id);
        
        return response()->json([
            'lu' => $estLu,
            'date_lecture' => $dateLecture,
        ]);
    }

    /**
     * Marquer un document comme lu
     */
    public function marquerLu(Document $document)
    {
        $user = Auth::user();
        
        if ($document->type !== 'reglement' || $user->role->name !== 'stagiaire') {
            return response()->json(['error' => 'Action non autorisée'], 403);
        }
        
        $document->marquerCommeLu($user->id);
        
        return response()->json([
            'success' => true,
            'date_lecture' => now()->toISOString(),
        ]);
    }
}
