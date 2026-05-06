<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Création d'une candidature de test 'en_cours' ===\n\n";
    
    // Vérifier s'il y a des offres disponibles
    $offres = \App\Models\OffreStage::all();
    if ($offres->isEmpty()) {
        echo "ERREUR: Aucune offre disponible. Créez d'abord une offre.\n";
        exit(1);
    }
    
    $offre = $offres->first();
    echo "Utilisation de l'offre: {$offre->titre} (ID: {$offre->id})\n";
    
    // Créer une candidature avec statut 'en_cours'
    $candidature = \App\Models\Candidature::create([
        'nom' => 'Test',
        'prenom' => 'En Cours',
        'email' => 'test.encours@example.com',
        'telephone' => '0123456789',
        'adresse' => '123 rue test',
        'dernier_diplome' => 'Baccalauréat',
        'etablissement' => 'Lycée Test',
        'annee_diplome' => 2023,
        'lettre_motivation' => 'Ceci est une lettre de motivation de test.',
        'cv_path' => 'test_cv.pdf', // Champ requis
        'offre_stage_id' => $offre->id,
        'statut' => 'en_cours',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Candidature créée avec succès !\n";
    echo "- ID: {$candidature->id}\n";
    echo "- Nom: {$candidature->nom} {$candidature->prenom}\n";
    echo "- Email: {$candidature->email}\n";
    echo "- Statut: {$candidature->statut}\n";
    echo "- Offre: {$candidature->offreStage->titre}\n";
    
    // Vérifier que la candidature apparaît bien dans le filtrage
    echo "\n=== Vérification du filtrage ===\n";
    
    $candidaturesEnCours = \App\Models\Candidature::where('statut', 'en_cours')->get();
    echo "Nombre de candidatures 'en_cours': " . $candidaturesEnCours->count() . "\n";
    
    foreach ($candidaturesEnCours as $c) {
        echo "- {$c->nom} {$c->prenom} (ID: {$c->id})\n";
    }
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
