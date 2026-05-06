<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $offres = \App\Models\OffreStage::with(['entreprise', 'secteur', 'typeStage'])->get();
    
    echo "=== Vérification des données des offres ===\n\n";
    
    foreach ($offres as $offre) {
        echo "Offre ID: {$offre->id} - Titre: {$offre->titre}\n";
        echo "- Lieu: " . ($offre->lieu ?? 'NULL') . "\n";
        echo "- Secteur: " . ($offre->secteur ?? 'NULL') . "\n";
        echo "- Type stage: " . ($offre->type_stage ?? 'NULL') . "\n";
        echo "- Secteur_id: " . ($offre->secteur_id ?? 'NULL') . "\n";
        echo "- Type_stage_id: " . ($offre->type_stage_id ?? 'NULL') . "\n";
        
        echo "- Relation secteur: " . ($offre->secteur ? $offre->secteur->nom : 'NULL') . "\n";
        echo "- Relation typeStage: " . ($offre->typeStage ? $offre->typeStage->nom : 'NULL') . "\n";
        echo "- Entreprise: " . ($offre->entreprise ? $offre->entreprise->nom : 'NULL') . "\n";
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
