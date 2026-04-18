<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAllUsersController extends Controller
{
    // Lister tous les utilisateurs (tous rôles)
    public function index()
    {
        $query = User::with('role');

        // Filtres
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtre par rôle
        if (request('role')) {
            $query->whereHas('role', function($q) {
                $q->where('name', request('role'));
            });
        }

        // Filtre par statut (actif/inactif)
        if (request('status')) {
            if (request('status') === 'active') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Tri
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        if (in_array($sort, ['nom', 'prenom', 'email', 'created_at', 'role'])) {
            if ($sort === 'role') {
                $query->join('roles', 'users.role_id', '=', 'roles.id')
                      ->orderBy('roles.name', $direction)
                      ->select('users.*');
            } else {
                $query->orderBy($sort, $direction);
            }
        }

        $users = $query->paginate(15);
        $roles = \App\Models\Role::all();

        return view('admin.all-users.index', compact('users', 'roles'));
    }

    // Créer un utilisateur (n'importe quel rôle)
    public function create()
    {
        $user = auth()->user();
        $roles = \App\Models\Role::all();
        
        // Si l'utilisateur est RH, exclure le rôle admin
        if ($user->role->name === 'rh') {
            $roles = $roles->where('name', '!=', 'admin');
        }
        
        $encadrants = User::whereHas('role', function($q) {
            $q->where('name', 'encadrant');
        })->get();

        return view('admin.all-users.create', compact('roles', 'encadrants'));
    }

    public function store(Request $request)
    {
        // Debug : Afficher toutes les données reçues
        \Log::info('Données reçues dans store:', [
            'all' => $request->all(),
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => $request->password ? '***' : null,
            'role_id' => $request->role_id,
            'encadrant_id' => $request->encadrant_id,
            'active' => $request->active,
        ]);

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'encadrant_id' => 'nullable|exists:users,id',
            'active' => 'sometimes|boolean',
        ]);

        // Vérifier si l'utilisateur est RH et essaie de créer un admin
        $currentUser = auth()->user();
        $selectedRole = \App\Models\Role::find($request->role_id);
        
        if ($currentUser->role->name === 'rh' && $selectedRole->name === 'admin') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Les RH ne peuvent pas créer d\'administrateurs.');
        }

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'encadrant_id' => $request->encadrant_id ?? null,
            'email_verified_at' => $request->active ? now() : null,
        ]);

        return redirect()->route('admin.all-users.index')
            ->with('success', 'Utilisateur créé avec succès');
    }

    // Modifier un utilisateur
    public function edit(User $user)
    {
        $currentUser = auth()->user();
        $roles = \App\Models\Role::all();
        
        // Si l'utilisateur est RH, exclure le rôle admin
        if ($currentUser->role->name === 'rh') {
            $roles = $roles->where('name', '!=', 'admin');
        }
        
        $encadrants = User::whereHas('role', function($q) {
            $q->where('name', 'encadrant');
        })->get();

        return view('admin.all-users.edit', compact('user', 'roles', 'encadrants'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'encadrant_id' => 'nullable|exists:users,id',
            'active' => 'boolean',
        ]);

        // Vérifier si l'utilisateur est RH et essaie de modifier en admin
        $currentUser = auth()->user();
        $selectedRole = \App\Models\Role::find($request->role_id);
        
        if ($currentUser->role->name === 'rh' && $selectedRole->name === 'admin') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Les RH ne peuvent pas modifier un utilisateur en administrateur.');
        }

        $user->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'encadrant_id' => $request->encadrant_id ?? null,
            'email_verified_at' => $request->active ? ($user->email_verified_at ?: now()) : null,
        ]);

        if ($request->password) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('admin.all-users.index')
            ->with('success', 'Utilisateur mis à jour avec succès');
    }

    // Désactiver un utilisateur
    public function deactivate(User $user)
    {
        $user->update(['email_verified_at' => null]);
        
        return redirect()->route('admin.all-users.index')
            ->with('success', 'Utilisateur désactivé avec succès');
    }

    // Activer un utilisateur
    public function activate(User $user)
    {
        $user->update(['email_verified_at' => now()]);
        
        return redirect()->route('admin.all-users.index')
            ->with('success', 'Utilisateur activé avec succès');
    }

    // Supprimer un utilisateur
    public function destroy(User $user)
    {
        // Empêcher la suppression de soi-même
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.all-users.index')
            ->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        $user->delete();
        
        return redirect()->route('admin.all-users.index')
            ->with('success', 'Utilisateur supprimé avec succès');
    }
}
