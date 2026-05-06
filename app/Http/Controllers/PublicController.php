<?php

namespace App\Http\Controllers;

use App\Models\OffreStage;
use App\Models\Entreprise;
use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/*
 * CLASS REDONDANTE - NON NÉCESSAIRE
 * Raison : Fonctionnalités peuvent être intégrées dans OffreStageController
 * Alternative : Séparation inutile entre public et RH
 * Date de mise en commentaire : 16/04/2026
 */
class PublicController extends Controller
{
    /**
     * Rôle : Interface publique pour les candidats
     * Responsabilités :
     * - Accueil avec dernières offres et statistiques
     * - Liste des offres avec filtres par secteur
     * - Détail d'une offre avec formulaire de candidature
     * - Traitement des candidatures avec upload de documents
     * - Affichage des entreprises partenaires
     */

    public function accueil()
    {
        // Dernières offres publiées
        $dernieresOffres = OffreStage::with('entreprise')
            ->publiee()
            ->latest()
            ->take(6)
            ->get();

        // Statistiques pour l'accueil
        $stats = [
            'total_offres' => OffreStage::publiee()->count(),
            'total_entreprises' => Entreprise::active()->count(),
            'total_candidatures' => Candidature::count(),
            'secteurs' => $this->getSecteursDisponibles(),
            'offres_par_secteur' => $this->getOffresParSecteur()
        ];

        return view('public.accueil', compact('dernieresOffres', 'stats'));
    }

