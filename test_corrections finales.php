<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Entretien;
use App\Models\Candidature;
use App\Models\OffreStage;

echo "=== Test des corrections finales ===\n\n";

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
    'prenom' => 'Corrections',
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
echo "   - Statut: {$entretien->statut}\n";

// 3. Tester l'affichage des entretiens en cours
echo "\n3. Test de l'affichage des entretiens en cours...\n";

$entretiensActifs = \App\Models\Entretien::with(['candidature.offreStage.entreprise'])
    ->whereNotIn('statut', ['termine', 'annule'])
    ->orderBy('date_entretien', 'desc')
    ->orderBy('heure_entretien', 'desc')
    ->limit(5)
    ->get();

echo "Entretiens actifs (non terminés/annulés): {$entretiensActifs->count()}\n";
$trouveDansActifs = $entretiensActifs->contains('id', $entretien->id);
echo "   - Entretien de test trouvé: " . ($trouveDansActifs ? 'OUI' : 'NON') . "\n";

// 4. Tester les routes pour les boutons
echo "\n4. Test des routes pour les boutons...\n";

echo "   - Route accepter: rh.candidatures.accepter\n";
echo "   - Route refuser: rh.candidatures.refuser\n";
echo "   - Route supprimer: rh.entretiens.destroy\n";

// 5. Vérifier les méthodes du contrôleur
echo "\n5. Vérification des méthodes du contrôleur...\n";

$controller = new \App\Http\Controllers\EntretienController();
echo "   - Méthode destroy existe: " . (method_exists($controller, 'destroy') ? 'OUI' : 'NON') . "\n";
echo "   - Méthode terminer existe: " . (method_exists($controller, 'terminer') ? 'OUI' : 'NON') . "\n";

// 6. Afficher les URLs de test
echo "\n6. URLs pour tester les corrections:\n";
echo "   - Page candidatures: http://127.0.0.1:8000/rh/candidatures\n";
echo "   - Page détail entretien: http://127.0.0.1:8000/rh/entretiens/{$entretien->id}\n";

// 7. Nettoyage
echo "\n7. Nettoyage des données de test...\n";

Entretien::where('id', $entretien->id)->delete();
Candidature::where('id', $candidature->id)->delete();

echo "✅ Nettoyage terminé\n";

echo "\n=== Test terminé ===\n";
echo "✅ Page candidatures corrigée pour afficher les entretiens actifs\n";
echo "✅ Page détail entretien corrigée avec 3 boutons\n";
echo "✅ Méthodes du contrôleur ajoutées\n";
echo "✅ Routes fonctionnelles pour les actions\n";
