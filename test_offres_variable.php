<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Test de la variable \$offres ===\n\n";

try {
    // Simuler la méthode create() du contrôleur
    $offres = App\Models\OffreStage::where('statut', 'publiee')
                               ->orderBy('titre')
                               ->get();
    
    echo "Nombre d'offres trouvées : " . $offres->count() . "\n";
    
    if ($offres->count() > 0) {
        echo "Liste des offres :\n";
        foreach ($offres as $offre) {
            echo "- ID: {$offre->id}, Titre: {$offre->titre}\n";
        }
    } else {
        echo "Aucune offre trouvée avec le statut 'publiee'\n";
        
        // Vérifier toutes les offres
        $allOffres = App\Models\OffreStage::all();
        echo "\nToutes les offres dans la base :\n";
        foreach ($allOffres as $offre) {
            echo "- ID: {$offre->id}, Titre: {$offre->titre}, Statut: {$offre->statut}\n";
        }
    }
    
    echo "\nTest de compact('offres') :\n";
    $data = compact('offres');
    echo "Clés dans compact : " . implode(', ', array_keys($data)) . "\n";
    echo "Type de \$offres : " . gettype($offres) . "\n";
    
} catch (\Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
    echo "Stack trace : " . $e->getTraceAsString() . "\n";
}
