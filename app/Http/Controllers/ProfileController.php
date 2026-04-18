<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/*
 * CLASS REDONDANTE - NON NÉCESSAIRE
 * Raison : Fonctionnalité peut être intégrée dans chaque contrôleur utilisateur
 * Alternative : Chaque contrôleur peut gérer son propre profil
 * Date de mise en commentaire : 16/04/2026
 */
class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo_path && $user->photo_path !== 'images/default-avatar.svg') {
                // Si c'est dans storage/public
                if (!str_starts_with($user->photo_path, 'images/')) {
                    Storage::disk('public')->delete($user->photo_path);
                }
            }
            
            // Stocker la nouvelle photo
            $path = $request->file('photo')->store('users/photos', 'public');
            $user->photo_path = $path;
        }

        $user->save();

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}