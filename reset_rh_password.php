<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Réinitialisation du mot de passe RH ===\n\n";

// Récupérer l'utilisateur RH
$rhUser = App\Models\User::where('email', 'rh@gmail.com')->first();

if ($rhUser) {
    echo "Utilisateur RH trouvé : {$rhUser->prenom} {$rhUser->nom}\n";
    echo "Email : {$rhUser->email}\n";
    
    // Réinitialiser le mot de passe à 'password'
    $rhUser->password = bcrypt('password');
    $rhUser->save();
    
    echo "Mot de passe réinitialisé à 'password'\n";
    
    // Test de connexion
    echo "\nTest de connexion avec le nouveau mot de passe:\n";
    if (Auth::attempt(['email' => 'rh@gmail.com', 'password' => 'password'])) {
        echo "SUCCÈS : Connexion réussie\n";
        $user = Auth::user();
        echo "Utilisateur connecté : {$user->prenom} {$user->nom}\n";
        echo "Rôle : " . ($user->role ? $user->role->name : 'Aucun') . "\n";
    } else {
        echo "ERREUR : Connexion échouée\n";
    }
} else {
    echo "ERREUR : Utilisateur RH non trouvé\n";
}

echo "\n=== Instructions de connexion ===\n";
echo "Email : rh@gmail.com\n";
echo "Mot de passe : password\n";
echo "URL : http://127.0.0.1:8000/login\n";
