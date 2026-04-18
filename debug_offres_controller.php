<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Débogage du contrôleur RHUserController ===\n\n";

try {
    // Simuler exactement la méthode create() du contrôleur
    echo "1. Récupération des offres avec statut 'publiee' :\n";
    $offres = App\Models\OffreStage::where('statut', 'publiee')
                               ->orderBy('titre')
                               ->get();
    
    echo "   Nombre d'offres trouvées : " . $offres->count() . "\n";
    
    if ($offres->count() > 0) {
        echo "   Détail des offres :\n";
        foreach ($offres as $offre) {
            echo "   - ID: {$offre->id}, Titre: {$offre->titre}, Statut: '{$offre->statut}'\n";
            echo "     Entreprise: " . ($offre->entreprise ? $offre->entreprise->nom : 'N/A') . "\n";
        }
    } else {
        echo "   Aucune offre trouvée avec statut 'publiee'\n";
    }
    
    echo "\n2. Vérification de toutes les offres :\n";
    $allOffres = App\Models\OffreStage::all();
    echo "   Total des offres dans la base : " . $allOffres->count() . "\n";
    
    foreach ($allOffres as $offre) {
        echo "   - ID: {$offre->id}, Titre: {$offre->titre}, Statut: '{$offre->statut}'\n";
    }
    
    echo "\n3. Test du compact() :\n";
    $data = compact('offres');
    echo "   Clés dans compact : " . implode(', ', array_keys($data)) . "\n";
    echo "   Type de \$offres : " . gettype($data['offres']) . "\n";
    echo "   Count \$offres : " . $data['offres']->count() . "\n";
    
    echo "\n4. Test de la vue (simulation) :\n";
    if (isset($data['offres']) && $data['offres']->count() > 0) {
        echo "   La condition isset(\$offres) && \$offres->count() > 0 est VRAIE\n";
        echo "   Les offres devraient s'afficher dans le formulaire\n";
    } else {
        echo "   La condition isset(\$offres) && \$offres->count() > 0 est FAUSSE\n";
        echo "   Le message 'Aucune offre disponible' s'affichera\n";
    }
    
} catch (\Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
    echo "Stack trace : " . $e->getTraceAsString() . "\n";
}
