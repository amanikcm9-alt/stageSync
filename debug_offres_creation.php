<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Diagnostic de la création d'offres ===\n\n";
    
    // Vérifier toutes les offres et leurs statuts
    $offres = \App\Models\OffreStage::all();
    echo "Toutes les offres dans la base de données:\n";
    foreach ($offres as $offre) {
        echo "- ID: {$offre->id} - Titre: {$offre->titre} - Statut: {$offre->statut}\n";
    }
    
    echo "\n=== Offres publiées ===\n";
    $offresPubliees = \App\Models\OffreStage::where('statut', 'publiee')->get();
    echo "Nombre d'offres publiées: " . $offresPubliees->count() . "\n";
    foreach ($offresPubliees as $offre) {
        echo "- {$offre->titre} (ID: {$offre->id})\n";
    }
    
    echo "\n=== Offres par statut ===\n";
    $statuts = \App\Models\OffreStage::selectRaw('statut, COUNT(*) as count')
        ->groupBy('statut')
        ->get();
    
    foreach ($statuts as $statut) {
        echo "- {$statut->statut}: {$statut->count} offre(s)\n";
    }
    
    echo "\n=== Création d'une offre de test ===\n";
    
    // Créer une offre de test pour voir le statut par défaut
    $nouvelleOffre = \App\Models\OffreStage::create([
        'titre' => 'Offre Test ' . date('H:i:s'),
        'description' => 'Description de test',
        'missions' => 'Missions de test',
        'lieu' => 'Lieu de test',
        'date_debut' => now()->addDays(30),
        'date_fin' => now()->addDays(90),
        'entreprise_id' => 1,
        'rh_id' => 1,
    ]);
    
    echo "Offre créée:\n";
    echo "- ID: {$nouvelleOffre->id}\n";
    echo "- Titre: {$nouvelleOffre->titre}\n";
    echo "- Statut par défaut: {$nouvelleOffre->statut}\n";
    
    // Vérifier si elle apparaît dans les offres publiées
    $publieesApresCreation = \App\Models\OffreStage::where('statut', 'publiee')->get();
    echo "\nOffres publiées après création: " . $publieesApresCreation->count() . "\n";
    
    // Tenter de la publier manuellement
    echo "\n=== Test de publication manuelle ===\n";
    $nouvelleOffre->update(['statut' => 'publiee']);
    echo "Offre mise à jour - Nouveau statut: {$nouvelleOffre->statut}\n";
    
    $publieesApresPublication = \App\Models\OffreStage::where('statut', 'publiee')->get();
    echo "Offres publiées après publication: " . $publieesApresPublication->count() . "\n";
    
    if ($publieesApresPublication->contains($nouvelleOffre)) {
        echo "✅ SUCCÈS: L'offre apparaît bien dans les offres publiées après publication manuelle.\n";
    } else {
        echo "❌ ÉCHEC: L'offre n'apparaît pas même après publication manuelle.\n";
    }
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
