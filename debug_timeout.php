<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Débogage du timeout ===\n\n";

$start = microtime(true);

try {
    // Test de la requête exacte du contrôleur
    echo "1. Test de la requête OffreStage::where('statut', 'publiee')->orderBy('titre')->get()\n";
    $offres = App\Models\OffreStage::where('statut', 'publiee')
                               ->orderBy('titre')
                               ->get();
    
    $time1 = microtime(true) - $start;
    echo "   Temps de la requête : " . number_format($time1, 4) . " secondes\n";
    echo "   Nombre d'offres : " . $offres->count() . "\n";
    
    if ($offres->count() > 0) {
        echo "2. Test d'accès aux entreprises (boucle foreach)\n";
        $start2 = microtime(true);
        
        foreach ($offres as $offre) {
            $entreprise = $offre->entreprise;
            echo "   Offre: {$offre->id} - Entreprise: " . ($entreprise ? $entreprise->nom : 'NULL') . "\n";
        }
        
        $time2 = microtime(true) - $start2;
        echo "   Temps de la boucle : " . number_format($time2, 4) . " secondes\n";
    }
    
    $totalTime = microtime(true) - $start;
    echo "\nTemps total : " . number_format($totalTime, 4) . " secondes\n";
    
    if ($totalTime > 30) {
        echo "⚠️  ATTENTION : Temps d'exécution élevé !\n";
    }
    
} catch (\Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
    echo "Fichier : " . $e->getFile() . ":" . $e->getLine() . "\n";
}
