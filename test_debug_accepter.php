<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Entretien;
use App\Models\Candidature;
use App\Models\OffreStage;

echo "=== Test de débogage du bouton accepter depuis page entretien ===\n\n";

// 1. Récupérer une offre existante
echo "1. Récupération d'une offre existante...\n";
$offre = OffreStage::first();
if (!$offre) {
    echo "❌ Aucune offre trouvée\n";
    exit;
}
echo "✅ Offre trouvée: {$offre->titre} (ID: {$offre->id})\n";

// 2. Créer une candidature avec toutes les données requises
echo "\n2. Création d'une candidature complète pour le test...\n";

$candidature = Candidature::create([
    'nom' => 'Debug Test',
    'prenom' => 'Accepter',
    'email' => 'debug.accepter@test.com',
    'telephone' => '0123456789',
    'offre_stage_id' => $offre->id,
    'statut' => 'en_cours',
    'date_naissance' => '2000-01-01',
    'dernier_diplome' => 'Bac',
    'annee_diplome' => '2020',
    'etablissement' => 'Test School',
    'cv_path' => 'test_cv.pdf',
    'lettre_motivation' => 'Test lettre de motivation pour debug accepter'
]);

echo "✅ Candidature créée (ID: {$candidature->id})\n";

// 3. Vérifier que toutes les propriétés sont bien définies
echo "\n3. Vérification des propriétés de la candidature...\n";

echo "   - Nom: '" . ($candidature->nom ?? 'NULL') . "'\n";
echo "   - Prénom: '" . ($candidature->prenom ?? 'NULL') . "'\n";
echo "   - Email: '" . ($candidature->email ?? 'NULL') . "'\n";
echo "   - Téléphone: '" . ($candidature->telephone ?? 'NULL') . "'\n";
echo "   - Statut: '" . ($candidature->statut ?? 'NULL') . "'\n";
echo "   - Offre ID: '" . ($candidature->offre_stage_id ?? 'NULL') . "'\n";

// 4. Créer un entretien associé
echo "\n4. Création d'un entretien associé...\n";

$entretien = Entretien::create([
    'candidature_id' => $candidature->id,
    'date_entretien' => date('Y-m-d', strtotime('+1 day')),
    'heure_entretien' => date('Y-m-d H:i:s', strtotime('+1 day')),
    'lieu_entretien' => 'Salle Debug Accepter',
    'statut' => 'planifie'
]);

echo "✅ Entretien créé (ID: {$entretien->id})\n";

// 5. Vérifier la relation entretien->candidature
echo "\n5. Vérification de la relation entretien->candidature...\n";

$entretienCharge = Entretien::with('candidature')->find($entretien->id);
if ($entretienCharge && $entretienCharge->candidature) {
    echo "✅ Relation OK - Candidature trouvée\n";
    echo "   - Nom: '" . ($entretienCharge->candidature->nom ?? 'NULL') . "'\n";
    echo "   - Prénom: '" . ($entretienCharge->candidature->prenom ?? 'NULL') . "'\n";
    echo "   - Email: '" . ($entretienCharge->candidature->email ?? 'NULL') . "'\n";
} else {
    echo "❌ Relation KO - Candidature non trouvée\n";
}

// 6. Afficher les instructions de test
echo "\n6. Instructions pour tester le débogage:\n";
echo "   1. Accédez à: http://127.0.0.1:8000/rh/entretiens/{$entretien->id}\n";
echo "   2. Cliquez sur le bouton 'Accepter'\n";
echo "   3. Vérifiez les logs dans: storage/logs/laravel.log\n";
echo "   4. Cherchez les logs avec '=== DÉBUT MÉTHODE ACCEPTER ==='\n";

// 7. Afficher la commande pour voir les logs en temps réel
echo "\n7. Pour voir les logs en temps réel (dans un autre terminal):\n";
echo "   tail -f storage/logs/laravel.log | grep 'DÉBUT MÉTHODE ACCEPTER'\n";

// 8. Nettoyage (commenté pour permettre le test)
echo "\n8. Données de test conservées pour le test manuel:\n";
echo "   - Candidature ID: {$candidature->id}\n";
echo "   - Entretien ID: {$entretien->id}\n";
echo "   - Pour nettoyer après test: php -r \"require 'vendor/autoload.php'; \\App\\Models\\Entretien::where('id', {$entretien->id})->delete(); \\App\\Models\\Candidature::where('id', {$candidature->id})->delete(); echo 'Nettoyage terminé';\"\n";

echo "\n=== Test de débogage prêt ===\n";
echo "✅ Environnement de test configuré\n";
echo "✅ Logs de débogage ajoutés dans le contrôleur\n";
echo "✅ Données de test créées avec toutes les propriétés\n";
echo "\n🎯 Testez maintenant le bouton accepter et vérifiez les logs !\n";
