<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Vérification des noms de tables ===\n\n";

// Récupérer toutes les tables de la base de données
$tables = \Illuminate\Support\Facades\Schema::getTableListing();

echo "Tables trouvées dans la base :\n";
foreach ($tables as $table) {
    echo "- {$table}\n";
}

echo "\n=== Recherche des tables liées aux offres ===\n";
$offresTables = array_filter($tables, function($table) {
    return strpos($table, 'offre') !== false || strpos($table, 'stage') !== false;
});

foreach ($offresTables as $table) {
    echo "- {$table}\n";
}

echo "\n=== Vérification du modèle OffreStage ===\n";
try {
    $offre = new App\Models\OffreStage();
    echo "Table du modèle OffreStage : " . $offre->getTable() . "\n";
    
    // Test de requête simple
    $count = \Illuminate\Support\Facades\DB::table($offre->getTable())->count();
    echo "Nombre d'enregistrements : " . $count . "\n";
    
} catch (\Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
}

echo "\n=== Test de requête directe ===\n";
try {
    // Essayer avec différents noms de table
    $possibleTables = ['offres_stages', 'offres', 'stage_offres', 'offre_stages'];
    
    foreach ($possibleTables as $tableName) {
        if (in_array($tableName, $tables)) {
            $count = \Illuminate\Support\Facades\DB::table($tableName)->count();
            echo "Table '{$tableName}' : {$count} enregistrements\n";
        }
    }
} catch (\Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
}
