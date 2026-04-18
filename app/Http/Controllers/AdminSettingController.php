<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    // Afficher les paramètres de la plateforme
    public function index()
    {
        $settings = [
            'site_name' => config('app.name', 'Gestion des Stages'),
            'site_email' => config('mail.from.address', 'admin@example.com'),
            'max_file_size' => config('filesystems.max_file_size', '2048'),
            'session_timeout' => config('session.lifetime', '120'),
            'password_min_length' => config('auth.password_min_length', '8'),
            'allow_registration' => config('auth.allow_registration', false),
            'email_verification' => config('auth.email_verification', true),
            'two_factor_auth' => config('auth.two_factor_auth', false),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    // Mettre à jour les paramètres
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_email' => 'required|email',
            'max_file_size' => 'required|integer|min:1024|max:10240',
            'session_timeout' => 'required|integer|min:5|max:1440',
            'password_min_length' => 'required|integer|min:6|max:20',
            'allow_registration' => 'boolean',
            'email_verification' => 'boolean',
            'two_factor_auth' => 'boolean',
        ]);

        // Ici vous pourriez sauvegarder dans un fichier .env ou une table settings
        // Pour l'exemple, on utilise la session
        
        session(['admin_settings_updated' => true]);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Paramètres mis à jour avec succès');
    }
}
