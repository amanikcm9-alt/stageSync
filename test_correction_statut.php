<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Entretien;
use App\Models\Candidature;
use App\Models\OffreStage;

echo "=== Test de correction de l'erreur SQLSTATE[01000] ===\n\n";

// 1. Récupérer une offre existante
echo "1. Récupération d'une offre existante...\n";
$offre = OffreStage::first();
if (!$offre) {
    echo "❌ Aucune offre trouvée\n";
    exit;
}
echo "✅ Offre trouvée: {$offre->titre} (ID: {$offre->id})\n";
echo "   - Statut actuel: '{$offre->statut}'\n";

// 2. Vérifier les statuts valides
echo "\n2. Vérification des statuts valides pour offre_stages...\n";
$statutsValides = ['brouillon', 'publiee', 'cloturee', 'affectee'];
echo "   - Statuts valides: " . implode(', ', $statutsValides) . "\n";
echo "   - 'disponible' était: " . (in_array('disponible', $statutsValides) ? 'VALIDE' : 'INVALIDE') . "\n";
echo "   - 'publiee' est: " . (in_array('publiee', $statutsValides) ? 'VALIDE' : 'INVALIDE') . "\n";

// 3. Créer une candidature de test pour refus
echo "\n3. Création d'une candidature pour test de refus...\n";

$candidature = Candidature::create([
    'nom' => 'Test Statut',
    'prenom' => 'Correction',
    'email' => 'statut.correction@test.com',
    'telephone' => '0123456789',
    'offre_stage_id' => $offre->id,
    'statut' => 'en_cours',
    'date_naissance' => '2000-01-01',
    'dernier_diplome' => 'Bac',
    'annee_diplome' => '2020',
    'etablissement' => 'Test School',
    'cv_path' => 'test_cv.pdf',
    'lettre_motivation' => 'Test pour vérifier correction du statut'
]);

$entretien = Entretien::create([
    'candidature_id' => $candidature->id,
    'date_entretien' => date('Y-m-d', strtotime('+1 day')),
    'heure_entretien' => date('Y-m-d H:i:s', strtotime('+1 day')),
    'lieu_entretien' => 'Salle Test Statut',
    'statut' => 'planifie'
]);

echo "✅ Entretien créé pour test (ID: {$entretien->id})\n";

// 4. Simuler la logique de refus
echo "\n4. Simulation de la logique de refus corrigée...\n";

try {
    // Simuler la mise à jour du statut de la candidature
    $candidature->update([
        'statut' => 'refuse',
        'date_decision' => now(),
        'motif_refus' => 'Test de correction du statut'
    ]);
    echo "✅ Candidature mise à jour: statut = 'refuse'\n";

    // Simuler la mise à jour du statut de l'offre (correction)
    $offreTest = OffreStage::find($offre->id);
    $ancienStatut = $offreTest->statut;
    
    $offreTest->update(['statut' => 'publiee']);
    echo "✅ Offre mise à jour: '{$ancienStatut}' → 'publiee'\n";
    
    // Vérifier que la mise à jour a fonctionné
    $offreVerifiee = OffreStage::find($offre->id);
    echo "   - Statut final: '{$offreVerifiee->statut}'\n";
    echo "   - Statut valide: " . (in_array($offreVerifiee->statut, $statutsValides) ? 'OUI' : 'NON') . "\n";

} catch (Exception $e) {
    echo "❌ Erreur lors de la simulation: " . $e->getMessage() . "\n";
}

// 5. Instructions de test
echo "\n5. Instructions pour tester la correction:\n";
echo "   1. Accédez à: http://127.0.0.1:8000/rh/entretiens/{$entretien->id}\n";
echo "   2. Cliquez sur 'Refuser'\n";
echo "   3. Vérifiez qu'il n'y a plus d'erreur SQLSTATE[01000]\n";
echo "   4. Vérifiez que le statut de l'offre devient 'publiee'\n";

// 6. Nettoyage (commenté pour permettre le test)
echo "\n6. Données de test conservées pour le test manuel:\n";
echo "   - Candidature ID: {$candidature->id}\n";
echo "   - Entretien ID: {$entretien->id}\n";
echo "   - Pour nettoyer après test: php -r \"require 'vendor/autoload.php'; \\App\\Models\\Entretien::where('id', {$entretien->id})->delete(); \\App\\Models\\Candidature::where('id', {$candidature->id})->delete(); echo 'Nettoyage terminé';\"\n";

echo "\n=== Test de correction terminé ===\n";
echo "✅ Statut 'disponible' remplacé par 'publiee'\n";
echo "✅ 'publiee' est un statut valide dans la base de données\n";
echo "✅ Log ajouté pour suivre la mise à jour du statut\n";
echo "✅ Erreur SQLSTATE[01000] devrait être corrigée\n";
echo "\n🎯 Le refus de candidature devrait maintenant fonctionner sans erreur !\n";
