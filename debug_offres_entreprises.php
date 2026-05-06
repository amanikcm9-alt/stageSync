<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OffreStage;
use App\Models\Entreprise;

echo "=== Diagnostic des offres et entreprises ===\n\n";

// Vérifier les entreprises existantes
$entreprises = Entreprise::all();
echo "Nombre d'entreprises dans la base: " . $entreprises->count() . "\n";
foreach ($entreprises as $entreprise) {
    echo "- Entreprise ID: {$entreprise->id} - Nom: {$entreprise->nom}\n";
}

echo "\n";

// Vérifier les offres et leurs entreprises
$offres = OffreStage::all();
echo "Nombre d'offres dans la base: " . $offres->count() . "\n";

foreach ($offres as $offre) {
    echo "- Offre ID: {$offre->id} - Titre: {$offre->titre}\n";
    echo "  enterprise_id: " . ($offre->entreprise_id ?? 'NULL') . "\n";
    
    // Charger la relation
    $entreprise = $offre->entreprise;
    echo "  Entreprise (relation): " . ($entreprise ? $entreprise->nom : 'NULL') . "\n";
    
    // Vérifier avec la méthode statique
    $entrepriseDirect = Entreprise::find($offre->entreprise_id);
    echo "  Entreprise (direct): " . ($entrepriseDirect ? $entrepriseDirect->nom : 'NULL') . "\n";
    echo "\n";
}

echo "=== Vérification des offres avec entreprise_id non null ===\n";
$offresAvecEntreprise = OffreStage::whereNotNull('entreprise_id')->get();
echo "Offres avec entreprise_id non null: " . $offresAvecEntreprise->count() . "\n";

foreach ($offresAvecEntreprise as $offre) {
    $entreprise = $offre->entreprise;
    echo "- Offre {$offre->id}: entreprise_id={$offre->entreprise_id}, entreprise={$entreprise?->nom}\n";
}

echo "\n=== Test de chargement avec with() ===\n";
$offresWithEntreprise = OffreStage::with('entreprise')->get();
foreach ($offresWithEntreprise as $offre) {
    echo "- Offre {$offre->id}: {$offre->titre} -> " . ($offre->entreprise ? $offre->entreprise->nom : 'NULL') . "\n";
}
