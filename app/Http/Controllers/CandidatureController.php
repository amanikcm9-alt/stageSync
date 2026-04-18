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

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

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

        $candidatures = $query->latest()->paginate(15);
        $offres = OffreStage::with('entreprise')->pluck('titre', 'id');
        $statuts = [
            'recue' => 'Reçue',
            'en_cours' => 'En cours',
            'accepte' => 'Acceptée',
            'refuse' => 'Refusée'
        ];

        return view($this->getViewPath('index'), compact('candidatures', 'offres', 'statuts'));
    }

    public function show(Candidature $candidature)
    {
        $candidature->load(['offreStage.entreprise', 'offreStage.rh']);
        
        return view($this->getViewPath('show'), compact('candidature'));
    }

    public function accepter(Request $request, Candidature $candidature)
    {
        $request->validate([
            'commentaire' => 'nullable|string|max:1000'
        ]);

        // Vérifier si le candidat a déjà un compte utilisateur
        $existingUser = User::where('email', $candidature->email)->first();
        
        // Récupérer le rôle stagiaire de manière sécurisée
        $stagiaireRole = Role::where('name', 'stagiaire')->first();
        if (!$stagiaireRole) {
            Log::error("ERREUR: Le rôle 'stagiaire' n'existe pas dans la base de données");
            return redirect()->back()
                ->with('error', 'Erreur : Le rôle stagiaire n\'existe pas. Veuillez contacter l\'administrateur.');
        }
        
        if (!$existingUser) {
            // Créer un nouveau compte stagiaire
            $password = Str::random(10); // Générer un mot de passe aléatoire
            
            Log::info("Création d'un nouveau compte stagiaire pour : {$candidature->email}");
            Log::info("Rôle stagiaire ID : {$stagiaireRole->id}");
            
            try {
                $user = User::create([
                    'nom' => $candidature->nom,
                    'prenom' => $candidature->prenom,
                    'email' => $candidature->email,
                    'telephone' => $candidature->telephone ?? null,
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

                // Envoyer email avec identifiants
                $emailEnvoye = $this->smsService->envoyerIdentifiantsConnexion($candidature, $candidature->email, $password);

                $message = 'La candidature a été acceptée avec succès.' . ($emailEnvoye ? ' Email envoyé au candidat avec succès.' : ' Échec d\'envoi email.');
                $message .= " Compte stagiaire créé (ID: {$user->id}) et ajouté à la liste des utilisateurs.";

                return redirect()->route($this->getRouteName('index'))
                    ->with('success', $message);
                    
            } catch (\Exception $e) {
                Log::error("ERREUR lors de la création de l'utilisateur : " . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Erreur lors de la création du compte stagiaire : ' . $e->getMessage());
            }
            
        } else {
            // Mettre à jour le rôle vers stagiaire si nécessaire
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

                // Envoyer email d'acceptation avec identifiants (mot de passe mis à jour)
                $emailEnvoye = $this->smsService->envoyerAcceptationCandidature($candidature);

                $message = 'La candidature a été acceptée avec succès.' . ($emailEnvoye ? ' Email envoyé au candidat avec succès.' : ' Échec d\'envoi email.');
                $message .= ' Utilisateur existant mis à jour vers le rôle stagiaire.';

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
        $request->validate([
            'motif_refus' => 'nullable|string|max:1000'
        ]);

        $candidature->update([
            'statut' => 'refuse',
            'date_decision' => now(),
            'motif_refus' => $request->motif_refus
        ]);

        // Envoyer email de refus
        $emailEnvoye = $this->smsService->envoyerRefusCandidature($candidature);

        return redirect()->route($this->getRouteName('index'))
            ->with('success', 'La candidature a été refusée.' . ($emailEnvoye ? ' Email envoyé au candidat.' : ' Échec d\'envoi email.'));
    }

    public function planifierEntretien(Request $request, Candidature $candidature)
    {
        $request->validate([
            'date_entretien' => 'required|date|after:now',
            'heure_entretien' => 'required|string',
            'lieu_entretien' => 'required|string',
            'notes_entretien' => 'nullable|string|max:1000'
        ]);

        $candidature->update([
            'statut' => 'en_cours',
            'date_entretien' => $request->date_entretien,
            'heure_entretien' => $request->heure_entretien,
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
