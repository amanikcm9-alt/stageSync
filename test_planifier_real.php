<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Entretien;
use App\Models\Candidature;

echo "=== Test réel de la planification d'entretien ===\n\n";

// 1. Vérifier les entretiens existants
$entretiensAvant = Entretien::count();
echo "Nombre d'entretiens avant: $entretiensAvant\n";

// 2. Prendre une candidature
$candidature = Candidature::with(['offreStage'])->first();
if (!$candidature) {
    echo "❌ Aucune candidature trouvée\n";
    exit;
}

echo "Candidature test: {$candidature->nom} {$candidature->prenom} (ID: {$candidature->id})\n";

// 3. Simuler exactement ce que fait EntretienController@planifier
echo "\n3. Simulation de EntretienController@planifier...\n";

try {
    // Données du formulaire
    $requestData = [
        'date_entretien' => date('Y-m-d', strtotime('+3 days')),
        'heure_entretien' => '14:30',
        'lieu_entretien' => 'Salle de conférence',
        'notes_entretien' => 'Entretien technique'
    ];

    // Validation (comme dans le contrôleur)
    $errors = [];
    if (empty($requestData['date_entretien'])) $errors[] = 'Date requise';
    if (empty($requestData['heure_entretien'])) $errors[] = 'Heure requise';
    if (empty($requestData['lieu_entretien'])) $errors[] = 'Lieu requis';
    
    if (!empty($errors)) {
        echo "❌ Erreurs de validation: " . implode(', ', $errors) . "\n";
        exit;
    }

    // Créer l'entretien (comme dans le contrôleur)
    $entretien = Entretien::create([
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
    echo "   - Lieu: {$entretien->lieu_entretien}\n";
    echo "   - Statut: {$entretien->statut}\n";

    // Mettre à jour la candidature (comme dans le contrôleur)
    $candidature->update([
        'date_entretien' => $requestData['date_entretien'],
        'heure_entretien' => $requestData['date_entretien'] . ' ' . $requestData['heure_entretien'],
        'lieu_entretien' => $requestData['lieu_entretien'],
        'notes_entretien' => $requestData['notes_entretien']
    ]);

    echo "✅ Candidature mise à jour\n";

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
    
    // Vérifier s'il apparaît dans la requête de la liste (comme dans EntretienController@index)
    $query = Entretien::with(['candidature.offreStage.entreprise', 'evaluateur']);
    $entretiensListe = $query->orderBy('date_entretien', 'desc')
                           ->orderBy('heure_entretien', 'desc')
                           ->paginate(15);
    
    $trouveDansListe = $entretiensListe->contains('id', $entretien->id);
    echo $trouveDansListe ? "✅ Entretien trouvé dans la liste principale\n" : "❌ Entretien non trouvé dans la liste principale\n";
    
    if ($trouveDansListe) {
        $position = $entretiensListe->getCollection()->search(function($item) use ($entretien) {
            return $item->id === $entretien->id;
        }) + 1;
        echo "   - Position dans la liste: $position\n";
    }
    
    // Vérifier la liste récente (comme dans rh/candidatures/index.blade.php)
    $entretiensRecents = Entretien::with(['candidature.offreStage.entreprise'])
        ->orderBy('date_entretien', 'desc')
        ->orderBy('heure_entretien', 'desc')
        ->limit(5)
        ->get();

    $trouveDansRecent = $entretiensRecents->contains('id', $entretien->id);
    echo $trouveDansRecent ? "✅ Entretien trouvé dans la liste récente\n" : "❌ Entretien non trouvé dans la liste récente\n";
    
} else {
    echo "❌ Entretien non trouvé dans la base de données\n";
}

// 5. Afficher tous les entretiens pour debug
echo "\n5. Tous les entretiens actuels:\n";
$tousEntretiens = Entretien::with(['candidature'])->orderBy('created_at', 'desc')->get();
foreach ($tousEntretiens as $e) {
    echo "- ID: {$e->id}, Candidat: {$e->candidature->nom} {$e->candidature->prenom}, Statut: {$e->statut}, Date: {$e->date_entretien}, Créé: {$e->created_at}\n";
}

echo "\n=== Test terminé ===\n";
