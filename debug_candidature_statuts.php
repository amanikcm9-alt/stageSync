<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Diagnostic des statuts de candidatures ===\n\n";
    
    // Vérifier tous les statuts existants
    $candidatures = \App\Models\Candidature::all();
    $statutsExistants = [];
    
    echo "Statuts existants dans la base de données:\n";
    foreach ($candidatures as $candidature) {
        $statutsExistants[] = $candidature->statut;
        echo "- Candidature ID: {$candidature->id} - Statut: '{$candidature->statut}'\n";
    }
    
    $statutsUniques = array_unique($statutsExistants);
    echo "\nStatuts uniques: " . implode(', ', $statutsUniques) . "\n";
    
    // Comparer avec les options de filtrage
    echo "\nOptions de filtrage dans la vue:\n";
    echo "- 'en_cours' => 'En cours'\n";
    echo "- 'recue' => 'Reçue'\n";
    echo "- 'acceptee' => 'Acceptée'\n";
    echo "- 'refusee' => 'Refusée'\n";
    echo "- 'toutes' => 'Toutes'\n";
    
    // Vérifier l'incohérence
    echo "\n=== Problèmes d'incohérence ===\n";
    foreach ($statutsUniques as $statut) {
        if ($statut === 'accepte') {
            echo "PROBLÈME: Statut 'accepte' dans la BD mais l'option de filtrage est 'acceptee'\n";
        }
        if ($statut === 'refuse') {
            echo "PROBLÈME: Statut 'refuse' dans la BD mais l'option de filtrage est 'refusee'\n";
        }
        if ($statut === 'recu') {
            echo "PROBLÈME: Statut 'recu' dans la BD mais l'option de filtrage est 'recue'\n";
        }
    }
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
