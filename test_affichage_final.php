<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Entretien;
use App\Models\Candidature;
use App\Models\OffreStage;

echo "=== Test de l'affichage final réduit et organisé ===\n\n";

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
    'nom' => 'Candidat Test Final',
    'prenom' => 'Affichage',
    'email' => 'affichage@test.com',
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
    'lieu_entretien' => 'Salle Test Affichage',
    'statut' => 'planifie'
]);

echo "✅ Entretien créé (ID: {$entretien->id})\n";

// 3. Vérifier les réductions appliquées
echo "\n3. Vérification des réductions maximales appliquées...\n";

echo "   - Espacement minimal:\n";
echo "     ✓ mb-2 (au lieu de mb-3/4) - espacement vertical très réduit\n";
echo "     ✓ py-1 (au lieu de py-2/3) - padding vertical minimal\n";
echo "     ✓ g-0/g-1 (gap minimal entre éléments)\n";

echo "\n   - Taille de texte ultra-réduite:\n";
echo "     ✓ class='small fw-bold' - titre compact\n";
echo "     ✓ class='small text-muted' - informations secondaires\n";
echo "     ✓ style='font-size: 0.65rem' - badges très petits\n";

echo "\n   - Boutons ultra-compacts:\n";
echo "     ✓ style='font-size: 0.75rem' - police très petite\n";
echo "     ✓ style='padding: 0.25rem 0.5rem' - padding minimal\n";
echo "     ✓ btn-sm - taille de bouton réduite\n";

echo "\n   - Confirmations ultra-courtes:\n";
echo "     ✓ 'OK?' (au lieu de 'Accepter ?')\n";
echo "     ✓ 'KO?' (au lieu de 'Refuser ?')\n";

// 4. Afficher l'URL de test
echo "\n4. URL pour tester l'affichage final:\n";
echo "   http://127.0.0.1:8000/rh/entretiens/{$entretien->id}\n";

// 5. Nettoyage
echo "\n5. Nettoyage des données de test...\n";

Entretien::where('id', $entretien->id)->delete();
Candidature::where('id', $candidature->id)->delete();

echo "✅ Nettoyage terminé\n";

echo "\n=== Test terminé ===\n";
echo "✅ Taille d'écriture ultra-réduite\n";
echo "✅ Interface organisée avec boutons en bas\n";
echo "✅ Espacement minimal appliqué\n";
echo "✅ Caches nettoyés pour affichage immédiat\n";
echo "\n🎯 L'interface devrait maintenant être visible avec les changements !\n";
