<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Structure de la table candidatures ===\n\n";

// Récupérer la structure de la table candidatures
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('candidatures');

echo "Colonnes de la table candidatures :\n";
foreach ($columns as $column) {
    $columnType = \Illuminate\Support\Facades\Schema::getColumnType('candidatures', $column);
    $nullable = !\Illuminate\Support\Facades\Schema::getConnection()
        ->getDoctrineColumn('candidatures', $column)
        ->getNotnull();
    
    echo "- {$column} ({$columnType})" . ($nullable ? " - NULLABLE" : " - NOT NULL") . "\n";
}

echo "\n=== Vérification du champ cv_path ===\n";

// Vérifier si cv_path peut être null
try {
    $cvPathType = \Illuminate\Support\Facades\Schema::getColumnType('candidatures', 'cv_path');
    $cvPathNullable = !\Illuminate\Support\Facades\Schema::getConnection()
        ->getDoctrineColumn('candidatures', 'cv_path')
        ->getNotnull();
    
    echo "Type de cv_path : {$cvPathType}\n";
    echo "cv_path nullable : " . ($cvPathNullable ? 'OUI' : 'NON') . "\n";
    
    if (!$cvPathNullable) {
        echo "\n💡 Solution : Rendre cv_path nullable dans la migration\n";
    }
    
} catch (\Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
}
