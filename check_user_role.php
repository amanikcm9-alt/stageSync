<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Vérifier l'utilisateur connecté et son rôle
echo "=== Vérification des rôles utilisateurs ===\n\n";

$users = App\Models\User::with('role')->get();

foreach ($users as $user) {
    echo "Utilisateur : {$user->prenom} {$user->nom}\n";
    echo "Email : {$user->email}\n";
    echo "Rôle : " . ($user->role ? $user->role->name : 'Aucun rôle') . "\n";
    echo "Rôle ID : " . ($user->role ? $user->role->id : 'N/A') . "\n";
    echo "------------------------\n";
}

echo "\n=== Vérification des rôles disponibles ===\n";

$roles = App\Models\Role::all();

foreach ($roles as $role) {
    echo "Rôle ID {$role->id} : {$role->name}\n";
}
