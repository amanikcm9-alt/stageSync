<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestEmailController extends Controller
{
    public function sendTest()
    {
        try {
            $email = request('email', 'test@example.com');
            
            Mail::raw(
                "Ceci est un email de test depuis le système de gestion des stages.\n\n" .
                "Si vous recevez cet email, la configuration Gmail est correcte !\n\n" .
                "Heure d'envoi : " . now()->format('d/m/Y H:i:s') . "\n" .
                "URL de l'application : " . url('/'),
                function ($message) use ($email) {
                    $message->to($email)
                           ->subject('📧 Test Email - Système Gestion Stages');
                }
            );
            
            return redirect()->route('login')->with('success', "Email de test envoyé à : {$email}");
            
        } catch (\Exception $e) {
            return back()->with('error', "Erreur d'envoi : " . $e->getMessage());
        }
    }
}
