<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Entretien;
use App\Models\Candidature;

echo "=== Debug de l'entretien ID 4 ===\n\n";

// 1. Récupérer l'entretien ID 4
echo "1. Récupération de l'entretien ID 4...\n";
$entretien = Entretien::find(4);

if (!$entretien) {
    echo "❌ L'entretien ID 4 n'existe pas\n";
    exit;
}

echo "✅ Entretien ID 4 trouvé\n";
echo "   - Statut: {$entretien->statut}\n";
echo "   - Date: {$entretien->date_entretien}\n";
echo "   - Heure: {$entretien->heure_entretien}\n";
echo "   - Lieu: {$entretien->lieu_entretien}\n";

// 2. Vérifier les méthodes du modèle
echo "\n2. Vérification des méthodes du modèle...\n";
echo "   - isPlanifié(): " . ($entretien->isPlanifie() ? 'VRAI' : 'FAUX') . "\n";
echo "   - isTermine(): " . ($entretien->isTermine() ? 'VRAI' : 'FAUX') . "\n";
echo "   - peutEtreEvalue(): " . ($entretien->peutEtreEvalue() ? 'VRAI' : 'FAUX') . "\n";

// 3. Vérifier la candidature associée
echo "\n3. Vérification de la candidature associée...\n";
$candidature = $entretien->candidature;
if ($candidature) {
    echo "✅ Candidature trouvée\n";
    echo "   - Nom: {$candidature->nom} {$candidature->prenom}\n";
    echo "   - Statut: {$candidature->statut}\n";
    echo "   - Email: {$candidature->email}\n";
} else {
    echo "❌ Aucune candidature associée\n";
}

// 4. Vérifier les constantes de statut
echo "\n4. Vérification des constantes de statut...\n";
echo "   - STATUT_PLANIFIE: " . \App\Models\Entretien::STATUT_PLANIFIE . "\n";
echo "   - STATUT_EN_COURS: " . \App\Models\Entretien::STATUT_EN_COURS . "\n";
echo "   - STATUT_TERMINE: " . \App\Models\Entretien::STATUT_TERMINE . "\n";
echo "   - STATUT_ANNULE: " . \App\Models\Entretien::STATUT_ANNULE . "\n";

// 5. Vérifier si le bouton devrait s'afficher
echo "\n5. Vérification de la condition d'affichage du bouton...\n";
$condition = $entretien->isPlanifie();
echo "   - Condition @if(\$entretien->isPlanifie()): " . ($condition ? 'VRAI - Bouton visible' : 'FAUX - Bouton caché') . "\n";

// 6. Vérifier si l'entretien est déjà évalué
echo "\n6. Vérification de l'évaluation...\n";
echo "   - evaluated_at: " . ($entretien->evaluated_at ? $entretien->evaluated_at : 'NULL') . "\n";
echo "   - note_evaluation: " . ($entretien->note_evaluation ?? 'NULL') . "\n";
echo "   - commentaires_evaluation: " . ($entretien->commentaires_evaluation ?? 'NULL') . "\n";

// 7. Afficher toutes les propriétés de l'entretien
echo "\n7. Propriétés complètes de l'entretien:\n";
foreach ($entretien->getAttributes() as $key => $value) {
    echo "   - $key: " . ($value ?? 'NULL') . "\n";
}

echo "\n=== Debug terminé ===\n";

// 8. Recommandation
echo "\nRecommandation:\n";
if ($entretien->isPlanifie()) {
    echo "✅ Le bouton devrait être visible. Vérifiez la vue show.blade.php\n";
} else {
    echo "❌ Le bouton ne sera pas visible car l'entretien n'est pas planifié.\n";
    echo "   - Statut actuel: {$entretien->statut}\n";
    echo "   - Pour afficher le bouton, le statut doit être: " . \App\Models\Entretien::STATUT_PLANIFIE . "\n";
}
