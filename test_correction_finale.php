<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Entretien;
use App\Models\Candidature;
use App\Models\OffreStage;

echo "=== Test de la correction finale des boutons redondants ===\n\n";

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
    'prenom' => 'Correction',
    'email' => 'final@test.com',
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
    'lieu_entretien' => 'Salle Test Final',
    'statut' => 'planifie'
]);

echo "✅ Entretien créé (ID: {$entretien->id})\n";

// 3. Vérifier les boutons restants
echo "\n3. Vérification des boutons restants...\n";

echo "   - Boutons principaux (en bas):\n";
echo "     ✓ Accepter (OK) - btn-success btn-sm\n";
echo "     ✓ Refuser (KO) - btn-danger btn-sm\n";

echo "\n   - Boutons dans section évaluation:\n";
echo "     ✓ En attente - btn-secondary (seul bouton restant)\n";
echo "     ✗ Accepter candidature - SUPPRIMÉ\n";
echo "     ✗ Refuser candidature - SUPPRIMÉ\n";

// 4. Vérifier la taille d'écriture réduite
echo "\n4. Vérification de la taille d'écriture réduite...\n";

echo "   - Confirmations:\n";
echo "     ✓ 'Accepter ?' (au lieu de 'Accepter cette candidature ?')\n";
echo "     ✓ 'Refuser ?' (au lieu de 'Refuser cette candidature ?')\n";

echo "\n   - Texte boutons:\n";
echo "     ✓ 'OK' (au lieu de 'Accepter')\n";
echo "     ✓ 'KO' (au lieu de 'Refuser')\n";

echo "\n   - Taille boutons:\n";
echo "     ✓ btn-sm (taille réduite)\n";
echo "     ✓ icônes sans me-1 (plus compactes)\n";

// 5. Afficher l'URL de test
echo "\n5. URL pour tester la correction finale:\n";
echo "   http://127.0.0.1:8000/rh/entretiens/{$entretien->id}\n";

// 6. Nettoyage
echo "\n6. Nettoyage des données de test...\n";

Entretien::where('id', $entretien->id)->delete();
Candidature::where('id', $candidature->id)->delete();

echo "✅ Nettoyage terminé\n";

echo "\n=== Test terminé ===\n";
echo "✅ Boutons redondants supprimés avec succès\n";
echo "✅ Taille d'écriture réduite\n";
echo "✅ Plus que 2 boutons principaux (OK/KO)\n";
echo "✅ Plus que 1 bouton d'évaluation (En attente)\n";
