<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Vérification des statuts de candidatures ===\n\n";
    
    // Vérifier les statuts actuels
    $candidatures = \App\Models\Candidature::all();
    $statuts = [];
    
    foreach ($candidatures as $candidature) {
        $statuts[] = $candidature->statut;
        echo "Candidature ID: {$candidature->id} - Statut: {$candidature->statut}\n";
    }
    
    echo "\nStatuts uniques: " . implode(', ', array_unique($statuts)) . "\n";
    
    // Vérifier la structure de la table
    echo "\n=== Structure de la table candidatures ===\n";
    $schema = \Illuminate\Support\Facades\Schema::getColumnListing('candidatures');
    foreach ($schema as $column) {
        echo "- $column\n";
    }
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
