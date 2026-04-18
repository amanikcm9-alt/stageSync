<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FirstConnectionController extends Controller
{
    public function show(Request $request)
    {
        // Vérifier si les coordonnées sont en session
        if (!$request->session()->has('user_email') && !$request->session()->has('user_password')) {
            return redirect()->route('login')->with('error', 'Aucune coordonnée de connexion trouvée.');
        }

        return view('auth.first-connection')->with([
            'first_connection' => true,
            'user_email' => $request->session()->get('user_email'),
            'user_password' => $request->session()->get('user_password')
        ]);
    }

    public function clear(Request $request)
    {
        // Effacer les coordonnées de la session
        $request->session()->forget(['user_email', 'user_password', 'first_connection']);
        
        return redirect()->route('login')->with('success', 'Coordonnées effacées de la session.');
    }
}
