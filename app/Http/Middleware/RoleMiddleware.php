<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user()->load('role');
        
        // Pour les routes admin, permettre aux admins et RH
        if ($role === 'admin') {
            if (!$user->role || !in_array($user->role->name, ['admin', 'rh'])) {
                abort(403);
            }
        } else {
            // Pour les autres rôles, vérification stricte
            if (!$user->role || $user->role->name !== $role) {
                abort(403);
            }
        }

        return $next($request);
    }
}