<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Débogage de l'authentification ===\n\n";

// Simuler la vérification d'authentification comme le middleware
echo "Test du middleware Role pour le rôle 'rh':\n\n";

// Récupérer l'utilisateur RH
$rhUser = App\Models\User::with('role')->where('email', 'rh@gmail.com')->first();

if ($rhUser) {
    echo "Utilisateur RH trouvé : {$rhUser->prenom} {$rhUser->nom}\n";
    echo "Email : {$rhUser->email}\n";
    echo "Rôle : " . ($rhUser->role ? $rhUser->role->name : 'Aucun') . "\n";
    echo "Rôle ID : " . ($rhUser->role ? $rhUser->role->id : 'N/A') . "\n";
    
    // Test de la logique du middleware
    $role = 'rh';
    echo "\nTest de la condition du middleware pour le rôle '$role':\n";
    
    if (!$rhUser->role) {
        echo "ERREUR : L'utilisateur n'a pas de rôle\n";
    } elseif ($rhUser->role->name !== $role) {
        echo "ERREUR : Le rôle de l'utilisateur '{$rhUser->role->name}' ne correspond pas à '$role'\n";
    } else {
        echo "SUCCÈS : Le rôle correspond, l'accès devrait être autorisé\n";
    }
} else {
    echo "ERREUR : Utilisateur RH non trouvé\n";
}

echo "\n=== Test de connexion ===\n";

// Test de la connexion
if (Auth::attempt(['email' => 'rh@gmail.com', 'password' => 'password'])) {
    echo "Connexion réussie\n";
    $user = Auth::user();
    echo "Utilisateur connecté : {$user->prenom} {$user->nom}\n";
    echo "Rôle : " . ($user->role ? $user->role->name : 'Aucun') . "\n";
} else {
    echo "ERREUR : Connexion échouée\n";
}
