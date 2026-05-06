<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Test de la relation secteur ===\n\n";
    
    // Test direct de la relation
    $offre = \App\Models\OffreStage::with('secteur')->find(3);
    echo "Offre ID: 3\n";
    echo "- secteur_id: " . ($offre->secteur_id ?? 'NULL') . "\n";
    echo "- secteur relation: " . ($offre->secteur ? 'FOUND' : 'NULL') . "\n";
    if ($offre->secteur) {
        echo "- secteur nom: " . $offre->secteur->nom . "\n";
    }
    
    // Test si le secteur existe dans la table
    $secteur = \App\Models\Secteur::find(6);
    echo "\nSecteur ID: 6\n";
    echo "- secteur trouvé: " . ($secteur ? 'YES' : 'NO') . "\n";
    if ($secteur) {
        echo "- secteur nom: " . $secteur->nom . "\n";
    }
    
    // Test tous les secteurs
    echo "\nTous les secteurs:\n";
    $secteurs = \App\Models\Secteur::all();
    foreach ($secteurs as $s) {
        echo "- ID: {$s->id}, Nom: {$s->nom}\n";
    }
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
