<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== Rendre le champ note_evaluation nullable ===\n";

try {
    // Rendre le champ note_evaluation nullable pour permettre les entretiens sans évaluation
    Schema::table('entretiens', function ($table) {
        $table->decimal('note_evaluation', 5, 2)->nullable()->change();
    });
    
    echo "✅ Champ note_evaluation rendu nullable\n";
    
    // Vérifier la nouvelle structure
    $columns = DB::select("DESCRIBE entretiens");
    $noteColumn = collect($columns)->firstWhere('Field', 'note_evaluation');
    
    if ($noteColumn) {
        echo "✅ Structure du champ note_evaluation: {$noteColumn->Type} (Null: {$noteColumn->Null})\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== Test de création d'entretien sans note ===\n";

// Tester la création d'un entretien sans note
try {
    $testId = DB::table('entretiens')->insertGetId([
        'candidature_id' => 1,
        'date_entretien' => date('Y-m-d'),
        'heure_entretien' => date('Y-m-d H:i:s'),
        'lieu_entretien' => 'Salle de réunion',
        'statut' => 'termine',
        'note_evaluation' => null, // Explicitement null
        'commentaires_evaluation' => null,
        'evaluated_by' => null,
        'evaluated_at' => null,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "✅ Entretien créé sans note (ID: $testId)\n";
    
    // Nettoyer le test
    DB::table('entretiens')->delete($testId);
    echo "✅ Test nettoyé\n";
    
} catch (Exception $e) {
    echo "❌ Erreur de création: " . $e->getMessage() . "\n";
}
