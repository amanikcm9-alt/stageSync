<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter; // ajout manquant
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{


 // Formulaire pour demander la réinitialisation
   public function showResetForm($token)
{
    return view('auth.passwords.reset', ['token' => $token]);
}

    // Envoyer le lien par email
    public function sendResetLink(Request $request) {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink($request->only('email'));
          dd($status);
        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Lien de réinitialisation envoyé par email !')
            : back()->withErrors(['email' => 'Impossible d’envoyer le lien.']);
    }

    // Formulaire pour entrer le nouveau mot de passe
    public function showNewPasswordForm($token) {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    // Enregistrer le nouveau mot de passe
    public function resetPassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = Password::reset(
            $request->only('email','password','password_confirmation','token'),
            function($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect('/login')->with('success', 'Mot de passe réinitialisé avec succès !')
            : back()->withErrors(['email' => 'Erreur lors de la réinitialisation.']);
    }

public function updatePassword(Request $request) {
    $request->validate([
        'current_password' => 'required',
        'password' => 'required|confirmed|min:6',
    ]);

    $user = auth()->user();

    if (!\Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect']);
    }

    $user->password = Hash::make($request->password);
    $user->save();

    return back()->with('success', 'Mot de passe modifié avec succès !');
}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $key = 'login-attempts:'.$request->ip();

        // Vérifier si trop de tentatives
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Trop de tentatives. Réessayez dans $seconds secondes."
            ]);
        }

        $credentials = $request->only('email', 'password');

        if(Auth::attempt($credentials)){
            RateLimiter::clear($key); // reset tentatives après succès
            $request->session()->regenerate();

            // Vérifier si le compte est actif
            $user = Auth::user();
            if (!$user->active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Votre compte a été désactivé. Contactez le RH pour réactiver votre accès.'
                ]);
            }

            // Redirection selon le rôle
            $user->load('role');
            $roleName = $user->role ? $user->role->name : 'stagiaire';
            
            if ($roleName == 'admin') {
                return redirect('/admin/dashboard');
            } elseif ($roleName == 'rh') {
                return redirect('/rh/dashboard');
            } elseif ($roleName == 'encadrant') {
                return redirect('/encadrant/dashboard');
            } else {
                return redirect('/stagiaire/dashboard');
            }
        }

        // Tentative échouée → incrémenter le compteur
        RateLimiter::hit($key, 60); // blocage temporaire pour 60 secondes après max 5 tentatives

        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}