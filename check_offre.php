<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OffreStage;
use App\Models\Secteur;

echo "=== Vérification de l'offre ID 15 ===\n";

// Vérifier l'offre
$offre = OffreStage::find(15);
if ($offre) {
    echo "Offre trouvée:\n";
    echo "- ID: " . $offre->id . "\n";
    echo "- Titre: " . $offre->titre . "\n";
    echo "- secteur_id: " . ($offre->secteur_id ?? 'NULL') . "\n";
    
    // Vérifier le secteur
    if ($offre->secteur_id) {
        $secteur = Secteur::find($offre->secteur_id);
        if ($secteur) {
            echo "- Secteur trouvé: " . $secteur->nom . "\n";
        } else {
            echo "- ERREUR: secteur_id existe mais secteur non trouvé\n";
        }
    } else {
        echo "- ERREUR: secteur_id est NULL\n";
    }
    
    // Tester la relation
    $offreAvecSecteur = OffreStage::with('secteur')->find(15);
    if ($offreAvecSecteur && $offreAvecSecteur->secteur) {
        echo "- Test relation: Secteur trouvé via relation = " . $offreAvecSecteur->secteur->nom . "\n";
    } else {
        echo "- Test relation: ERREUR - Secteur non trouvé via relation\n";
    }
} else {
    echo "ERREUR: Offre ID 15 non trouvée\n";
}

echo "\n=== Liste des secteurs disponibles ===\n";
$secteurs = Secteur::all();
foreach ($secteurs as $secteur) {
    echo "- ID: " . $secteur->id . " - Nom: " . $secteur->nom . "\n";
}
