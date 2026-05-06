<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Récupérer la première entreprise
    $entreprise = \App\Models\Entreprise::first();
    
    if (!$entreprise) {
        echo "Aucune entreprise trouvée dans la base de données.\n";
        exit(1);
    }
    
    // Mettre à jour toutes les offres sans entreprise
    $count = \App\Models\OffreStage::whereNull('entreprise_id')->update(['entreprise_id' => $entreprise->id]);
    
    echo "Mise à jour terminée :\n";
    echo "- Entreprise utilisée : {$entreprise->nom} (ID: {$entreprise->id})\n";
    echo "- Nombre d'offres mises à jour : {$count}\n";
    
    // Vérification
    $offres = \App\Models\OffreStage::with('entreprise')->get();
    echo "\nVérification :\n";
    foreach ($offres as $offre) {
        echo "- Offre '{$offre->titre}' -> Entreprise: " . ($offre->entreprise ? $offre->entreprise->nom : 'NULL') . "\n";
    }
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
