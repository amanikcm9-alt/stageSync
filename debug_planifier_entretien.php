<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Entretien;
use App\Models\Candidature;

echo "=== Debug de la planification d'entretien ===\n\n";

// 1. Vérifier les entretiens avant test
$entretiensAvant = Entretien::count();
echo "Nombre d'entretiens avant: $entretiensAvant\n";

// 2. Prendre une candidature pour le test
$candidature = Candidature::with(['offreStage'])->first();
if (!$candidature) {
    echo "❌ Aucune candidature trouvée\n";
    exit;
}

echo "Candidature test: {$candidature->nom} {$candidature->prenom} (ID: {$candidature->id})\n";
echo "Statut actuel: {$candidature->statut}\n";

// 3. Simuler exactement ce que fait le contrôleur
echo "\n3. Simulation de la planification...\n";

try {
    // Données du formulaire
    $requestData = [
        'date_entretien' => date('Y-m-d', strtotime('+2 days')),
        'heure_entretien' => '10:00',
        'lieu_entretien' => 'Salle B',
        'notes_entretien' => 'Test de planification'
    ];

    // Créer l'entretien dans la table entretiens (comme dans le contrôleur)
    $entretien = \App\Models\Entretien::create([
        'candidature_id' => $candidature->id,
        'date_entretien' => $requestData['date_entretien'],
        'heure_entretien' => $requestData['date_entretien'] . ' ' . $requestData['heure_entretien'],
        'lieu_entretien' => $requestData['lieu_entretien'],
        'notes_entretien' => $requestData['notes_entretien'],
        'statut' => \App\Models\Entretien::STATUT_PLANIFIE
    ]);

    echo "✅ Entretien créé (ID: {$entretien->id})\n";
    echo "   - Date: {$entretien->date_entretien}\n";
    echo "   - Heure: {$entretien->heure_entretien}\n";
    echo "   - Statut: {$entretien->statut}\n";

    // Mettre à jour la candidature (comme dans le contrôleur)
    $candidature->update([
        'statut' => 'en_cours',
        'date_entretien' => $requestData['date_entretien'],
        'heure_entretien' => $requestData['date_entretien'] . ' ' . $requestData['heure_entretien'],
        'lieu_entretien' => $requestData['lieu_entretien'],
        'notes_entretien' => $requestData['notes_entretien']
    ]);

    echo "✅ Candidature mise à jour\n";
    echo "   - Nouveau statut: {$candidature->fresh()->statut}\n";

} catch (Exception $e) {
    echo "❌ Erreur lors de la création: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit;
}

// 4. Vérifier si l'entretien apparaît dans la liste
echo "\n4. Vérification dans la liste des entretiens...\n";

$entretiensApres = Entretien::count();
echo "Nombre d'entretiens après: $entretiensApres\n";

if ($entretiensApres > $entretiensAvant) {
    echo "✅ Nouvel entretien ajouté à la base de données\n";
} else {
    echo "❌ Aucun nouvel entretien ajouté\n";
}

// Vérifier spécifiquement notre entretien
$entretienTrouve = Entretien::find($entretien->id);
if ($entretienTrouve) {
    echo "✅ Entretien trouvé dans la base de données\n";
    
    // Vérifier s'il apparaît dans la requête de la liste
    $entretiensListe = Entretien::with(['candidature.offreStage.entreprise'])
        ->orderBy('date_entretien', 'desc')
        ->orderBy('heure_entretien', 'desc')
        ->get();
    
    $trouveDansListe = $entretiensListe->contains('id', $entretien->id);
    echo $trouveDansListe ? "✅ Entretien trouvé dans la liste affichée\n" : "❌ Entretien non trouvé dans la liste affichée\n";
    
    if ($trouveDansListe) {
        $position = $entretiensListe->search(function($item) use ($entretien) {
            return $item->id === $entretien->id;
        }) + 1;
        echo "   - Position dans la liste: $position\n";
    }
} else {
    echo "❌ Entretien non trouvé dans la base de données\n";
}

// 5. Vérifier la liste dans la page des candidatures
echo "\n5. Vérification dans la page des candidatures...\n";

$entretiensRecents = Entretien::with(['candidature.offreStage.entreprise'])
    ->orderBy('date_entretien', 'desc')
    ->orderBy('heure_entretien', 'desc')
    ->limit(5)
    ->get();

$trouveDansRecent = $entretiensRecents->contains('id', $entretien->id);
echo $trouveDansRecent ? "✅ Entretien trouvé dans la liste récente\n" : "❌ Entretien non trouvé dans la liste récente\n";

// 6. Debug: afficher tous les entretiens
echo "\n6. Liste complète des entretiens:\n";
$tousEntretiens = Entretien::with(['candidature'])->get();
foreach ($tousEntretiens as $e) {
    echo "- ID: {$e->id}, Candidat: {$e->candidature->nom} {$e->candidature->prenom}, Statut: {$e->statut}, Date: {$e->date_entretien}\n";
}

echo "\n=== Debug terminé ===\n";
