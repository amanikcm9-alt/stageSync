<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\OffreStage;
use App\Models\User;
use App\Models\Role;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CandidatureController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Rôle : Gestion des candidatures pour RH/Admin
     * Responsabilités :
     * - Liste des candidatures avec filtres
     * - Traitement des candidatures (accepter/refuser)
     * - Planification des entretiens
     * - Envoi des notifications SMS
     */

    /**
     * Get the appropriate view path based on the current route prefix
     */
    private function getViewPath($view)
    {
        $uri = request()->getRequestUri();
        
        if (strpos($uri, '/rh/') === 0) {
            return "rh.candidatures.{$view}";
        }
        
        return "admin.candidatures.{$view}";
    }

    /**
     * Get the appropriate route name based on the current route prefix
     */
    private function getRouteName($route)
    {
        $uri = request()->getRequestUri();
        
        if (strpos($uri, '/rh/') === 0) {
            return "rh.candidatures.{$route}";
        }
        
        return "admin.candidatures.{$route}";
    }

    public function index(Request $request)
    {
        $query = Candidature::with(['offreStage.entreprise', 'offreStage.rh']);

        // Par défaut, afficher uniquement les candidatures en cours
        if (!$request->filled('statut')) {
            $query->where('statut', 'en_cours');
        }

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut') && $request->statut !== 'toutes') {
            if ($request->statut === 'archive') {
                $query->whereNotNull('archived_at');
            } else {
                $query->where('statut', $request->statut);
            }
        }

        $candidatures = $query->orderBy('created_at', 'desc')->paginate(15);

        // Récupérer les entretiens qui ne sont ni terminés ni annulés pour l'affichage
        $entretiens = \App\Models\Entretien::with(['candidature.offreStage.entreprise'])
            ->whereNotIn('statut', ['termine', 'annule'])
            ->orderBy('date_entretien', 'desc')
            ->orderBy('heure_entretien', 'desc')
            ->limit(5)
            ->get();

        $statuts = [
            'en_cours' => 'En cours',
            'accepte' => 'Acceptée',
            'refuse' => 'Refusée',
            'archive' => 'Archivée'
        ];

        if ($request->filled('offre_id')) {
            $query->where('offre_stage_id', $request->offre_id);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // Filtre d'archivage
        if ($request->filled('archive')) {
            if ($request->archive === 'true') {
                $query->archived();
            } elseif ($request->archive === 'false') {
                $query->notArchived();
            }
            // Si 'archive' a une autre valeur ou est vide, on affiche tout (actives + archives)
        }
        // Si le filtre n'est pas spécifié, on affiche tout par défaut (actives + archives)

        $offres = OffreStage::with('entreprise')->pluck('titre', 'id');

        return view($this->getViewPath('index'), compact('candidatures', 'entretiens', 'offres', 'statuts'));
    }

    public function show(Candidature $candidature)
    {
        $candidature->load(['offreStage.entreprise', 'offreStage.rh']);
        
        return view($this->getViewPath('show'), compact('candidature'));
    }

    public function accepter(Request $request, Candidature $candidature)
    {
        // Logs de débogage pour identifier le problème
        Log::info("=== DÉBUT MÉTHODE ACCEPTER ===");
        Log::info("Candidature ID: " . $candidature->id);
        Log::info("Candidature object: " . ($candidature ? 'exists' : 'NULL'));
        
        if ($candidature) {
            Log::info("Candidature nom: '" . ($candidature->nom ?? 'NULL') . "'");
            Log::info("Candidature prénom: '" . ($candidature->prenom ?? 'NULL') . "'");
            Log::info("Candidature email: '" . ($candidature->email ?? 'NULL') . "'");
            Log::info("Candidature téléphone: '" . ($candidature->telephone ?? 'NULL') . "'");
        }

        $request->validate([
            'commentaire' => 'nullable|string|max:1000'
        ]);

        // Récupérer le rôle stagiaire de manière sécurisée
        $stagiaireRole = Role::where('name', 'stagiaire')->first();
        if (!$stagiaireRole) {
            Log::error("ERREUR: Le rôle 'stagiaire' n'existe pas dans la base de données");
            return redirect()->back()
                ->with('error', 'Erreur : Le rôle stagiaire n\'existe pas. Veuillez contacter l\'administrateur.');
        }
        
        // Vérifier si un utilisateur avec cet email existe déjà
        $existingUser = User::where('email', $candidature->email)->first();
        
        if (!$existingUser) {
            // Créer un nouveau compte stagiaire
            $password = Str::random(10); // Générer un mot de passe aléatoire
            
            Log::info("Création d'un nouveau compte stagiaire pour : {$candidature->email}");
            Log::info("Rôle stagiaire ID : {$stagiaireRole->id}");
            
            try {
                // Vérifications supplémentaires pour éviter les erreurs de null
                $nom = $candidature->nom ?? 'Nom';
                $prenom = $candidature->prenom ?? 'Prénom';
                $email = $candidature->email ?? '';
                $telephone = $candidature->telephone ?? null;
                
                // S'assurer que l'email n'est pas vide
                if (empty($email)) {
                    throw new \Exception('L\'email de la candidature est invalide');
                }
                
                $user = User::create([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'telephone' => $telephone,
                    'password' => bcrypt($password),
                    'role_id' => $stagiaireRole->id,
                    'email_verified_at' => now(),
                    'active' => true,
                ]);
                
                Log::info("Utilisateur créé avec ID : {$user->id}, rôle : " . ($user->role ? $user->role->name : 'non défini'));
                
                // Vérification immédiate que l'utilisateur existe bien
                $verificationUser = User::find($user->id);
                if ($verificationUser) {
                    Log::info("Vérification OK : Utilisateur {$verificationUser->id} trouvé avec rôle : " . ($verificationUser->role ? $verificationUser->role->name : 'non défini'));
                } else {
                    Log::error("ERREUR : Utilisateur créé mais non retrouvé avec ID : {$user->id}");
                }
                
                // Mettre à jour la candidature
                $candidature->update([
                    'statut' => 'accepte',
                    'date_decision' => now(),
                    'commentaire' => $request->commentaire
                ]);

                // Mettre à jour le statut de l'offre à "affectée"
                $offre = $candidature->offreStage;
                if ($offre) {
                    $offre->update(['statut' => 'affectee']);
                }

                // Envoyer email avec identifiants
                $emailEnvoye = $this->smsService->envoyerIdentifiantsConnexion($candidature, $candidature->email, $password);

                $message = 'La candidature a été acceptée avec succès.' . ($emailEnvoye ? ' Email envoyé au candidat avec succès.' : ' Échec d\'envoi email.');
                $message .= " Compte stagiaire créé (ID: {$user->id}) et ajouté à la liste des utilisateurs.";
                $message .= " L'offre a été marquée comme affectée.";

                return redirect()->route($this->getRouteName('index'))
                    ->with('success', $message);
                    
            } catch (\Exception $e) {
                Log::error("ERREUR lors de la création de l'utilisateur : " . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Erreur lors de la création du compte stagiaire : ' . $e->getMessage());
            }
        } else {
            // Utilisateur existe déjà - le mettre à jour vers le rôle stagiaire
            Log::info("Utilisateur existant trouvé : {$existingUser->id}, rôle actuel : " . ($existingUser->role ? $existingUser->role->name : 'non défini'));
            
            try {
                if ($existingUser->role_id !== $stagiaireRole->id) {
                    $existingUser->update(['role_id' => $stagiaireRole->id]);
                    Log::info("Rôle mis à jour vers stagiaire pour l'utilisateur : {$existingUser->id}");
                }
                
                // S'assurer que l'utilisateur est actif
                if (!$existingUser->active) {
                    $existingUser->update(['active' => true]);
                    Log::info("Utilisateur activé : {$existingUser->id}");
                }
                
                // Mettre à jour la candidature
                $candidature->update([
                    'statut' => 'accepte',
                    'date_decision' => now(),
                    'commentaire' => $request->commentaire
                ]);

                // Mettre à jour le statut de l'offre à "affectée"
                $offre = $candidature->offreStage;
                if ($offre) {
                    $offre->update(['statut' => 'affectee']);
                }

                // Envoyer email d'acceptation
                try {
                    Log::info("Envoi email d'acceptation pour candidature ID: " . $candidature->id);
                    $emailEnvoye = $this->smsService->envoyerAcceptationCandidature($candidature);
                    Log::info("Email d'acceptation envoyé: " . ($emailEnvoye ? 'OUI' : 'NON'));
                } catch (\Exception $e) {
                    Log::error("ERREUR lors de l'envoi email d'acceptation: " . $e->getMessage());
                    $emailEnvoye = false;
                }

                // Supprimer l'entretien associé après acceptation
                $entretienAssocie = \App\Models\Entretien::where('candidature_id', $candidature->id)->first();
                if ($entretienAssocie) {
                    $entretienAssocie->delete();
                    Log::info("Entretien ID {$entretienAssocie->id} supprimé après acceptation de la candidature");
                }

                $message = 'La candidature a été acceptée avec succès.' . ($emailEnvoye ? ' Email envoyé au candidat avec succès.' : ' Échec d\'envoi email.');
                $message .= ' Utilisateur existant mis à jour vers le rôle stagiaire.';
                $message .= ' L\'offre a été marquée comme affectée.';
                $message .= ' L\'entretien a été supprimé de la liste.';

                return redirect()->route($this->getRouteName('index'))
                    ->with('success', $message);
                    
            } catch (\Exception $e) {
                Log::error("ERREUR lors de la mise à jour de l'utilisateur : " . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Erreur lors de la mise à jour du compte stagiaire : ' . $e->getMessage());
            }
        }
    }

    public function refuser(Request $request, Candidature $candidature)
    {
        // Logs de débogage pour la méthode refuser
        Log::info("=== DÉBUT MÉTHODE REFUSER ===");
        Log::info("Candidature ID: " . $candidature->id);
        Log::info("Candidature object: " . ($candidature ? 'exists' : 'NULL'));
        
        if ($candidature) {
            Log::info("Candidature nom: '" . ($candidature->nom ?? 'NULL') . "'");
            Log::info("Candidature prénom: '" . ($candidature->prenom ?? 'NULL') . "'");
            Log::info("Candidature email: '" . ($candidature->email ?? 'NULL') . "'");
        }

        $request->validate([
            'motif_refus' => 'nullable|string|max:1000'
        ]);

        try {
            $candidature->update([
                'statut' => 'refuse',
                'date_decision' => now(),
                'motif_refus' => $request->motif_refus
            ]);

            // Mettre à jour le statut de l'offre à "publiee" (rendre l'offre disponible à nouveau)
            $offre = $candidature->offreStage;
            if ($offre) {
                $offre->update(['statut' => 'publiee']);
                Log::info("Offre ID {$offre->id} remise en statut 'publiee' après refus de la candidature");
            }

            // Envoyer email de refus avec vérifications
            try {
                Log::info("Envoi email de refus pour candidature ID: " . $candidature->id);
                $emailEnvoye = $this->smsService->envoyerRefusCandidature($candidature);
                Log::info("Email de refus envoyé: " . ($emailEnvoye ? 'OUI' : 'NON'));
            } catch (\Exception $e) {
                Log::error("ERREUR lors de l'envoi email de refus: " . $e->getMessage());
                $emailEnvoye = false;
            }

            // Supprimer l'entretien associé après refus
                $entretienAssocie = \App\Models\Entretien::where('candidature_id', $candidature->id)->first();
                if ($entretienAssocie) {
                    $entretienAssocie->delete();
                    Log::info("Entretien ID {$entretienAssocie->id} supprimé après refus de la candidature");
                }

                return redirect()->route($this->getRouteName('index'))
                    ->with('success', 'La candidature a été refusée.' . ($emailEnvoye ? ' Email envoyé au candidat.' : ' Échec d\'envoi email.') . ' L\'entretien a été supprimé de la liste.');
                
        } catch (\Exception $e) {
            Log::error("ERREUR lors du refus de la candidature : " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors du refus de la candidature : ' . $e->getMessage());
        }
    }

    public function planifierEntretien(Request $request, Candidature $candidature)
    {
        $request->validate([
            'date_entretien' => 'required|date|after_or_equal:today',
            'heure_entretien' => 'required|string',
            'lieu_entretien' => 'required|string',
            'notes_entretien' => 'nullable|string|max:1000'
        ]);

        // Créer l'entretien dans la table entretiens
        \App\Models\Entretien::create([
            'candidature_id' => $candidature->id,
            'date_entretien' => $request->date_entretien,
            'heure_entretien' => $request->date_entretien . ' ' . $request->heure_entretien,
            'lieu_entretien' => $request->lieu_entretien,
            'notes_entretien' => $request->notes_entretien,
            'statut' => \App\Models\Entretien::STATUT_PLANIFIE
        ]);

        // Mettre à jour la candidature
        $candidature->update([
            'statut' => 'en_cours',
            'date_entretien' => $request->date_entretien,
            'heure_entretien' => $request->date_entretien . ' ' . $request->heure_entretien,
            'lieu_entretien' => $request->lieu_entretien,
            'notes_entretien' => $request->notes_entretien
        ]);

        // Envoyer email de planification d'entretien
        $emailEnvoye = $this->smsService->envoyerEntretienPlanifie($candidature);

        return redirect()->route($this->getRouteName('index'))
            ->with('success', 'L\'entretien a été planifié avec succès.' . ($emailEnvoye ? ' Email envoyé au candidat.' : ' Échec d\'envoi email.'));
    }

    public function destroy(Candidature $candidature)
    {
        // Supprimer les fichiers associés
        if ($candidature->cv_path) {
            Storage::delete($candidature->cv_path);
        }
        if ($candidature->lettre_motivation_path) {
            Storage::delete($candidature->lettre_motivation_path);
        }
        if ($candidature->portfolio_path) {
            Storage::delete($candidature->portfolio_path);
        }

        $candidature->delete();

        return redirect()->route($this->getRouteName('index'))
            ->with('success', 'La candidature a été supprimée avec succès.');
    }

    /**
     * Archiver une candidature
     */
    public function archive(Candidature $candidature)
    {
        $candidature->archive();
        
        return redirect()->route($this->getRouteName('index'))
            ->with('success', 'La candidature a été archivée avec succès.');
    }

    /**
     * Désarchiver une candidature
     */
    public function unarchive(Candidature $candidature)
    {
        $candidature->unarchive();
        
        return redirect()->route($this->getRouteName('index'))
            ->with('success', 'La candidature a été restaurée avec succès.');
    }
}
