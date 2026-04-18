<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Changement du mot de passe RH ===\n\n";

// Récupérer l'utilisateur RH
$rhUser = App\Models\User::where('email', 'rh@gmail.com')->first();

if ($rhUser) {
    echo "Utilisateur RH trouvé : {$rhUser->prenom} {$rhUser->nom}\n";
    echo "Email : {$rhUser->email}\n";
    
    // Changer le mot de passe à '123456'
    $rhUser->password = bcrypt('123456');
    $rhUser->save();
    
    echo "Mot de passe changé à '123456'\n";
    
    // Test de connexion
    echo "\nTest de connexion avec le nouveau mot de passe:\n";
    if (Auth::attempt(['email' => 'rh@gmail.com', 'password' => '123456'])) {
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
echo "Mot de passe : 123456\n";
echo "URL : http://127.0.0.1:8000/login\n";
