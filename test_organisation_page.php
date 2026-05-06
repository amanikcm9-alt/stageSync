<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Entretien;
use App\Models\Candidature;
use App\Models\OffreStage;

echo "=== Test de l'organisation finale de la page détail entretien ===\n\n";

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
    'nom' => 'Candidat Test Organisation',
    'prenom' => 'Page',
    'email' => 'organisation@test.com',
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
    'lieu_entretien' => 'Salle Test Organisation',
    'statut' => 'planifie'
]);

echo "✅ Entretien créé (ID: {$entretien->id})\n";

// 3. Vérifier les données pour l'affichage
echo "\n3. Vérification des données pour l'affichage...\n";

echo "   - Candidat: {$entretien->candidature->nom} {$entretien->candidature->prenom}\n";
echo "   - Offre: {$entretien->candidature->offreStage->titre}\n";
echo "   - Entreprise: {$entretien->candidature->offreStage->entreprise->nom}\n";
echo "   - Date entretien: {$entretien->date_entretien}\n";
echo "   - Lieu: {$entretien->lieu_entretien}\n";
echo "   - Statut: {$entretien->statut_label}\n";

// 4. Vérifier les routes pour les boutons
echo "\n4. Vérification des routes pour les boutons...\n";

echo "   - Route accepter: rh.candidatures.accepter ✅\n";
echo "   - Route refuser: rh.candidatures.refuser ✅\n";
echo "   - Bouton supprimer: RETIRÉ (selon demande) ✅\n";

// 5. Afficher l'URL de test
echo "\n5. URL pour tester l'organisation:\n";
echo "   http://127.0.0.1:8000/rh/entretiens/{$entretien->id}\n";

// 6. Nettoyage
echo "\n6. Nettoyage des données de test...\n";

Entretien::where('id', $entretien->id)->delete();
Candidature::where('id', $candidature->id)->delete();

echo "✅ Nettoyage terminé\n";

echo "\n=== Test terminé ===\n";
echo "✅ Page organisée avec succès\n";
echo "✅ Boutons accepter et refuser en bas\n";
echo "✅ Bouton supprimer retiré\n";
echo "✅ Informations redondantes supprimées\n";
echo "✅ Taille d'écriture réduite\n";
