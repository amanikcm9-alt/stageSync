<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== Correction du champ note_evaluation ===\n";

try {
    // Modifier le champ note_evaluation
    Schema::table('entretiens', function ($table) {
        $table->decimal('note_evaluation', 5, 2)->change();
    });
    
    echo "✅ Champ note_evaluation modifié avec succès vers decimal(5,2)\n";
    
    // Vérifier la nouvelle structure
    $columns = DB::select("DESCRIBE entretiens");
    $noteColumn = collect($columns)->firstWhere('Field', 'note_evaluation');
    
    if ($noteColumn) {
        echo "✅ Nouvelle structure du champ note_evaluation: {$noteColumn->Type}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== Test d'insertion avec la nouvelle structure ===\n";

// Tester l'insertion avec une note de 19
try {
    $testId = DB::table('entretiens')->insertGetId([
        'candidature_id' => 1,
        'date_entretien' => date('Y-m-d'),
        'heure_entretien' => date('Y-m-d H:i:s'),
        'lieu_entretien' => 'Test',
        'statut' => 'termine',
        'note_evaluation' => 19.5,
        'commentaires_evaluation' => 'Test comment',
        'evaluated_by' => 1,
        'evaluated_at' => now(),
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "✅ Insertion réussie avec note 19.5 (ID: $testId)\n";
    
    // Nettoyer le test
    DB::table('entretiens')->delete($testId);
    echo "✅ Test nettoyé\n";
    
} catch (Exception $e) {
    echo "❌ Erreur d'insertion: " . $e->getMessage() . "\n";
}
