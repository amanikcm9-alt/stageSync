<?php

namespace App\Http\Controllers;

use App\Models\OffreStage;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OffreStageController extends Controller
{
    /**
     * Rôle : Gestion CRUD des offres de stage pour RH/Admin
     * Responsabilités :
     * - Liste des offres avec filtres avancés
     * - Création/Modification/Suppression des offres
     * - Workflow : brouillon → publié → clôturé
     * - Gestion des entreprises associées
     */

    /**
     * Get the appropriate route name based on the current route prefix
     */
    private function getRouteName($route)
    {
        // Utiliser l'URL pour une détection plus fiable
        $uri = request()->getRequestUri();
        
        if (strpos($uri, '/rh/') === 0) {
            return "rh.offres.{$route}";
        }
        
        return "admin.offres.{$route}";
    }

    /**
     * Get the appropriate view path based on the current route prefix
     */
    private function getViewPath($view)
    {
        // Utiliser l'URL pour une détection plus fiable
        $uri = request()->getRequestUri();
        
        // Debug complet
        logger()->info('Current URI: ' . $uri);
        
        if (strpos($uri, '/rh/') === 0) {
            logger()->info('Using RH view: rh.offres.' . $view);
            return "rh.offres.{$view}";
        }
        
        logger()->info('Using Admin view: admin.offres.' . $view);
        return "admin.offres.{$view}";
    }

    public function index(Request $request)
    {
        $query = OffreStage::with('entreprise', 'rh', 'secteur', 'typeStage');

        // Par défaut, afficher uniquement les offres publiées:
        // Afficher uniquement les offres actuellement actives: Déjà commencées , Pas encore terminées


        if (!$request->filled('statut')) {
            $query->where('statut', 'publiee');
        }

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('missions', 'like', "%{$search}%");
            });
        }

        if ($request->filled('secteur_id')) {
            $query->where('secteur_id', $request->secteur_id);
        }

        if ($request->filled('statut')) {
            // Gérer les différents statuts possibles
            $statut = $request->statut;
            if ($statut === 'affectée' || $statut === 'affectee') {
                $query->where('statut', 'affectée');
            } else {
                $query->where('statut', $statut);
            }
        }

        if ($request->filled('entreprise_id')) {
            $query->where('entreprise_id', $request->entreprise_id);
        }

        if ($request->filled('type_stage_id')) {
            $query->where('type_stage_id', $request->type_stage_id);
        }

        if ($request->filled('remuneration')) {
            if ($request->remuneration === 'remunere') {
                $query->renumere();
            } elseif ($request->remuneration === 'non_renumere') {
                $query->nonRenumere();
            }
        }

        $offres = $query->latest()->paginate(15);
        $entreprises = Entreprise::active()->pluck('nom', 'id');
        $secteurs = $this->getSecteursDisponibles();
        $statuts = ['brouillon' => 'Brouillon', 'publiee' => 'Publiée', 'cloturee' => 'Clôturée'];

        return view($this->getViewPath('index'), compact('offres', 'entreprises', 'secteurs', 'statuts'));
    }

    public function create()
    {
        $entreprises = Entreprise::active()->get();
        $secteurs = $this->getSecteursDisponibles();
        
        $viewPath = $this->getViewPath('create');
        logger()->info('Create view path: ' . $viewPath);
        
        return view($viewPath, compact('entreprises', 'secteurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'missions' => 'required|string',
            'secteur_id' => 'required|exists:secteurs,id',
            'lieu' => 'required|string|max:255',
            'type_stage_id' => 'required|exists:type_stages,id',
            'remuneration' => 'nullable|numeric|min:0|max:9999.99',
            'date_debut' => 'nullable|date|after_or_equal:today',
            'date_fin' => 'nullable|date|after:date_debut',
            'statut' => 'required|in:brouillon,publiee,cloturee'
        ], [
            'secteur_id.exists' => 'Le secteur sélectionné n\'est pas valide.',
            'type_stage_id.exists' => 'Le type de stage sélectionné n\'est pas valide.',
            'remuneration.max' => 'La rémunération ne peut pas dépasser 9999.99€.'
        ]);

        $offre = OffreStage::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'missions' => $request->missions,
            'secteur_id' => $request->secteur_id,
            'lieu' => $request->lieu,
            'type_stage_id' => $request->type_stage_id,
            'remuneration' => $request->remuneration,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'rh_id' => auth()->id(),
            'statut' => $request->statut
        ]);

        return redirect()->route('rh.offres')
            ->with('success', 'L\'offre de stage a été créée avec succès.');
    }

    public function show(OffreStage $offre)
    {
        $offre->load('entreprise', 'rh', 'candidatures');
        $candidaturesCount = $offre->candidatures()->count();
        $secteurs = $this->getSecteursDisponibles();
        
        return view($this->getViewPath('show'), compact('offre', 'candidaturesCount', 'secteurs'));
    }

    public function edit(OffreStage $offre)
    {
        $entreprises = Entreprise::active()->get();
        $secteurs = $this->getSecteursDisponibles();
        
        return view($this->getViewPath('edit'), compact('offre', 'entreprises', 'secteurs'));
    }

    public function update(Request $request, OffreStage $offre)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'missions' => 'required|string',
            'secteur' => 'required|string|in:' . implode(',', array_keys($this->getSecteursDisponibles())),
            'lieu' => 'required|string|max:255',
            'duree_semaines' => 'required|integer|min:1|max:52',
            'remuneration' => 'nullable|numeric|min:0|max:9999.99',
            'date_debut' => 'nullable|date|after_or_equal:today',
            'date_fin' => 'nullable|date|after:date_debut',
            'entreprise_id' => 'required|exists:entreprises,id',
            'statut' => 'required|in:brouillon,publiee,cloturee'
        ]);

        $offre->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'missions' => $request->missions,
            'secteur' => $request->secteur,
            'lieu' => $request->lieu,
            'duree_semaines' => $request->duree_semaines,
            'remuneration' => $request->remuneration,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'entreprise_id' => $request->entreprise_id,
            'statut' => $request->statut
        ]);

        return redirect()->route($this->getRouteName('index'))
            ->with('success', 'L\'offre de stage a été mise à jour avec succès.');
    }

    public function destroy(OffreStage $offre)
    {
        // Vérifier s'il y a des candidatures
        if ($offre->candidatures()->count() > 0) {
            return redirect()->route('rh.offres')
                ->with('error', 'Impossible de supprimer cette offre car elle a des candidatures associées.');
        }

        $offre->delete();

        return redirect()->route('rh.offres')
            ->with('success', 'L\'offre de stage a été supprimée avec succès.');
    }

    // Actions spécifiques
    public function publier(OffreStage $offre)
    {
        $offre->update([
            'statut' => 'publiee',
            'date_debut' => $offre->date_debut ?? now()
        ]);

        return redirect()->back()
            ->with('success', 'L\'offre a été publiée avec succès.');
    }

    public function cloturer(Request $request, OffreStage $offre)
    {
        $request->validate([
            'notification_cloture' => 'required|string|max:1000',
            'notifier_candidats' => 'sometimes|boolean'
        ]);

        $offre->update([
            'statut' => 'cloturee',
            'date_fin' => now(),
            'notification_cloture' => $request->notification_cloture
        ]);

        // Notifier les candidats si demandé
        if ($request->boolean('notifier_candidats')) {
            $candidats = $offre->candidatures()->with('user')->get();
            
            foreach ($candidats as $candidature) {
                if ($candidature->user) {
                    // TODO: Envoyer un email de notification
                    // Mail::to($candidature->user->email)->send(new OffreClotureeNotification($offre, $request->notification_cloture));
                }
            }
        }

        return redirect()->back()
            ->with('success', 'L\'offre a été clôturée avec succès. ' . 
                ($request->boolean('notifier_candidats') ? 'Les candidats ont été notifiés.' : ''));
    }

    // Méthodes utilitaires
    private function getSecteursDisponibles()
    {
        return [
            'banque' => 'Banque/Finance',
            'full-stack' => 'Développement Full-Stack',
            'digital-marketing' => 'Digital Marketing',
            'pfe' => 'Projet de Fin d\'Études',
            'perfectionnement' => 'Perfectionnement',
            'initiation' => 'Stage d\'Initiation',
            'data-science' => 'Data Science/IA',
            'design' => 'Design UX/UI',
            'marketing' => 'Marketing Traditionnel',
            'vente' => 'Vente/Commercial',
            'rh' => 'Ressources Humaines',
            'logistique' => 'Logistique/Supply Chain',
            'autre' => 'Autre'
        ];
    }
}
