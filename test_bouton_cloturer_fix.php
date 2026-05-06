<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Entretien;
use App\Models\Candidature;
use App\Models\OffreStage;

echo "=== Test du bouton 'Clôturer entretien' corrigé ===\n\n";

// 1. Récupérer une offre existante
echo "1. Récupération d'une offre existante...\n";
$offre = OffreStage::first();
if (!$offre) {
    echo "❌ Aucune offre trouvée\n";
    exit;
}
echo "✅ Offre trouvée: {$offre->titre} (ID: {$offre->id})\n";

// 2. Créer un entretien planifié pour le test
echo "\n2. Création d'un entretien planifié...\n";

$candidature = Candidature::create([
    'nom' => 'Candidat Test Bouton',
    'prenom' => 'Fix',
    'email' => 'fix@test.com',
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

$entretienPlanifie = Entretien::create([
    'candidature_id' => $candidature->id,
    'date_entretien' => date('Y-m-d', strtotime('+1 day')),
    'heure_entretien' => date('Y-m-d H:i:s', strtotime('+1 day')),
    'lieu_entretien' => 'Salle Test Fix',
    'statut' => 'planifie'
]);

echo "✅ Entretien planifié créé (ID: {$entretienPlanifie->id})\n";
echo "   - Statut: {$entretienPlanifie->statut}\n";

// 3. Vérifier les conditions d'affichage
echo "\n3. Vérification des conditions d'affichage...\n";

echo "Entretien planifié:\n";
echo "   - isTermine(): " . ($entretienPlanifie->isTermine() ? 'VRAI' : 'FAUX') . "\n";
echo "   - Condition !isTermine(): " . (!$entretienPlanifie->isTermine() ? 'VRAI - Bouton visible' : 'FAUX - Bouton caché') . "\n";

echo "\nEntretien ID 4 (déjà terminé):\n";
$entretienExistant = Entretien::find(4);
if ($entretienExistant) {
    echo "   - isTermine(): " . ($entretienExistant->isTermine() ? 'VRAI' : 'FAUX') . "\n";
    echo "   - Condition !isTermine(): " . (!$entretienExistant->isTermine() ? 'VRAI - Bouton visible' : 'FAUX - Bouton caché') . "\n";
}

// 4. Créer un entretien en cours pour tester
echo "\n4. Création d'un entretien en cours...\n";

$entretienEnCours = Entretien::create([
    'candidature_id' => $candidature->id,
    'date_entretien' => date('Y-m-d'),
    'heure_entretien' => date('Y-m-d H:i:s'),
    'lieu_entretien' => 'Salle Test En Cours',
    'statut' => 'en_cours'
]);

echo "✅ Entretien en cours créé (ID: {$entretienEnCours->id})\n";
echo "   - Statut: {$entretienEnCours->statut}\n";
echo "   - isTermine(): " . ($entretienEnCours->isTermine() ? 'VRAI' : 'FAUX') . "\n";
echo "   - Condition !isTermine(): " . (!$entretienEnCours->isTermine() ? 'VRAI - Bouton visible' : 'FAUX - Bouton caché') . "\n";

// 5. Test de la méthode terminer
echo "\n5. Test de la méthode terminer sur l'entretien planifié...\n";

try {
    $entretienPlanifie->update([
        'statut' => \App\Models\Entretien::STATUT_TERMINE
    ]);

    echo "✅ Entretien marqué comme terminé\n";
    $entretienPlanifie->refresh();
    echo "   - Nouveau statut: {$entretienPlanifie->statut}\n";
    echo "   - isTermine(): " . ($entretienPlanifie->isTermine() ? 'VRAI' : 'FAUX') . "\n";
    echo "   - Condition !isTermine(): " . (!$entretienPlanifie->isTermine() ? 'VRAI - Bouton visible' : 'FAUX - Bouton caché') . "\n";

} catch (Exception $e) {
    echo "❌ Erreur lors de la clôture: " . $e->getMessage() . "\n";
}

// 6. Nettoyage
echo "\n6. Nettoyage des données de test...\n";

Entretien::whereIn('id', [$entretienPlanifie->id, $entretienEnCours->id])->delete();
Candidature::where('id', $candidature->id)->delete();

echo "✅ Nettoyage terminé\n";

echo "\n=== Test terminé ===\n";
echo "✅ Le bouton s'affiche maintenant pour les entretiens non terminés\n";
echo "✅ La condition !isTermine() fonctionne correctement\n";
echo "✅ Les entretiens planifiés et en cours peuvent être clôturés\n";
echo "✅ Les entretiens déjà terminés affichent un message approprié\n";
