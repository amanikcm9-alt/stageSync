<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Mettre à jour l'utilisateur RH avec la nouvelle photo par défaut
$user = App\Models\User::where('prenom', 'rh')->where('nom', 'rh')->first();

if ($user) {
    echo "Mise à jour de la photo pour : " . $user->prenom . " " . $user->nom . "\n";
    
    // Utiliser le chemin public/images/
    $user->photo_path = 'images/default-avatar.svg';
    $user->save();
    
    echo "Nouveau chemin de photo : " . $user->photo_path . "\n";
    echo "URL complète : " . asset('storage/' . $user->photo_path) . "\n";
    echo "URL directe : " . asset($user->photo_path) . "\n";
} else {
    echo "Utilisateur non trouvé\n";
}
