<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Models\Notification;
use Illuminate\Http\Request;

class CandidatController extends Controller
{
    /**
     * Tableau de bord du candidat
     */
    public function dashboard(Request $request)
    {
        // Récupérer les candidatures du candidat (basé sur email pour la démo)
        $email = $request->get('email', session('candidat_email'));
        
        if ($email) {
            session(['candidat_email' => $email]);
            $candidatures = Candidature::where('email', $email)
                ->with('offreStage.entreprise')
                ->latest()
                ->get();
        } else {
            $candidatures = collect();
        }

        // Récupérer les notifications SMS du candidat
        $notifications = collect();
        if ($email) {
            $notifications = Notification::sms()
                ->where('destinataire', 'like', "%{$this->extraireTelephoneFromEmail($email)}%")
                ->orWhere('contenu', 'like', "%{$this->extraireNomFromEmail($email)}%")
                ->latest('date_envoi')
                ->get();
        }

        return view('candidat.dashboard', compact('candidatures', 'notifications', 'email'));
    }

    /**
     * Détails d'une candidature
     */
    public function candidature($id)
    {
        $candidature = Candidature::with(['offreStage.entreprise'])
            ->findOrFail($id);

        return view('candidat.candidature', compact('candidature'));
    }

    /**
     * Notifications du candidat
     */
    public function notifications(Request $request)
    {
        $email = $request->get('email', session('candidat_email'));
        
        if (!$email) {
            return redirect()->route('candidat.dashboard')
                ->with('error', 'Veuillez d\'abord saisir votre email pour accéder à vos notifications.');
        }

        $notifications = Notification::sms()
            ->where(function($query) use ($email) {
                $telephone = $this->extraireTelephoneFromEmail($email);
                $nom = $this->extraireNomFromEmail($email);
                $query->where('destinataire', 'like', "%{$telephone}%")
                      ->orWhere('contenu', 'like', "%{$nom}%");
            })
            ->latest('date_envoi')
            ->paginate(15);

        return view('candidat.notifications', compact('notifications', 'email'));
    }

    /**
     * Extraire le numéro de téléphone depuis l'email (démo)
     */
    private function extraireTelephoneFromEmail($email)
    {
        // Pour la démo, on simule que le téléphone est dans une table users
        // En production, il faudrait une relation user->candidatures
        $demoPhones = [
            'amanikcm9@gmail.com' => '95638371',
            'test@email.com' => '0612345678',
            'candidat@email.com' => '0787654321',
        ];
        
        return $demoPhones[$email] ?? '0000000000';
    }

    /**
     * Extraire le nom depuis l'email (démo)
     */
    private function extraireNomFromEmail($email)
    {
        $demoNames = [
            'amanikcm9@gmail.com' => 'amani',
            'test@email.com' => 'Test',
            'candidat@email.com' => 'Candidat',
        ];
        
        return $demoNames[$email] ?? 'Candidat';
    }
}
