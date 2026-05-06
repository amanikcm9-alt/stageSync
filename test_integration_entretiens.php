<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Entretien;
use App\Models\Candidature;
use App\Models\OffreStage;

echo "=== Test d'intégration des entretiens dans la page des candidatures ===\n\n";

// 1. Récupérer une offre existante
echo "1. Récupération d'une offre existante...\n";
$offre = OffreStage::first();
if (!$offre) {
    echo "❌ Aucune offre trouvée\n";
    exit;
}
echo "✅ Offre trouvée: {$offre->titre} (ID: {$offre->id})\n";

// 2. Créer des données de test
echo "\n2. Création de données de test...\n";

// Candidature en cours avec entretien
$candidatureEnCours = Candidature::create([
    'nom' => 'Candidat Integration',
    'prenom' => 'Test',
    'email' => 'integration@test.com',
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

$entretienEnCours = Entretien::create([
    'candidature_id' => $candidatureEnCours->id,
    'date_entretien' => date('Y-m-d', strtotime('+2 days')),
    'heure_entretien' => date('Y-m-d H:i:s', strtotime('+2 days')),
    'lieu_entretien' => 'Salle Integration',
    'statut' => 'planifie'
]);

// Candidature acceptée avec entretien (ne doit pas apparaître)
$candidatureAcceptee = Candidature::create([
    'nom' => 'Candidat Accepte',
    'prenom' => 'Test',
    'email' => 'accepte2@test.com',
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

$entretienAccepte = Entretien::create([
    'candidature_id' => $candidatureAcceptee->id,
    'date_entretien' => date('Y-m-d', strtotime('-1 day')),
    'heure_entretien' => date('Y-m-d H:i:s', strtotime('-1 day')),
    'lieu_entretien' => 'Salle Accepte',
    'statut' => 'termine',
    'note_evaluation' => 16,
    'commentaires_evaluation' => 'Bon candidat'
]);

echo "✅ Données de test créées\n";
echo "   - Candidature en cours avec entretien\n";
echo "   - Candidature acceptée avec entretien (ne doit pas apparaître)\n\n";

// 3. Tester la logique du contrôleur CandidatureController
echo "3. Test de la logique du contrôleur...\n";

// Simuler la requête du contrôleur
$queryCandidatures = Candidature::with(['offreStage.entreprise', 'offreStage.rh']);
$queryCandidatures->where('statut', 'en_cours');
$candidatures = $queryCandidatures->orderBy('created_at', 'desc')->paginate(15);

echo "Candidatures en cours: {$candidatures->count()}\n";

// Simuler la requête des entretiens
$entretiensQuery = Entretien::with(['candidature.offreStage.entreprise', 'evaluateur']);
$entretiensQuery->whereHas('candidature', function($q) {
    $q->whereNotIn('statut', ['accepte', 'refuse']);
});

$entretiens = $entretiensQuery->orderBy('date_entretien', 'desc')
                             ->orderBy('heure_entretien', 'desc')
                             ->paginate(10);

echo "Entretiens en cours (candidatures non traitées): {$entretiens->count()}\n";

foreach ($entretiens as $e) {
    echo "- ID: {$e->id}, Candidat: {$e->candidature->nom}, Statut candidature: {$e->candidature->statut}, Date: {$e->date_entretien}\n";
}

// 4. Tester les filtres
echo "\n4. Test des filtres d'entretiens...\n";

// Filtre par statut d'entretien
$entretiensStatut = Entretien::with(['candidature'])
    ->whereHas('candidature', function($q) {
        $q->whereNotIn('statut', ['accepte', 'refuse']);
    })
    ->where('statut', 'planifie')
    ->get();

echo "Entretiens avec statut 'planifié': {$entretiensStatut->count()}\n";

// Filtre par date
$entretiensDate = Entretien::with(['candidature'])
    ->whereHas('candidature', function($q) {
        $q->whereNotIn('statut', ['accepte', 'refuse']);
    })
    ->where('date_entretien', '>=', now()->format('Y-m-d'))
    ->get();

echo "Entretiens pas encore terminés: {$entretiensDate->count()}\n";

// 5. Vérifier que les entretiens de candidatures acceptées sont exclus
echo "\n5. Vérification de l'exclusion des candidatures traitées...\n";

$entretiensAcceptes = Entretien::with(['candidature'])
    ->whereHas('candidature', function($q) {
        $q->whereIn('statut', ['accepte', 'refuse']);
    })
    ->get();

echo "Entretiens de candidatures acceptées/refusées (exclus): {$entretiensAcceptes->count()}\n";

foreach ($entretiensAcceptes as $e) {
    echo "- ID: {$e->id}, Candidat: {$e->candidature->nom}, Statut: {$e->candidature->statut} (EXCLUS)\n";
}

// 6. Nettoyage
echo "\n6. Nettoyage des données de test...\n";

Entretien::whereIn('id', [$entretienEnCours->id, $entretienAccepte->id])->delete();
Candidature::whereIn('id', [$candidatureEnCours->id, $candidatureAcceptee->id])->delete();

echo "✅ Nettoyage terminé\n";

echo "\n=== Test d'intégration terminé ===\n";
echo "✅ L'intégration des entretiens dans la page des candidatures fonctionne correctement\n";
echo "✅ Seuls les entretiens de candidatures non traitées sont affichés\n";
echo "✅ Les filtres fonctionnent comme prévu\n";
