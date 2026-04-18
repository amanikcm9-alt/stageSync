<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Vérifier l'utilisateur RH
$user = App\Models\User::where('prenom', 'rh')->where('nom', 'rh')->first();

if ($user) {
    echo "Utilisateur trouvé : " . $user->prenom . " " . $user->nom . "\n";
    echo "Email : " . $user->email . "\n";
    echo "Photo path : " . ($user->photo_path ?? 'NULL') . "\n";
    echo "User ID : " . $user->id . "\n";
    
    // Si pas de photo, en ajouter une par défaut
    if (!$user->photo_path) {
        echo "Aucune photo trouvée. Ajout d'une photo par défaut...\n";
        
        // Créer une image par défaut simple
        $defaultImagePath = 'users/default-avatar.png';
        
        // Mettre à jour l'utilisateur
        $user->photo_path = $defaultImagePath;
        $user->save();
        
        echo "Photo par défaut ajoutée : " . $defaultImagePath . "\n";
    } else {
        echo "Photo existante : " . $user->photo_path . "\n";
        
        // Vérifier si le fichier existe
        $fullPath = storage_path('app/public/' . $user->photo_path);
        echo "Chemin complet : " . $fullPath . "\n";
        echo "Fichier existe : " . (file_exists($fullPath) ? 'OUI' : 'NON') . "\n";
    }
} else {
    echo "Aucun utilisateur avec prénom='rh' et nom='rh' trouvé\n";
    
    // Chercher tous les utilisateurs
    $users = App\Models\User::all();
    echo "Liste des utilisateurs :\n";
    foreach ($users as $u) {
        echo "- " . $u->prenom . " " . $u->nom . " (" . $u->email . ") - Photo: " . ($u->photo_path ?? 'NULL') . "\n";
    }
}
