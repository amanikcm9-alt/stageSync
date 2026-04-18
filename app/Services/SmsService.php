<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SmsService
{
    /**
     * Envoyer un email au candidat (remplace SMS)
     * Note: Les candidats reçoivent des emails, pas des SMS
     */
    public function envoyer($destinataire, $sujet, $contenu)
    {
        try {
            // Pour les candidats, $destinataire est l'email
            $email = $destinataire;
            
            Log::info("Début envoi email à {$email}: {$sujet}");
            
            // Créer la notification en base
            $notification = Notification::create([
                'type' => 'email',
                'destinataire' => $email,
                'sujet' => $sujet,
                'contenu' => $contenu,
                'statut' => 'en_attente',
                'date_envoi' => null,
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 1, // ID de l'utilisateur admin par défaut
            ]);
            
            Log::info("Notification créée avec ID: {$notification->id}");

            // Envoi réel de l'email
            $resultat = $this->envoyerEmail($email, $sujet, $contenu);
            Log::info("Résultat envoi email: " . json_encode($resultat));

            if ($resultat['succes']) {
                $notification->marquerEnvoye();
                Log::info("Email envoyé avec succès à {$email}: {$sujet}");
                return true;
            } else {
                $notification->marquerEchec($resultat['erreur']);
                Log::error("Échec d'envoi email à {$email}: {$resultat['erreur']}");
                return false;
            }

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi email: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Envoyer un email réel
     */
    private function envoyerEmail($email, $sujet, $contenu)
    {
        try {
            Mail::raw($contenu, function ($message) use ($email, $sujet) {
                $message->to($email)
                       ->subject($sujet)
                       ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            return [
                'succes' => true,
                'message' => 'Email envoyé avec succès'
            ];
            
        } catch (\Exception $e) {
            return [
                'succes' => false,
                'erreur' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Envoyer une notification d'acceptation de candidature par email avec identifiants
     */
    public function envoyerAcceptationCandidature($candidature)
    {
        $sujet = "Votre compte stagiaire a été créé - Candidature Acceptée";
        
        // Vérifier si le candidat a un compte utilisateur
        $user = \App\Models\User::where('email', $candidature->email)->first();
        
        if ($user) {
            // Si le compte existe, générer un nouveau mot de passe ou utiliser l'existant
            // Pour l'instant, on utilise un mot de passe par défaut ou on génère un nouveau
            $password = 'stagiaire2024'; // Mot de passe par défaut, ou vous pouvez générer un aléatoire
            
            // Mettre à jour le mot de passe de l'utilisateur
            $user->update([
                'password' => bcrypt($password)
            ]);
            
            $contenu = "Félicitations {$candidature->prenom} !\n\n" .
                       "Votre candidature pour le poste de {$candidature->offreStage->titre} chez {$candidature->offreStage->entreprise->nom} a été acceptée.\n\n" .
                       "Voici vos identifiants pour accéder à la plateforme :\n\n" .
                       "Email : {$candidature->email}\n" .
                       "Mot de passe : {$password}\n\n" .
                       "URL de connexion : " . url('/login') . "\n\n" .
                       "Nous vous conseillons de changer votre mot de passe lors de votre première connexion.\n\n" .
                       "Cordialement,\n" .
                       "L'équipe de recrutement";
        } else {
            // Si le compte n'existe pas encore, il sera créé dans le contrôleur
            // On envoie un email de notification générale
            $contenu = "Félicitations {$candidature->prenom} !\n\n" .
                       "Votre candidature pour le poste de {$candidature->offreStage->titre} chez {$candidature->offreStage->entreprise->nom} a été acceptée.\n\n" .
                       "Un compte stagiaire va être créé pour vous.\n" .
                       "Vous recevrez prochainement un email avec vos identifiants de connexion.\n\n" .
                       "URL de connexion : " . url('/login') . "\n\n" .
                       "Cordialement,\n" .
                       "L'équipe de recrutement";
        }

        return $this->envoyer($candidature->email, $sujet, $contenu);
    }

    /**
     * Envoyer un email avec les identifiants de connexion (appelé après création du compte)
     */
    public function envoyerIdentifiantsConnexion($candidature, $email, $password)
    {
        $sujet = "Vos identifiants de connexion - Compte Stagiaire Créé";
        $contenu = "Félicitations {$candidature->prenom} !\n\n" .
                   "Votre compte stagiaire a été créé avec succès.\n\n" .
                   "Voici vos identifiants pour accéder à la plateforme :\n\n" .
                   "Email : {$email}\n" .
                   "Mot de passe : {$password}\n\n" .
                   "URL de connexion : " . url('/login') . "\n\n" .
                   "Nous vous conseillons de changer votre mot de passe lors de votre première connexion.\n\n" .
                   "Cordialement,\n" .
                   "L'équipe de recrutement";

        return $this->envoyer($email, $sujet, $contenu);
    }

    /**
     * Envoyer une notification de refus de candidature par email
     */
    public function envoyerRefusCandidature($candidature)
    {
        $sujet = "Candidature Refusée - {$candidature->offreStage->titre}";
        $contenu = "Bonjour {$candidature->prenom},\n\n" .
                   "Nous vous remercions pour votre intérêt.\n\n" .
                   "Votre candidature pour le poste de {$candidature->offreStage->titre} n'a pas été retenue.\n\n" .
                   "Nous vous souhaitons beaucoup de succès dans vos recherches.\n\n" .
                   "Cordialement,\n" .
                   "L'équipe de recrutement";

        return $this->envoyer($candidature->email, $sujet, $contenu);
    }

    /**
     * Envoyer une notification d'entretien planifié par email
     */
    public function envoyerEntretienPlanifie($candidature)
    {
        $sujet = "Entretien Planifié - {$candidature->offreStage->titre}";
        $contenu = "Bonjour {$candidature->prenom},\n\n" .
                   "Un entretien est prévu pour votre candidature.\n\n" .
                   "Date: {$candidature->date_entretien->format('d/m/Y H:i')}\n" .
                   "Lieu: {$candidature->lieu_entretien}\n\n" .
                   "Merci de votre présence.\n\n" .
                   "Cordialement,\n" .
                   "L'équipe de recrutement";

        return $this->envoyer($candidature->email, $sujet, $contenu);
    }
}
