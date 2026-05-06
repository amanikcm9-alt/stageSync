<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Vérification de la structure de la table entretiens ===\n\n";

// Vérifier si la table existe
if (!Schema::hasTable('entretiens')) {
    echo "La table 'entretiens' n'existe pas !\n";
    exit;
}

// Obtenir la structure de la table
$columns = DB::select("DESCRIBE entretiens");

echo "Structure de la table 'entretiens':\n";
foreach ($columns as $column) {
    echo "- {$column->Field}: {$column->Type} (Null: {$column->Null}, Default: " . ($column->Default ?? 'NULL') . ")\n";
}

echo "\n";

// Vérifier spécifiquement le champ note_evaluation
$noteColumn = collect($columns)->firstWhere('Field', 'note_evaluation');
if ($noteColumn) {
    echo "Champ 'note_evaluation':\n";
    echo "- Type: {$noteColumn->Type}\n";
    echo "- Null: {$noteColumn->Null}\n";
    echo "- Default: " . ($noteColumn->Default ?? 'NULL') . "\n";
    
    // Analyser le type pour comprendre les limites
    if (strpos($noteColumn->Type, 'decimal') !== false) {
        echo "- Type décimal détecté\n";
        if (preg_match('/decimal\((\d+),(\d+)\)/', $noteColumn->Type, $matches)) {
            $total = $matches[1];
            $decimal = $matches[2];
            $integer = $total - $decimal;
            echo "- Total digits: $total, Decimal places: $decimal, Integer places: $integer\n";
            echo "- Valeur maximale: " . str_repeat('9', $integer) . '.' . str_repeat('9', $decimal) . "\n";
        }
    }
} else {
    echo "Champ 'note_evaluation' non trouvé !\n";
}

echo "\n=== Test d'insertion de valeurs ===\n";

// Tester différentes valeurs pour voir laquelle cause l'erreur
$testValues = [19, 19.5, 20, 20.0, 15, 10];

foreach ($testValues as $value) {
    try {
        // Créer un enregistrement de test
        $testId = DB::table('entretiens')->insertGetId([
            'candidature_id' => 1, // Supposer qu'il existe
            'date_entretien' => date('Y-m-d'),
            'heure_entretien' => date('Y-m-d H:i:s'),
            'lieu_entretien' => 'Test',
            'statut' => 'termine',
            'note_evaluation' => $value,
            'commentaires_evaluation' => 'Test comment',
            'evaluated_by' => 1,
            'evaluated_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Valeur $value: Insertion réussie (ID: $testId)\n";
        
        // Supprimer le test
        DB::table('entretiens')->delete($testId);
        
    } catch (Exception $e) {
        echo "❌ Valeur $value: Erreur - " . $e->getMessage() . "\n";
    }
}
