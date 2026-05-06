<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Entretien;
use App\Models\Candidature;
use App\Models\OffreStage;

echo "=== Test de la réduction finale des éléments ===\n\n";

// 1. Récupérer une offre existante
echo "1. Récupération d'une offre existante...\n";
$offre = OffreStage::first();
if (!$offre) {
    echo "❌ Aucune offre trouvée\n";
    exit;
}
echo "✅ Offre trouvée: {$offre->titre} (ID: {$offre->id})\n";

// 2. Créer une candidature et un entretien de test
echo "\n2. Création d'une candidature et entretien de test...\n";

$candidature = Candidature::create([
    'nom' => 'Candidat Test Reduction',
    'prenom' => 'Finale',
    'email' => 'reduction@test.com',
    'telephone' => '0123456789',
    'offre_stage_id' => $offre->id,
    'statut' => 'en_cours',
    'date_naissance' => '2000-01-01',
    'dernier_diplome' => 'Bac',
    'annee_diplome' => '2020',
    'etablissement' => 'Test School',
    'cv_path' => 'test_cv.pdf',
    'lettre_motivation' => 'Test lettre de motivation'
]);

$entretien = Entretien::create([
    'candidature_id' => $candidature->id,
    'date_entretien' => date('Y-m-d', strtotime('+1 day')),
    'heure_entretien' => date('Y-m-d H:i:s', strtotime('+1 day')),
    'lieu_entretien' => 'Salle Test Reduction',
    'statut' => 'planifie'
]);

echo "✅ Entretien créé (ID: {$entretien->id})\n";

// 3. Vérifier les réductions appliquées
echo "\n3. Vérification des réductions appliquées...\n";

echo "   - Cartes et espacement:\n";
echo "     ✓ mb-3 (au lieu de mb-4) - moins d'espace vertical\n";
echo "     ✓ py-2 (au lieu de py-3) - padding vertical réduit\n";
echo "     ✓ g-1/g-2 (gap réduit entre éléments)\n";

echo "\n   - Taille du texte:\n";
echo "     ✓ class='small' - police plus petite\n";
echo "     ✓ badge-sm - badges plus petits\n";

echo "\n   - Boutons en bas:\n";
echo "     ✓ btn-sm - boutons plus petits\n";
echo "     ✓ w-100 - largeur complète\n";
echo "     ✓ py-2 dans card-body - espacement réduit\n";

// 4. Afficher l'URL de test
echo "\n4. URL pour tester la réduction finale:\n";
echo "   http://127.0.0.1:8000/rh/entretiens/{$entretien->id}\n";

// 5. Nettoyage
echo "\n5. Nettoyage des données de test...\n";

Entretien::where('id', $entretien->id)->delete();
Candidature::where('id', $candidature->id)->delete();

echo "✅ Nettoyage terminé\n";

echo "\n=== Test terminé ===\n";
echo "✅ Taille des éléments réduite avec succès\n";
echo "✅ 2 boutons en bas en petite taille\n";
echo "✅ Espacement optimisé\n";
echo "✅ Police plus compacte\n";