    public function offres(Request $request)
    {
        $query = OffreStage::with('entreprise')->publiee();

        // Filtre par secteur
        if ($request->filled('secteur')) {
            $query->where('secteur', $request->secteur);
        }

        // Filtre par recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('missions', 'like', "%{$search}%")
                  ->orWhere('lieu', 'like', "%{$search}%");
            });
        }

        // Filtre par type de stage
        if ($request->filled('type_stage')) {
            $query->where('description', 'like', "%{$request->type_stage}%");
        }

        $offres = $query->latest()->paginate(12);
        $secteurs = $this->getSecteursDisponibles();
        $typesStage = $this->getTypesStage();

        return view('public.offres', compact('offres', 'secteurs', 'typesStage'));
    }

    public function showOffre(OffreStage $offre)
    {
        // Vérifier que l'offre est publiée et active
        if (!$offre->estPublieeEtActive()) {
            abort(404);
        }

        $offre->load('entreprise');
        $autresOffres = OffreStage::with('entreprise')
            ->where('id', '!=', $offre->id)
            ->publiee()
            ->active()
            ->take(4)
            ->get();

        return view('public.offre-detail', compact('offre', 'autresOffres'));
    }

    /**
     * Retourner les détails d'une offre en JSON pour la modale
     */
    public function getOffreDetails(OffreStage $offre)
    {
        // Charger l'offre avec ses relations
        $offre->load('entreprise');
        
        return response()->json([
            'success' => true,
            'offre' => [
                'id' => $offre->id,
                'titre' => $offre->titre,
                'description' => $offre->description,
                'missions' => $offre->missions,
                'secteur' => $offre->secteur,
                'lieu' => $offre->lieu,
                'duree_semaines' => $offre->duree_semaines,
                'remuneration' => $offre->remuneration,
                'date_debut' => $offre->date_debut,
                'date_fin' => $offre->date_fin,
                'type_stage' => $offre->type_stage,
                'statut' => $offre->statut,
                'entreprise' => $offre->entreprise ? [
                    'id' => $offre->entreprise->id,
                    'nom' => $offre->entreprise->nom,
                ] : null,
            ]
        ]);
    }

    /**
     * Retourner les offres disponibles en JSON
     */
    public function getOffresDisponibles()
    {
        $offres = OffreStage::with('entreprise')
            ->where('statut', 'publié')
            ->latest()
            ->get()
            ->map(function ($offre) {
                return [
                    'id' => $offre->id,
                    'titre' => $offre->titre,
                    'entreprise' => $offre->entreprise ? $offre->entreprise->nom : null,
                ];
            });

        return response()->json([
            'success' => true,
            'offres' => $offres
        ]);
    }

    public function formCandidature(OffreStage $offre)
    {
        // Vérifier que l'offre est publiée et active
        if (!$offre->estPublieeEtActive()) {
            abort(404);
        }

        $offre->load('entreprise');
        
        return view('public.candidature-form', compact('offre'));
    }

    public function soumettreCandidature(Request $request, OffreStage $offre)
    {
        // Vérifier que l'offre est publiée et active
        if (!$offre->estPublieeEtActive()) {
            abort(404);
        }

        // Validation complète du formulaire
        $request->validate([
            // Informations personnelles
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:500',
            
            // Formation (optionnels)
            'etablissement' => 'nullable|string|max:255',
            'formation' => 'nullable|string|max:255',
            
            // Documents
            'cv' => 'required|file|mimes:pdf|max:5120',
            'lettre_motivation' => 'nullable|string|max:2000',
            
            // Message optionnel
            'message' => 'nullable|string|max:1000'
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'cv.required' => 'Le CV est obligatoire.',
            'cv.mimes' => 'Le CV doit être au format PDF.',
            'cv.max' => 'Le CV ne doit pas dépasser 5MB.',
            'lettre_motivation.string' => 'La lettre de motivation doit être du texte.',
            'lettre_motivation.max' => 'La lettre de motivation ne doit pas dépasser 2000 caractères.'
        ]);

        // Upload des documents
        $cvPath = $request->file('cv')->store('cvs', 'public');
        
        // La lettre de motivation est maintenant du texte, pas un fichier
        $lettreTexte = $request->lettre_motivation;

        // Création de la candidature
        $candidature = Candidature::create([
            // Informations personnelles
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            
            // Formation (optionnels)
            'etablissement' => $request->etablissement,
            'formation' => $request->formation,
            
            // Documents
            'cv_path' => $cvPath,
            'lettre_motivation' => $lettreTexte,
            
            // Offre
            'offre_stage_id' => $offre->id,
            
            // Message
            'message' => $request->message,
            
            // Statut
            'statut' => 'recue'
        ]);

        return redirect()->route('offres.show', $offre)
            ->with('success', 'Votre candidature a été soumise avec succès ! Vous recevrez une réponse par SMS.');
    }

    public function apropos()
    {
        // Récupérer l'entreprise principale (la première ou celle avec un statut spécial)
        $entreprise = Entreprise::first();
        
        // Statistiques pour la page à propos
        $stats = [
            'total_offres' => OffreStage::publiee()->count(),
            'total_candidatures' => Candidature::count(),
            'total_stagiaires' => \App\Models\User::whereHas('role', function($query) {
                $query->where('name', 'stagiaire');
            })->count(),
            'annee_creation' => '2020' // À adapter selon vos besoins
        ];

        return view('public.apropos', compact('entreprise', 'stats'));
    }

    public function entreprises()
    {
        $entreprises = Entreprise::active()
            ->withCount('offres')
            ->orderBy('nom')
            ->paginate(12);

        return view('public.entreprises', compact('entreprises'));
    }

    public function showEntreprise(Entreprise $entreprise)
    {
        // Vérifier que l'entreprise est active
        if (!$entreprise->active) {
            abort(404);
        }

        $entreprise->load(['offres' => function($query) {
            $query->publiee()->active()->latest();
        }]);

        return view('public.entreprise-detail', compact('entreprise'));
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

    private function getTypesStage()
    {
        return [
            'classique' => 'Stage Classique',
            'alternance' => 'Alternance',
            'pfe' => 'PFE',
            'volontariat' => 'Volontariat',
            'service-civique' => 'Service Civique'
        ];
    }

    private function getNombreOffresBySecteur($secteur)
    {
        // Compter les offres par secteur en utilisant la colonne secteur
        return OffreStage::where('secteur', $secteur)
            ->publiee()
            ->active()
            ->count();
    }

    public static function getNombreOffresBySecteurStatic($secteur)
    {
        // Compter les offres par secteur en utilisant la colonne secteur
        return OffreStage::where('secteur', $secteur)
            ->publiee()
            ->active()
            ->count();
    }

    private function getOffresParSecteur()
    {
        $secteurs = $this->getSecteursDisponibles();
        $offresParSecteur = [];
        
        foreach ($secteurs as $key => $secteur) {
            $offresParSecteur[$key] = $this->getNombreOffresBySecteur($key);
        }
        
        return $offresParSecteur;
    }
}
