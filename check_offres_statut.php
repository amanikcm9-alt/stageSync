<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Vérification des statuts d'offres ===\n\n";
    
    $offres = \App\Models\OffreStage::all();
    
    foreach ($offres as $offre) {
        echo "Offre ID: {$offre->id} - Titre: {$offre->titre}\n";
        echo "- Statut actuel: {$offre->statut}\n";
        echo "- Visible publiquement: " . ($offre->statut === 'publiee' ? 'OUI' : 'NON') . "\n";
        
        // Vérifier les candidatures associées
        $candidatures = $offre->candidatures;
        echo "- Nombre de candidatures: " . $candidatures->count() . "\n";
        
        foreach ($candidatures as $candidature) {
            echo "  * Candidature ID: {$candidature->id} - Statut: {$candidature->statut}\n";
        }
        
        echo "\n";
    }
    
    // Vérifier le scope publiee
    echo "=== Test du scope publiee ===\n";
    $offresPubliees = \App\Models\OffreStage::publiee()->get();
    echo "Offres avec scope publiee(): " . $offresPubliees->count() . "\n";
    foreach ($offresPubliees as $offre) {
        echo "- {$offre->titre} (statut: {$offre->statut})\n";
    }
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
