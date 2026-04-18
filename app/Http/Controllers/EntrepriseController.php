<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EntrepriseController extends Controller
{
    /**
     * Rôle : Gestion des entreprises partenaires pour RH/Admin
     * Responsabilités :
     * - CRUD des entreprises
     * - Activation/Désactivation
     * - Gestion des conditions de stage
     */

    public function index(Request $request)
    {
        // Récupérer la première entreprise (ou une entreprise spécifique)
        $entreprise = Entreprise::first();
        
        // Si aucune entreprise n'existe, en créer une par défaut
        if (!$entreprise) {
            $entreprise = new Entreprise([
                'nom' => 'Tech Innovation Solutions',
                'secteur' => 'Technologies de l\'Information',
                'adresse' => '123 Avenue des Technologies',
                'ville' => 'Paris',
                'pays' => 'France',
                'contact_nom' => 'Admin',
                'contact_prenom' => 'RH',
                'contact_email' => 'contact@entreprise.com',
                'contact_telephone' => '0123456789',
                'actif' => true,
            ]);
        }

        return view('admin.entreprises.index', compact('entreprise'));
    }

    public function create()
    {
        return view('admin.entreprises.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'secteur' => 'required|string|max:255',
            'adresse' => 'required|string',
            'ville' => 'required|string',
            'pays' => 'required|string',
            'contact_nom' => 'required|string|max:255',
            'contact_prenom' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_telephone' => 'nullable|string',
            'contact_fonction' => 'nullable|string|max:255',
            'site_web' => 'nullable|url',
            'reglement_interne' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'charte_graphique' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'procedures_securite' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'guide_informatique' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'regles_stagiaires' => 'nullable|string',
            'horaires_travail' => 'nullable|string',
            'dress_code' => 'nullable|string',
        ]);

        $data = $request->except(['reglement_interne', 'charte_graphique', 'procedures_securite', 'guide_informatique']);

        // Gestion des fichiers
        if ($request->hasFile('reglement_interne')) {
            $data['reglement_interne'] = $request->file('reglement_interne')->store('entreprises/reglements', 'public');
        }
        if ($request->hasFile('charte_graphique')) {
            $data['charte_graphique'] = $request->file('charte_graphique')->store('entreprises/chartes', 'public');
        }
        if ($request->hasFile('procedures_securite')) {
            $data['procedures_securite'] = $request->file('procedures_securite')->store('entreprises/procedures', 'public');
        }
        if ($request->hasFile('guide_informatique')) {
            $data['guide_informatique'] = $request->file('guide_informatique')->store('entreprises/guides', 'public');
        }

        $data['actif'] = true;

        $entreprise = Entreprise::create($data);

        return redirect()->route('admin.entreprises.index')
            ->with('success', 'Entreprise créée avec succès.');
    }

    public function show(Entreprise $entreprise)
    {
        $entreprise->load(['offres' => function($query) {
            $query->latest();
        }]);

        return view('admin.entreprises.show', compact('entreprise'));
    }

    public function edit(Entreprise $entreprise)
    {
        return view('admin.entreprises.edit', compact('entreprise'));
    }

    public function update(Request $request, Entreprise $entreprise)
    {
        // Debug : Afficher les données reçues
        \Log::info('Données reçues pour update entreprise', ['data' => $request->all()]);
        \Log::info('ID de l\'entreprise', ['id' => $entreprise->id]);
        
        try {
            $validatedData = $request->validate([
                'nom' => 'required|string|max:255',
                'secteur' => 'required|string|max:255',
                'adresse' => 'nullable|string',
                'ville' => 'nullable|string',
                'pays' => 'nullable|string',
                'contact_nom' => 'nullable|string|max:255',
                'contact_prenom' => 'nullable|string|max:255',
                'contact_email' => 'nullable|email',
                'contact_telephone' => 'nullable|string',
                'contact_fonction' => 'nullable|string|max:255',
                'site_web' => 'nullable|url',
                'reglement_interne' => 'nullable|string',
                'partager_reglement_stagiaires' => 'nullable|boolean',
                'reglement_interne_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                'charte_graphique' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                'procedures_securite' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                'guide_informatique' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                'regles_stagiaires' => 'nullable|string',
                'horaires_travail' => 'nullable|string',
                'dress_code' => 'nullable|string',
            ]);
            
            // Debug : Afficher les données validées
            \Log::info('Données validées avec succès', ['data' => $validatedData]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Debug : Afficher les erreurs de validation
            \Log::error('Erreurs de validation', ['errors' => $e->errors()]);
            \Log::error('Message validation', ['message' => $e->getMessage()]);
            
            // Renvoyer avec les erreurs
            $errorMessages = [];
            foreach ($e->errors() as $fieldErrors) {
                foreach ($fieldErrors as $error) {
                    $errorMessages[] = $error;
                }
            }
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Erreur de validation: ' . implode(', ', $errorMessages));
        }

        // Ajouter les valeurs par défaut si les champs sont vides
        if (empty($validatedData['ville'])) {
            $validatedData['ville'] = 'Paris';
        }
        if (empty($validatedData['pays'])) {
            $validatedData['pays'] = 'France';
        }
        if (empty($validatedData['contact_nom'])) {
            $validatedData['contact_nom'] = 'Admin';
        }
        if (empty($validatedData['contact_prenom'])) {
            $validatedData['contact_prenom'] = 'RH';
        }
        if (empty($validatedData['contact_email'])) {
            $validatedData['contact_email'] = 'contact@entreprise.com';
        }
        if (empty($validatedData['contact_telephone'])) {
            $validatedData['contact_telephone'] = '0123456789';
        }
        if (empty($validatedData['adresse'])) {
            $validatedData['adresse'] = '123 Avenue de la Tech';
        }
        if (empty($validatedData['contact_fonction'])) {
            $validatedData['contact_fonction'] = 'Responsable RH';
        }
        if (!isset($validatedData['partager_reglement_stagiaires'])) {
            $validatedData['partager_reglement_stagiaires'] = false;
        }

        // Gestion des fichiers
        if ($request->hasFile('reglement_interne')) {
            // Supprimer l'ancien fichier s'il existe
            if ($entreprise->reglement_interne) {
                Storage::disk('public')->delete($entreprise->reglement_interne);
                \Log::info('Ancien reglement_interne supprimé', ['file' => $entreprise->reglement_interne]);
            }
            $validatedData['reglement_interne'] = $request->file('reglement_interne')->store('entreprises/reglements', 'public');
            \Log::info('Nouveau reglement_interne stocké', ['file' => $validatedData['reglement_interne']]);
        }
        if ($request->hasFile('charte_graphique')) {
            if ($entreprise->charte_graphique) {
                Storage::disk('public')->delete($entreprise->charte_graphique);
                \Log::info('Ancien charte_graphique supprimé', ['file' => $entreprise->charte_graphique]);
            }
            $validatedData['charte_graphique'] = $request->file('charte_graphique')->store('entreprises/chartes', 'public');
            \Log::info('Nouveau charte_graphique stocké', ['file' => $validatedData['charte_graphique']]);
        }
        if ($request->hasFile('procedures_securite')) {
            if ($entreprise->procedures_securite) {
                Storage::disk('public')->delete($entreprise->procedures_securite);
                \Log::info('Ancien procedures_securite supprimé', ['file' => $entreprise->procedures_securite]);
            }
            $validatedData['procedures_securite'] = $request->file('procedures_securite')->store('entreprises/procedures', 'public');
            \Log::info('Nouveau procedures_securite stocké', ['file' => $validatedData['procedures_securite']]);
        }
        if ($request->hasFile('guide_informatique')) {
            if ($entreprise->guide_informatique) {
                Storage::disk('public')->delete($entreprise->guide_informatique);
                \Log::info('Ancien guide_informatique supprimé', ['file' => $entreprise->guide_informatique]);
            }
            $validatedData['guide_informatique'] = $request->file('guide_informatique')->store('entreprises/guides', 'public');
            \Log::info('Nouveau guide_informatique stocké', ['file' => $validatedData['guide_informatique']]);
        }

        // Debug : Afficher les données finales avant update
        \Log::info('Données finales avant update', ['data' => $validatedData]);

        try {
            $result = $entreprise->update($validatedData);
            \Log::info('Résultat update entreprise', ['result' => $result]);
            
            // Vérifier si c'est une requête RH pour rediriger correctement
            $uri = request()->getRequestUri();
            if (strpos($uri, '/rh/') === 0) {
                return redirect()->route('rh.entreprises.show', $entreprise)
                    ->with('success', 'Entreprise mise à jour avec succès.');
            }
            
            return redirect()->route('admin.entreprises.index')
                ->with('success', 'Entreprise mise à jour avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'update entreprise', ['error' => $e->getMessage()]);
            \Log::error('Stack trace', ['trace' => $e->getTraceAsString()]);
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function showReglement(Entreprise $entreprise)
    {
        // Vérifier si le règlement est partagé avec les stagiaires
        if (!$entreprise->reglement_interne || !$entreprise->partager_reglement_stagiaires) {
            abort(404, 'Règlement non disponible ou non partagé avec les stagiaires');
        }

        return view('entreprises.reglement', compact('entreprise'));
    }

    public function destroy(Entreprise $entreprise)
    {
        // Vérifier si l'entreprise a des offres
        if ($entreprise->offres()->count() > 0) {
            return redirect()->route('admin.entreprises.index')
                ->with('error', 'Impossible de supprimer cette entreprise car elle a des offres associées.');
        }

        // Supprimer les fichiers associés
        if ($entreprise->reglement_interne) {
            Storage::disk('public')->delete($entreprise->reglement_interne);
        }
        if ($entreprise->charte_graphique) {
            Storage::disk('public')->delete($entreprise->charte_graphique);
        }
        if ($entreprise->procedures_securite) {
            Storage::disk('public')->delete($entreprise->procedures_securite);
        }
        if ($entreprise->guide_informatique) {
            Storage::disk('public')->delete($entreprise->guide_informatique);
        }

        $entreprise->delete();

        return redirect()->route('admin.entreprises.index')
            ->with('success', 'Entreprise supprimée avec succès.');
    }

    public function activer(Entreprise $entreprise)
    {
        $entreprise->update(['actif' => true]);

        return redirect()->route('admin.entreprises.index')
            ->with('success', 'Entreprise activée avec succès.');
    }

    public function desactiver(Entreprise $entreprise)
    {
        $entreprise->update(['actif' => false]);

        return redirect()->route('admin.entreprises.index')
            ->with('success', 'Entreprise désactivée avec succès.');
    }
}
