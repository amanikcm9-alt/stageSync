<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Entretien;
use App\Models\Candidature;
use App\Models\OffreStage;

echo "=== Test du bouton 'Planifier entretien' ===\n\n";

// 1. Vérifier les entretiens existants
$entretiensCount = Entretien::count();
echo "Nombre d'entretiens existants: $entretiensCount\n";

// Afficher les entretiens existants
$entretiensExistants = Entretien::with(['candidature.offreStage'])->get();
echo "\nEntretiens existants:\n";
foreach ($entretiensExistants as $entretien) {
    echo "- ID: {$entretien->id}, Candidat: {$entretien->candidature->nom} {$entretien->candidature->prenom}, Statut: {$entretien->statut}, Date: {$entretien->date_entretien}\n";
}

// 2. Simuler la planification d'un entretien
echo "\n2. Simulation de la planification d'un entretien...\n";

// Prendre une candidature existante
$candidature = Candidature::where('statut', '!=', 'accepte')->where('statut', '!=', 'refuse')->first();
if (!$candidature) {
    echo "❌ Aucune candidature disponible pour planifier un entretien\n";
    exit;
}

echo "Candidature sélectionnée: {$candidature->nom} {$candidature->prenom} (ID: {$candidature->id})\n";

// Simuler les données du formulaire de planification
$planificationData = [
    'date_entretien' => date('Y-m-d', strtotime('+3 days')),
    'heure_entretien' => '14:00',
    'lieu_entretien' => 'Salle de réunion A',
    'notes_entretien' => 'Entretien technique avec le RH'
];

try {
    // Créer l'entretien comme le fait le contrôleur
    $entretien = Entretien::create([
        'candidature_id' => $candidature->id,
        'date_entretien' => $planificationData['date_entretien'],
        'heure_entretien' => $planificationData['date_entretien'] . ' ' . $planificationData['heure_entretien'],
        'lieu_entretien' => $planificationData['lieu_entretien'],
        'notes_entretien' => $planificationData['notes_entretien'],
        'statut' => \App\Models\Entretien::STATUT_PLANIFIE
    ]);

    echo "✅ Entretien créé avec succès (ID: {$entretien->id})\n";
    echo "   - Date: {$entretien->date_entretien}\n";
    echo "   - Heure: {$entretien->heure_entretien}\n";
    echo "   - Lieu: {$entretien->lieu_entretien}\n";
    echo "   - Statut: {$entretien->statut}\n";

    // Mettre à jour la candidature comme le fait le contrôleur
    $candidature->update([
        'statut' => 'en_cours',
        'date_entretien' => $planificationData['date_entretien'],
        'heure_entretien' => $planificationData['date_entretien'] . ' ' . $planificationData['heure_entretien'],
        'lieu_entretien' => $planificationData['lieu_entretien'],
        'notes_entretien' => $planificationData['notes_entretien']
    ]);

    echo "✅ Candidature mise à jour\n";

} catch (Exception $e) {
    echo "❌ Erreur lors de la création de l'entretien: " . $e->getMessage() . "\n";
    exit;
}

// 3. Vérifier si l'entretien apparaît dans la liste
echo "\n3. Vérification de l'apparition dans la liste...\n";

// Simuler la requête du contrôleur EntretienController@index
$query = Entretien::with(['candidature.offreStage.entreprise', 'evaluateur']);

// Appliquer les mêmes filtres que le contrôleur
$entretiensListe = $query->orderBy('date_entretien', 'desc')
                        ->orderBy('heure_entretien', 'desc')
                        ->paginate(15);

echo "Nombre d'entretiens dans la liste: {$entretiensListe->total()}\n";

// Vérifier si notre nouvel entretien est dans la liste
$trouve = false;
foreach ($entretiensListe as $e) {
    if ($e->id === $entretien->id) {
        $trouve = true;
        echo "✅ Entretien trouvé dans la liste !\n";
        echo "   - Position: " . ($entretiensListe->getCollection()->search($e) + 1) . "\n";
        echo "   - Candidat: {$e->candidature->nom} {$e->candidature->prenom}\n";
        echo "   - Offre: " . ($e->candidature->offreStage->titre ?? 'N/A') . "\n";
        break;
    }
}

if (!$trouve) {
    echo "❌ L'entretien n'apparaît pas dans la liste !\n";
    
    // Debug: afficher tous les entretiens de la liste
    echo "\nEntretiens dans la liste:\n";
    foreach ($entretiensListe as $e) {
        echo "- ID: {$e->id}, Statut: {$e->statut}, Date: {$e->date_entretien}\n";
    }
}

// 4. Vérifier la liste dans la page des candidatures
echo "\n4. Vérification de l'affichage dans la page des candidatures...\n";

// Simuler la requête utilisée dans rh/candidatures/index.blade.php
$entretiensRecents = Entretien::with(['candidature.offreStage.entreprise'])
    ->orderBy('date_entretien', 'desc')
    ->orderBy('heure_entretien', 'desc')
    ->limit(5)
    ->get();

echo "Nombre d'entretiens récents: {$entretiensRecents->count()}\n";

$trouveRecent = false;
foreach ($entretiensRecents as $e) {
    if ($e->id === $entretien->id) {
        $trouveRecent = true;
        echo "✅ Entretien trouvé dans la liste récente !\n";
        break;
    }
}

if (!$trouveRecent) {
    echo "❌ L'entretien n'apparaît pas dans la liste récente !\n";
}

echo "\n=== Test terminé ===\n";
