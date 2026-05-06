<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Entretien;
use App\Models\Candidature;
use App\Models\OffreStage;

echo "=== Test des nouveaux filtres d'entretiens ===\n\n";

// 1. Récupérer une offre existante
echo "1. Récupération d'une offre existante...\n";
$offre = OffreStage::first();
if (!$offre) {
    echo "❌ Aucune offre trouvée\n";
    exit;
}
echo "✅ Offre trouvée: {$offre->titre} (ID: {$offre->id})\n";

// 2. Créer des données de test
echo "2. Création de données de test...\n";

// Créer une candidature acceptée
$candidatureAcceptee = Candidature::create([
    'nom' => 'Candidat Accepté',
    'prenom' => 'Test',
    'email' => 'accepte@test.com',
    'telephone' => '0123456789',
    'offre_stage_id' => $offre->id,
    'statut' => 'accepte',
    'date_naissance' => '2000-01-01',
    'dernier_diplome' => 'Bac',
    'annee_diplome' => '2020',
    'etablissement' => 'Test School',
    'cv_path' => 'test_cv.pdf',
    'lettre_motivation' => 'Test lettre de motivation'
]);

// Créer une candidature refusée
$candidatureRefusee = Candidature::create([
    'nom' => 'Candidat Refusé',
    'prenom' => 'Test',
    'email' => 'refuse@test.com',
    'telephone' => '0123456789',
    'offre_stage_id' => $offre->id,
    'statut' => 'refuse',
    'date_naissance' => '2000-01-01',
    'dernier_diplome' => 'Bac',
    'annee_diplome' => '2020',
    'etablissement' => 'Test School',
    'cv_path' => 'test_cv.pdf',
    'lettre_motivation' => 'Test lettre de motivation'
]);

// Créer une candidature en cours
$candidatureEnCours = Candidature::create([
    'nom' => 'Candidat En Cours',
    'prenom' => 'Test',
    'email' => 'encours@test.com',
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

// Créer des entretiens pour chaque candidature
$entretienAccepte = Entretien::create([
    'candidature_id' => $candidatureAcceptee->id,
    'date_entretien' => date('Y-m-d', strtotime('-2 days')),
    'heure_entretien' => date('Y-m-d H:i:s', strtotime('-2 days')),
    'lieu_entretien' => 'Salle A',
    'statut' => 'termine',
    'note_evaluation' => 15,
    'commentaires_evaluation' => 'Bon candidat'
]);

$entretienRefuse = Entretien::create([
    'candidature_id' => $candidatureRefusee->id,
    'date_entretien' => date('Y-m-d', strtotime('-1 day')),
    'heure_entretien' => date('Y-m-d H:i:s', strtotime('-1 day')),
    'lieu_entretien' => 'Salle B',
    'statut' => 'termine',
    'note_evaluation' => 8,
    'commentaires_evaluation' => 'Candidat faible'
]);

$entretienEnCours = Entretien::create([
    'candidature_id' => $candidatureEnCours->id,
    'date_entretien' => date('Y-m-d', strtotime('+1 day')),
    'heure_entretien' => date('Y-m-d H:i:s', strtotime('+1 day')),
    'lieu_entretien' => 'Salle C',
    'statut' => 'planifie'
]);

$entretienPasse = Entretien::create([
    'candidature_id' => $candidatureEnCours->id,
    'date_entretien' => date('Y-m-d', strtotime('-3 days')),
    'heure_entretien' => date('Y-m-d H:i:s', strtotime('-3 days')),
    'lieu_entretien' => 'Salle D',
    'statut' => 'planifie'
]);

echo "✅ Données de test créées\n";
echo "   - Candidature acceptée avec entretien\n";
echo "   - Candidature refusée avec entretien\n";
echo "   - Candidature en cours avec 2 entretiens\n\n";

// 2. Tester le filtre par défaut (ni accepté ni refusé)
echo "2. Test du filtre par défaut (ni accepté ni refusé)...\n";

$queryDefault = Entretien::with(['candidature.offreStage.entreprise', 'evaluateur']);
$queryDefault->whereHas('candidature', function($q) {
    $q->whereNotIn('statut', ['accepte', 'refuse']);
});

$entretiensDefault = $queryDefault->get();
echo "Nombre d'entretiens par défaut: {$entretiensDefault->count()}\n";

foreach ($entretiensDefault as $e) {
    echo "- ID: {$e->id}, Candidat: {$e->candidature->nom}, Statut candidature: {$e->candidature->statut}, Date: {$e->date_entretien}\n";
}

// 3. Tester les filtres de date
echo "\n3. Test des filtres de date...\n";

// Filtre "Déjà commencées"
$queryCommencees = Entretien::with(['candidature']);
$queryCommencees->whereHas('candidature', function($q) {
    $q->whereNotIn('statut', ['accepte', 'refuse']);
});
$queryCommencees->where('date_entretien', '<=', now()->format('Y-m-d'));

$entretiensCommencees = $queryCommencees->get();
echo "Entretiens déjà commencées: {$entretiensCommencees->count()}\n";

foreach ($entretiensCommencees as $e) {
    echo "- ID: {$e->id}, Date: {$e->date_entretien}\n";
}

// Filtre "Pas encore terminées"
$queryNonTerminees = Entretien::with(['candidature']);
$queryNonTerminees->whereHas('candidature', function($q) {
    $q->whereNotIn('statut', ['accepte', 'refuse']);
});
$queryNonTerminees->where('date_entretien', '>=', now()->format('Y-m-d'));

$entretiensNonTerminees = $queryNonTerminees->get();
echo "Entretiens pas encore terminées: {$entretiensNonTerminees->count()}\n";

foreach ($entretiensNonTerminees as $e) {
    echo "- ID: {$e->id}, Date: {$e->date_entretien}\n";
}

// 4. Nettoyage
echo "\n4. Nettoyage des données de test...\n";

Entretien::whereIn('id', [$entretienAccepte->id, $entretienRefuse->id, $entretienEnCours->id, $entretienPasse->id])->delete();
Candidature::whereIn('id', [$candidatureAcceptee->id, $candidatureRefusee->id, $candidatureEnCours->id])->delete();

echo "✅ Nettoyage terminé\n";

echo "\n=== Test terminé ===\n";
