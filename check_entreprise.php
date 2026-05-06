<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $entreprise = \App\Models\Entreprise::first();
    
    if (!$entreprise) {
        echo "Aucune entreprise trouvée.\n";
        exit(1);
    }
    
    echo "Entreprise trouvée : {$entreprise->nom}\n";
    echo "Attributs disponibles :\n";
    foreach ($entreprise->getAttributes() as $key => $value) {
        echo "- $key: " . ($value ?? 'NULL') . "\n";
    }
    
    echo "\nVérification des champs de secteur :\n";
    echo "- secteur_activite: " . ($entreprise->secteur_activite ?? 'NULL') . "\n";
    echo "- secteur: " . ($entreprise->secteur ?? 'NULL') . "\n";
    echo "- secteur_id: " . ($entreprise->secteur_id ?? 'NULL') . "\n";
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
