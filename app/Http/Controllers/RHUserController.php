<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\OffreStage;
use App\Models\Candidature;
use App\Models\Secteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RHUserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs (uniquement stagiaires et encadrants pour le RH)
     */
    public function index(Request $request)
    {
        // Récupérer les rôles de manière sécurisée
        $stagiaireRole = Role::where('name', 'stagiaire')->first();
        $encadrantRole = Role::where('name', 'encadrant')->first();
        
        $roleIds = [];
        if ($stagiaireRole) $roleIds[] = $stagiaireRole->id;
        if ($encadrantRole) $roleIds[] = $encadrantRole->id;
        
        if (empty($roleIds)) {
            Log::error("ERREUR: Aucun rôle trouvé (stagiaire ou encadrant)");
            return view('rh.users.index', ['users' => collect()]);
        }
        
        $users = User::with('role')
            ->whereIn('role_id', $roleIds)
            ->when($request->search, function($query, $search) {
                return $query->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->role, function($query, $role) {
                if ($role === 'stagiaire') {
                    $stagiaireRole = Role::where('name', 'stagiaire')->first();
                    if ($stagiaireRole) {
                        $query->where('role_id', $stagiaireRole->id);
                    }
                } elseif ($role === 'encadrant') {
                    $encadrantRole = Role::where('name', 'encadrant')->first();
                    if ($encadrantRole) {
                        $query->where('role_id', $encadrantRole->id);
                    }
                }
            })
            ->when($request->status, function($query, $status) {
                if ($status === 'active') {
                    $query->where('active', true);
                } elseif ($status === 'inactive') {
                    $query->where('active', false);
                } elseif ($status === 'en_cours') {
                    // Stagiaires et encadrants selon leur statut d'affectation
                    $query->where(function($q) {
                        // Stagiaires - essayer plusieurs conditions
                        $q->whereHas('candidature', function($subQ) {
                            $subQ->whereIn('statut', ['affecté', 'affectee', 'accepte']);
                        })
                        // Stagiaires avec encadrant_id non null (alternative)
                        ->orWhere(function($subQ) {
                            $subQ->whereHas('role', function($roleQ) {
                                $roleQ->where('name', 'stagiaire');
                            })->whereNotNull('encadrant_id');
                        })
                        // Encadrants avec stagiaires affectés
                        ->orWhereHas('stagiairesAffectes', function($subQ) {
                            $subQ->whereNotNull('encadrant_id');
                        });
                    });
                } elseif ($status === 'autres') {
                    // Encadrants sans affectation (généralement)
                    $query->where(function($q) {
                        $q->whereHas('role', function($roleQ) {
                            $roleQ->where('name', 'encadrant');
                        })
                        ->whereDoesntHave('stagiairesAffectes');
                    });
                }
            }, function($query) {
                // Par défaut, appliquer le filtre "en cours"
                $query->where(function($q) {
                    // Stagiaires - essayer plusieurs conditions
                    $q->whereHas('candidature', function($subQ) {
                        $subQ->whereIn('statut', ['affecté', 'affectee', 'accepte']);
                    })
                    // Stagiaires avec encadrant_id non null (alternative)
                    ->orWhere(function($subQ) {
                        $subQ->whereHas('role', function($roleQ) {
                            $roleQ->where('name', 'stagiaire');
                        })->whereNotNull('encadrant_id');
                    })
                    // Encadrants avec stagiaires affectés
                    ->orWhereHas('stagiairesAffectes', function($subQ) {
                        $subQ->whereNotNull('encadrant_id');
                    });
                });
            })
            ->latest()
            ->paginate(15);

        // Log pour débogage
        Log::info("Recherche utilisateurs RH - Total: " . $users->total() . 
                  ", Rôles trouvés: " . implode(',', $roleIds) . 
                  ", Filtres: " . json_encode($request->only(['search', 'role', 'status'])));
        
        // Compter les stagiaires pour vérification
        if ($stagiaireRole) {
            $stagiaireCount = User::where('role_id', $stagiaireRole->id)->count();
            Log::info("Nombre total de stagiaires en base: {$stagiaireCount}");
        }

        return view('rh.users.index', compact('users'));
    }

    /**
     * Afficher le formulaire de création d'utilisateur
     */
    public function create()
    {
        // Récupérer les offres de stage disponibles avec eager loading
        $offres = OffreStage::with('entreprise')
                               ->where('statut', 'publiee')
                               ->orderBy('titre')
                               ->get();
        
        // Récupérer les secteurs disponibles pour les encadrants
        $secteurs = \App\Models\Secteur::where('actif', true)
                                       ->orderBy('nom')
                                       ->get();
        
        return view('rh.users.create', compact('offres', 'secteurs'));
    }

    /**
     * Stocker un nouvel utilisateur (uniquement stagiaire ou encadrant)
     */
    public function store(Request $request)
    {
        try {
            // Logging pour débogage
            \Log::info('RHUserController@store - Début de la création d\'utilisateur');
            \Log::info('Données reçues: ' . json_encode($request->all()));
            
            // Vérifier l'existence des rôles
            $stagiaireRole = Role::where('name', 'stagiaire')->first();
            $encadrantRole = Role::where('name', 'encadrant')->first();
            
            \Log::info('Rôle stagiaire trouvé: ' . ($stagiaireRole ? 'ID ' . $stagiaireRole->id : 'NON'));
            \Log::info('Rôle encadrant trouvé: ' . ($encadrantRole ? 'ID ' . $encadrantRole->id : 'NON'));
            
            // Afficher tous les rôles disponibles pour débogage
            $allRoles = Role::all()->pluck('name')->toArray();
            \Log::info('Tous les rôles disponibles: ' . json_encode($allRoles));
            
            $rules = [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'telephone' => 'nullable|string|max:20',
                'role' => 'required|in:stagiaire,encadrant',
                'offre_id' => 'nullable|exists:offre_stages,id',
            ];

            // Ajouter la règle secteur_id obligatoire pour les encadrants
            if ($request->role === 'encadrant') {
                $rules['secteur_id'] = 'required|exists:secteurs,id';
            } else {
                $rules['secteur_id'] = 'nullable|exists:secteurs,id';
            }

            $validatedData = $request->validate($rules);

            \Log::info('Validation réussie: ' . json_encode($validatedData));

            // Récupérer le rôle
            $role = Role::where('name', $validatedData['role'])->first();
            \Log::info('Rôle recherché: ' . $validatedData['role']);
            \Log::info('Rôle trouvé: ' . ($role ? $role->id : 'null'));

            if (!$role) {
                \Log::error('Rôle non trouvé: ' . $validatedData['role']);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Rôle non valide: ' . $validatedData['role']);
            }

            \Log::info('Création de l\'utilisateur...');
            
            $user = User::create([
                'nom' => $validatedData['nom'],
                'prenom' => $validatedData['prenom'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'telephone' => $validatedData['telephone'] ?? null,
                'role_id' => $role->id,
                'secteur_id' => $validatedData['secteur_id'] ?? null,
                'email_verified_at' => now(),
            ]);

            \Log::info('Utilisateur créé avec ID: ' . $user->id);

            // Si c'est un stagiaire, créer une candidature associée et lier l'offre
            if ($validatedData['role'] === 'stagiaire' && isset($validatedData['offre_id'])) {
                \Log::info('Création de la candidature pour le stagiaire...');
                
                // Mettre à jour le champ offre_stage_id du stagiaire
                $user->offre_stage_id = $validatedData['offre_id'];
                $user->save();
                \Log::info('offre_stage_id du stagiaire mis à jour: ' . $validatedData['offre_id']);
                
                // Mettre à jour le statut de l'offre à 'affectée'
                $offre = OffreStage::find($validatedData['offre_id']);
                if ($offre) {
                    $offre->statut = 'affectée';
                    $offre->save();
                    \Log::info('Offre ID ' . $validatedData['offre_id'] . ' mise à jour avec le statut "affectée"');
                }
                
                Candidature::create([
                    'nom' => $validatedData['nom'],
                    'prenom' => $validatedData['prenom'],
                    'date_naissance' => now()->subYears(20), // Date par défaut (20 ans)
                    'email' => $validatedData['email'],
                    'telephone' => $validatedData['telephone'] ?? 'Non renseigné',
                    'adresse' => 'Adresse à compléter',
                    'dernier_diplome' => 'À compléter',
                    'etablissement' => 'Établissement à compléter',
                    'annee_diplome' => now()->year - 1, // Année par défaut
                    'cv_path' => '',
                    'lettre_motivation_path' => '',
                    'lettre_motivation' => 'Stagiaire ajouté directement par le RH - offre associée par défaut',
                    'offre_stage_id' => $validatedData['offre_id'],
                    'message' => 'Stagiaire ajouté directement par le RH',
                    'statut' => 'accepte',
                    'date_decision' => now(),
                    'commentaire' => 'Stagiaire pré-sélectionné par le RH',
                    'stagiaire_id' => $user->id, // Lier au stagiaire créé
                ]);
                
                \Log::info('Candidature créée pour le stagiaire et offre liée');
            } elseif ($validatedData['role'] === 'encadrant') {
                \Log::info('Création d\'un encadrant - aucune candidature créée');
            } else {
                \Log::warning('Rôle non reconnu: ' . $validatedData['role']);
            }

            \Log::info('Redirection vers rh.users.index avec succès');
            
            return redirect()->route('rh.users.index')
                ->with('success', 'Utilisateur créé avec succès.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation: ' . json_encode($e->errors()));
            $errorMessage = 'Erreur de validation: ';
            foreach ($e->errors() as $field => $messages) {
                $errorMessage .= implode(', ', $messages) . ' ';
            }
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', trim($errorMessage));
                
        } catch (\Exception $e) {
            \Log::error('Exception lors de la création: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'un utilisateur
     */
    public function show(User $user)
    {
        // Charger la relation role pour éviter les erreurs
        $user->load('role');
        
        // Vérifier que le RH peut voir cet utilisateur
        if (!in_array($user->role->name, ['stagiaire', 'encadrant', 'rh', 'admin'])) {
            abort(403, 'Accès non autorisé');
        }

        $user->load('role', 'encadrant', 'stagiaires');
        
        // Si c'est un stagiaire, récupérer ou créer sa candidature et l'offre associée
        $candidature = null;
        $offre = null;
        
        if ($user->role->name === 'stagiaire') {
            $candidature = Candidature::where('email', $user->email)
                                      ->with('offreStage.entreprise')
                                      ->first();
            
            // Si le stagiaire n'a pas de candidature, en créer une par défaut
            if (!$candidature) {
                // Récupérer la première offre disponible comme offre par défaut
                $offreParDefaut = OffreStage::where('statut', 'publiee')
                                               ->orderBy('titre')
                                               ->first();
                
                if ($offreParDefaut) {
                    $candidature = Candidature::create([
                        'nom' => $user->nom,
                        'prenom' => $user->prenom,
                        'date_naissance' => now()->subYears(20), // Date par défaut (20 ans)
                        'email' => $user->email,
                        'telephone' => $user->telephone ?? 'Non renseigné',
                        'adresse' => 'Adresse à compléter',
                        'dernier_diplome' => 'À compléter',
                        'etablissement' => 'Établissement à compléter',
                        'annee_diplome' => now()->year - 1, // Année par défaut
                        'cv_path' => '',
                        'lettre_motivation_path' => '',
                        'lettre_motivation' => 'Stagiaire créé par le RH - offre associée par défaut',
                        'offre_stage_id' => $offreParDefaut->id,
                        'message' => 'Stagiaire créé directement par le RH',
                        'statut' => 'accepte',
                        'date_decision' => now(),
                        'commentaire' => 'Offre associée automatiquement par défaut',
                        'stagiaire_id' => $user->id, // Lier au stagiaire
                    ]);
                    
                    // Recharger la candidature avec l'offre
                    $candidature->load('offreStage.entreprise');
                    $offre = $candidature->offreStage;
                }
            } else {
                // Si la candidature existe mais n'a pas d'offre, lui en assigner une
                if (!$candidature->offreStage) {
                    $offreParDefaut = OffreStage::where('statut', 'publiee')
                                                   ->orderBy('titre')
                                                   ->first();
                    
                    if ($offreParDefaut) {
                        $candidature->update([
                            'offre_stage_id' => $offreParDefaut->id,
                            'commentaire' => 'Offre associée automatiquement',
                        ]);
                        
                        $candidature->load('offreStage.entreprise');
                        $offre = $candidature->offreStage;
                    }
                } else {
                    $offre = $candidature->offreStage;
                }
            }
        }
        
        return view('rh.users.show', compact('user', 'candidature', 'offre'));
    }

    /**
     * Afficher le formulaire de modification d'utilisateur
     */
    public function edit(User $user)
    {
        // Charger la relation role pour éviter les erreurs
        $user->load('role');
        
        // Vérifier que le RH peut modifier cet utilisateur
        if (!in_array($user->role->name, ['stagiaire', 'encadrant', 'rh', 'admin'])) {
            abort(403, 'Accès non autorisé');
        }

        return view('rh.users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        // Charger la relation role pour éviter les erreurs
        $user->load('role');
        
        // Vérifier que le RH peut modifier cet utilisateur
        if (!in_array($user->role->name, ['stagiaire', 'encadrant', 'rh', 'admin'])) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
        ]);

        // Gérer la checkbox active : valeur explicite (1 = cochée, 0 = non cochée)
        $isActive = $request->input('active', 0) == 1;

        $user->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'active' => $isActive,
        ]);

        // Log pour débogage
        \Log::info("Mise à jour utilisateur {$user->id} - active: " . ($isActive ? 'true' : 'false') . " (valeur reçue: " . $request->input('active', 'null') . ")");

        return redirect()->route('rh.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        // Charger la relation role pour éviter les erreurs
        $user->load('role');
        
        // Vérifier que le RH peut supprimer cet utilisateur
        if (!in_array($user->role->name, ['stagiaire', 'encadrant', 'rh', 'admin'])) {
            abort(403, 'Accès non autorisé');
        }

        $user->delete();

        return redirect()->route('rh.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Activer un utilisateur
     */
    public function activate(User $user)
    {
        $user->load('role');
        if (!in_array($user->role->name, ['stagiaire', 'encadrant', 'rh', 'admin'])) {
            abort(403, 'Accès non autorisé');
        }

        $user->update(['active' => true]);

        return redirect()->route('rh.users.index')
            ->with('success', 'Utilisateur activé avec succès.');
    }

    /**
     * Désactiver un utilisateur
     */
    public function deactivate(User $user)
    {
        $user->load('role');
        if (!in_array($user->role->name, ['stagiaire', 'encadrant', 'rh', 'admin'])) {
            abort(403, 'Accès non autorisé');
        }

        $user->update(['active' => false]);

        return redirect()->route('rh.users.index')
            ->with('success', 'Utilisateur désactivé avec succès.');
    }

    // ===== GESTION DES AFFECTATIONS =====

    /**
     * Lister toutes les affectations encadrant-stagiaire
     */
    public function assignmentsIndex()
    {
        $query = User::whereHas('role', function($q) {
            $q->where('name', 'stagiaire');
        })->with('encadrant', 'role');

        // Par défaut, afficher uniquement les stagiaires non affectés
        if (!request('assignment_status')) {
            $query->whereNull('encadrant_id');
        }

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
    public function assignmentsCreate(Request $request)
    {
        $stagiaires = User::whereHas('role', function($q) {
            $q->where('name', 'stagiaire');
        })->with(['candidature.offreStage.secteur'])->get();
        
        // Récupérer le stagiaire pré-sélectionné si passé en paramètre
        $selectedStagiaire = null;
        $encadrants = collect();
        
        if ($request->has('stagiaire_id')) {
            $selectedStagiaire = User::whereHas('role', function($q) {
                $q->where('name', 'stagiaire');
            })->with(['candidature.offreStage.secteur'])
            ->findOrFail($request->stagiaire_id);
            
            // Filtrer les encadrants par secteur du stagiaire, ou afficher tous si pas de secteur
            $secteurId = null;
            $secteurNom = null;
            
            // Essayer d'abord via la relation directe offre_stage_id dans le user
            if ($selectedStagiaire->offre_stage_id) {
                $offreStage = OffreStage::find($selectedStagiaire->offre_stage_id);
                if ($offreStage && $offreStage->secteur_id) {
                    $secteur = Secteur::find($offreStage->secteur_id);
                    if ($secteur) {
                        $secteurId = $offreStage->secteur_id;
                        $secteurNom = $secteur->nom;
                    }
                }
            }
            
            // Si pas trouvé via la relation directe, essayer via la candidature
            if (!$secteurId && $selectedStagiaire->candidature) {
                if ($selectedStagiaire->candidature->offreStage && $selectedStagiaire->candidature->offreStage->secteur) {
                    $secteurId = $selectedStagiaire->candidature->offreStage->secteur_id;
                    $secteurNom = $selectedStagiaire->candidature->offreStage->secteur->nom;
                }
            }
            
            // Si secteur trouvé, filtrer les encadrants
            if ($secteurId) {
                $encadrants = User::whereHas('role', function($q) {
                    $q->where('name', 'encadrant');
                })
                ->where('secteur_id', $secteurId)
                ->with(['secteur', 'stagiairesAffectes'])
                ->get()
                ->map(function($encadrant) {
                    return [
                        'id' => $encadrant->id,
                        'nom' => $encadrant->nom,
                        'prenom' => $encadrant->prenom,
                        'email' => $encadrant->email,
                        'secteur' => $encadrant->secteur ? $encadrant->secteur->nom : 'Non défini',
                        'nombre_stagiaires' => $encadrant->stagiairesAffectes->count(),
                        'disponibilite' => $encadrant->stagiairesAffectes->count() < 5 ? 'Disponible' : 'Charge élevée',
                        'couleur_disponibilite' => $encadrant->stagiairesAffectes->count() < 5 ? 'success' : 'warning',
                    ];
                });
            } else {
                // Si pas de secteur défini, afficher tous les encadrants
                $encadrants = User::whereHas('role', function($q) {
                    $q->where('name', 'encadrant');
                })
                ->with(['secteur', 'stagiairesAffectes'])
                ->get()
                ->map(function($encadrant) {
                    return [
                        'id' => $encadrant->id,
                        'nom' => $encadrant->nom,
                        'prenom' => $encadrant->prenom,
                        'email' => $encadrant->email,
                        'secteur' => $encadrant->secteur ? $encadrant->secteur->nom : 'Non défini',
                        'nombre_stagiaires' => $encadrant->stagiairesAffectes->count(),
                        'disponibilite' => $encadrant->stagiairesAffectes->count() < 5 ? 'Disponible' : 'Charge élevée',
                        'couleur_disponibilite' => $encadrant->stagiairesAffectes->count() < 5 ? 'success' : 'warning',
                    ];
                });
            }
        }

        return view('rh.assignments.create', compact('stagiaires', 'encadrants', 'selectedStagiaire'));
    }

    /**
     * Créer une affectation
     */
    public function assignmentsStore(Request $request)
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
     * Modifier une affectation
     */
    public function assignmentsEdit($id)
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
    public function assignmentsUpdate(Request $request, $id)
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
    public function assignmentsDestroy($id)
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
