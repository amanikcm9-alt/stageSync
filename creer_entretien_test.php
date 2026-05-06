<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Entretien;
use App\Models\Candidature;
use App\Models\OffreStage;

echo "=== Création d'un entretien de test pour vérifier le bouton ===\n\n";

// 1. Récupérer une offre existante
echo "1. Récupération d'une offre existante...\n";
$offre = OffreStage::first();
if (!$offre) {
    echo "❌ Aucune offre trouvée\n";
    exit;
}
echo "✅ Offre trouvée: {$offre->titre} (ID: {$offre->id})\n";

// 2. Créer une candidature de test
echo "\n2. Création d'une candidature de test...\n";

$candidature = Candidature::create([
    'nom' => 'Candidat Test',
    'prenom' => 'Bouton',
    'email' => 'bouton@test.com',
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

echo "✅ Candidature créée (ID: {$candidature->id})\n";

// 3. Créer un entretien planifié
echo "\n3. Création d'un entretien planifié...\n";

$entretien = Entretien::create([
    'candidature_id' => $candidature->id,
    'date_entretien' => date('Y-m-d', strtotime('+2 days')),
    'heure_entretien' => date('Y-m-d H:i:s', strtotime('+2 days')),
    'lieu_entretien' => 'Salle Test Bouton',
    'statut' => 'planifie'
]);

echo "✅ Entretien créé (ID: {$entretien->id})\n";
echo "   - Statut: {$entretien->statut}\n";
echo "   - Date: {$entretien->date_entretien}\n";
echo "   - isTermine(): " . ($entretien->isTermine() ? 'VRAI' : 'FAUX') . "\n";
echo "   - Condition !isTermine(): " . (!$entretien->isTermine() ? 'VRAI - Bouton visible' : 'FAUX - Bouton caché') . "\n";

// 4. Afficher l'URL pour tester
echo "\n4. URL pour tester le bouton:\n";
echo "   http://127.0.0.1:8000/rh/entretiens/{$entretien->id}\n";

// 5. Vérifier les autres entretiens existants
echo "\n5. Autres entretiens existants:\n";
$tousEntretiens = Entretien::with('candidature')->get();
foreach ($tousEntretiens as $e) {
    echo "   - ID: {$e->id}, Statut: {$e->statut}, isTermine(): " . ($e->isTermine() ? 'VRAI' : 'FAUX') . ", URL: /rh/entretiens/{$e->id}\n";
}

echo "\n=== Test terminé ===\n";
echo "✅ Entretien de test créé avec succès\n";
echo "✅ Accédez à l'URL ci-dessus pour voir le bouton 'Clôturer entretien'\n";
echo "✅ Le bouton devrait être visible car l'entretien est planifié (non terminé)\n";
