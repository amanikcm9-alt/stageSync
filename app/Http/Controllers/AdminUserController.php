<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/*
 * CLASS REDONDANTE - NON NÉCESSAIRE
 * Raison : L'admin ne gère que les RH et autres admins, pas tous les utilisateurs
 * Alternative : RHUserController gère déjà les utilisateurs (stagiaires/encadrants)
 * Date de mise en commentaire : 16/04/2026
 */
class AdminUserController extends Controller
{
    /**
     * Liste des utilisateurs pour attribution de rôles (RH et Admin uniquement)
     * Filtrage par nom/email et rôle RH/admin uniquement
     */
    public function index()
    {
        // Récupérer uniquement les utilisateurs RH et Admin
        $query = User::with('role')
                    ->whereHas('role', function($query) {
                        $query->whereIn('name', ['rh', 'admin']);
                    });

        // Filtre par recherche nom ou email
        if (request('search')) {
            $search = request('search');
            $query->where(function($query) use ($search) {
                $query->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtre par rôle (RH et admin uniquement)
        if (request('role')) {
            $role = request('role');
            if (in_array($role, ['rh', 'admin'])) {
                $query->whereHas('role', function($query) use ($role) {
                    $query->where('name', $role);
                });
            }
        }

        $users = $query->latest()->paginate(15);
        
        // Rôles disponibles pour le filtre (RH et admin uniquement)
        $roles = Role::whereIn('name', ['rh', 'admin'])->get();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Afficher le formulaire de création d'utilisateur (RH ou Admin)
     */
    public function create()
    {
        $roles = Role::whereIn('name', ['rh', 'admin'])->get();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Créer un nouvel utilisateur (RH ou Admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'telephone' => 'nullable|string|max:20',
        ]);

        // Vérifier que le rôle est RH ou Admin
        $role = Role::find($request->role_id);
        if (!in_array($role->name, ['rh', 'admin'])) {
            return back()->with('error', 'Seuls les rôles RH et Admin peuvent être attribués.');
        }

        User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'telephone' => $request->telephone,
            'active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Afficher les détails d'un utilisateur
     */
   public function show(User $user)
    {
        // Vérifier que l'utilisateur est RH ou Admin
        if (!in_array($user->role->name, ['rh', 'admin'])) {
            abort(403, 'Accès non autorisé');
        }

        $user->load('role');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Afficher le formulaire de modification d'utilisateur
     */
   public function edit(User $user)
    {
        // Vérifier que l'utilisateur est RH ou Admin
        if (!in_array($user->role->name, ['rh', 'admin'])) {
            abort(403, 'Accès non autorisé');
        }

        $roles = Role::whereIn('name', ['rh', 'admin'])->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        // Vérifier que l'utilisateur est RH ou Admin
        if (!in_array($user->role->name, ['rh', 'admin'])) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'telephone' => 'nullable|string|max:20',
            'active' => 'nullable|boolean',
        ]);

        // Vérifier que le rôle est RH ou Admin
        $role = Role::find($request->role_id);
        if (!in_array($role->name, ['rh', 'admin'])) {
            return back()->with('error', 'Seuls les rôles RH et Admin peuvent être attribués.');
        }

        // Gérer la checkbox active : valeur explicite (1 = cochée, 0 = non cochée)
        $isActive = $request->input('active', 0) == 1;

        $user->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'telephone' => $request->telephone,
            'active' => $isActive,
        ]);

        if ($request->password) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Activer/Désactiver un utilisateur
     */
    public function toggleActive(User $user)
    {
        // Vérifier que l'utilisateur est RH ou Admin
        if (!in_array($user->role->name, ['rh', 'admin'])) {
            abort(403, 'Accès non autorisé');
        }

        $user->update(['active' => !$user->active]);

        return back()->with('success', 'Utilisateur ' . ($user->active ? 'activé' : 'désactivé') . ' avec succès.');
    }

    
     /* Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        // Vérifier que l'utilisateur est RH ou Admin
        if (!in_array($user->role->name, ['rh', 'admin'])) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Afficher le formulaire d'attribution de rôle
     */
    public function assignRole(User $user)
    {
        // Vérifier que l'utilisateur est RH ou Admin
        if (!in_array($user->role->name, ['rh', 'admin'])) {
            abort(403, 'Accès non autorisé');
        }

        $roles = Role::whereIn('name', ['rh', 'admin'])->get();
        
        return view('admin.users.assign-role', compact('user', 'roles'));
    }

    
     /* Mettre à jour le rôle d'un utilisateur*/
     
    public function updateRole(Request $request, User $user)
    {
        // Vérifier que l'utilisateur est RH ou Admin
        if (!in_array($user->role->name, ['rh', 'admin'])) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        // Vérifier que le rôle est RH ou Admin
        $role = Role::find($request->role_id);
        if (!in_array($role->name, ['rh', 'admin'])) {
            return back()->with('error', 'Seuls les rôles RH et Admin peuvent être attribués.');
        }

        $user->update(['role_id' => $request->role_id]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Rôle attribué avec succès.');
    }
}
