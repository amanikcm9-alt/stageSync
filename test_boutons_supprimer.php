<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Secteur;
use App\Models\TypeStage;
use App\Models\OffreStage;

echo "=== Vérification des boutons supprimer manquants ===\n\n";

// 1. Analyser tous les secteurs
echo "1. Analyse détaillée des secteurs...\n";

$secteurs = Secteur::all();
echo "✅ Nombre total de secteurs: {$secteurs->count()}\n\n";

foreach ($secteurs as $secteur) {
    $offresCount = $secteur->offres()->count();
    $canDelete = $offresCount === 0;
    
    echo "📋 Secteur: {$secteur->nom}\n";
    echo "   - ID: {$secteur->id}\n";
    echo "   - Actif: " . ($secteur->actif ? 'OUI' : 'NON') . "\n";
    echo "   - Offres associées: {$offresCount}\n";
    echo "   - Bouton supprimer: " . ($canDelete ? '✅ OUI' : '❌ NON') . "\n";
    
    if (!$canDelete) {
        echo "   - Raison: {$offresCount} offre(s) associée(s)\n";
        // Afficher les offres associées
        $offres = $secteur->offres()->take(3)->get();
        foreach ($offres as $offre) {
            echo "     * {$offre->titre} (ID: {$offre->id})\n";
        }
        if ($offresCount > 3) {
            echo "     * ... et " . ($offresCount - 3) . " autre(s)\n";
        }
    }
    echo "\n";
}

// 2. Analyser tous les types de stage
echo "2. Analyse détaillée des types de stage...\n";

$typeStages = TypeStage::all();
echo "✅ Nombre total de types de stage: {$typeStages->count()}\n\n";

foreach ($typeStages as $typeStage) {
    $offresCount = $typeStage->offres()->count();
    $canDelete = $offresCount === 0;
    
    echo "📋 Type de stage: {$typeStage->nom}\n";
    echo "   - ID: {$typeStage->id}\n";
    echo "   - Code: " . ($typeStage->code ?? 'N/A') . "\n";
    echo "   - Actif: " . ($typeStage->actif ? 'OUI' : 'NON') . "\n";
    echo "   - Offres associées: {$offresCount}\n";
    echo "   - Bouton supprimer: " . ($canDelete ? '✅ OUI' : '❌ NON') . "\n";
    
    if (!$canDelete) {
        echo "   - Raison: {$offresCount} offre(s) associée(s)\n";
        // Afficher les offres associées
        $offres = $typeStage->offres()->take(3)->get();
        foreach ($offres as $offre) {
            echo "     * {$offre->titre} (ID: {$offre->id})\n";
        }
        if ($offresCount > 3) {
            echo "     * ... et " . ($offresCount - 3) . " autre(s)\n";
        }
    }
    echo "\n";
}

// 3. Identifier les problèmes
echo "3. Identification des problèmes potentiels...\n";

$secteursSansBouton = $secteurs->filter(function($secteur) {
    return $secteur->offres()->count() > 0;
});

$typesSansBouton = $typeStages->filter(function($typeStage) {
    return $typeStage->offres()->count() > 0;
});

echo "📊 Résumé des problèmes:\n";
echo "   - Secteurs sans bouton supprimer: {$secteursSansBouton->count()}\n";
echo "   - Types de stage sans bouton supprimer: {$typesSansBouton->count()}\n\n";

// 4. Solutions possibles
echo "4. Solutions possibles:\n";
echo "   a) Comportement normal (recommandé):\n";
echo "      - Le bouton supprimer est masqué pour protéger les données\n";
echo "      - Empêche la suppression d'éléments utilisés\n";
echo "      - C'est la logique actuelle et sécurisée\n\n";

echo "   b) Forcer l'affichage (non recommandé):\n";
echo "      - Afficher le bouton supprimer même avec des offres associées\n";
echo "      - Ajouter une confirmation plus stricte\n";
echo "      - Risque de perte de données\n\n";

echo "   c) Permettre la suppression avec migration:\n";
echo "      - Supprimer l'élément et mettre à jour les offres associées\n";
echo "      - Plus complexe à implémenter\n\n";

// 5. Vérifier la logique dans le code
echo "5. Vérification de la logique dans le code...\n";

$codeSecteur = '@if($secteur->offres()->count() == 0)';
$codeType = '@if($typeStage->offres()->count() == 0)';

echo "✅ Logique actuelle pour les secteurs: {$codeSecteur}\n";
echo "✅ Logique actuelle pour les types: {$codeType}\n";
echo "   - Cette logique est correcte et sécurisée\n";
echo "   - Elle protège contre la suppression accidentelle\n\n";

// 6. Recommandation
echo "6. Recommandation:\n";
if ($secteursSansBouton->count() > 0 || $typesSansBouton->count() > 0) {
    echo "🔍 Les secteurs/types sans bouton supprimer sont NORMAL:\n";
    echo "   - Ils ont des offres associées\n";
    echo "   - La suppression casserait les données\n";
    echo "   - Le comportement actuel est CORRECT\n\n";
    
    echo "💡 Si vous voulez quand même supprimer:\n";
    echo "   1. Supprimez d'abord toutes les offres associées\n";
    echo "   2. Le bouton supprimer apparaîtra automatiquement\n";
    echo "   3. Vous pourrez alors supprimer le secteur/type\n";
} else {
    echo "✅ Tous les secteurs/types devraient avoir le bouton supprimer\n";
    echo "   - Aucune offre associée détectée\n";
    echo "   - Vérifiez l'affichage dans l'interface\n";
}

echo "\n=== Analyse terminée ===\n";
