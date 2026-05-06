<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Test du flux d'acceptation de candidature ===\n\n";
    
    // Créer une nouvelle candidature de test
    echo "1. Création d'une candidature de test...\n";
    
    $offre = \App\Models\OffreStage::where('statut', 'publiee')->first();
    if (!$offre) {
        echo "Aucune offre publiée trouvée. Création d'une offre de test...\n";
        $offre = \App\Models\OffreStage::create([
            'titre' => 'Offre Test Acceptation',
            'description' => 'Description test',
            'missions' => 'Missions test',
            'lieu' => 'Lieu test',
            'date_debut' => now()->addDays(30),
            'date_fin' => now()->addDays(90),
            'statut' => 'publiee',
            'entreprise_id' => 1,
            'rh_id' => 1,
        ]);
        echo "Offre créée: {$offre->titre} (ID: {$offre->id})\n";
    }
    
    $candidature = \App\Models\Candidature::create([
        'nom' => 'Test',
        'prenom' => 'Acceptation',
        'email' => 'test.acceptation@example.com',
        'telephone' => '0123456789',
        'adresse' => '123 rue test',
        'dernier_diplome' => 'Baccalauréat',
        'etablissement' => 'Lycée Test',
        'annee_diplome' => 2023,
        'lettre_motivation' => 'Ceci est une lettre de motivation de test.',
        'cv_path' => 'test_cv.pdf',
        'offre_stage_id' => $offre->id,
        'statut' => 'en_cours',
    ]);
    
    echo "Candidature créée: {$candidature->nom} {$candidature->prenom} (ID: {$candidature->id})\n";
    echo "Statut initial: {$candidature->statut}\n";
    echo "Offre associée: {$offre->titre} (statut: {$offre->statut})\n\n";
    
    // Simuler l'acceptation
    echo "2. Simulation de l'acceptation...\n";
    
    // Charger la relation pour tester
    $candidature->load('offreStage');
    
    // Mettre à jour le statut de la candidature
    $candidature->update([
        'statut' => 'accepte',
        'date_decision' => now(),
    ]);
    
    // Mettre à jour le statut de l'offre
    $offre = $candidature->offreStage;
    if ($offre) {
        $offre->update(['statut' => 'affectee']);
        echo "Offre mise à jour: {$offre->titre} (nouveau statut: {$offre->statut})\n";
    } else {
        echo "ERREUR: Offre non trouvée pour la candidature {$candidature->id}\n";
    }
    
    echo "Candidature mise à jour: {$candidature->nom} (nouveau statut: {$candidature->statut})\n\n";
    
    // Vérifier le résultat
    echo "3. Vérification du résultat...\n";
    
    $offresPubliees = \App\Models\OffreStage::publiee()->get();
    echo "Offres publiées après acceptation: " . $offresPubliees->count() . "\n";
    
    $offresAffectees = \App\Models\OffreStage::where('statut', 'affectee')->get();
    echo "Offres affectées: " . $offresAffectees->count() . "\n";
    
    // Vérifier si notre offre est bien affectée
    $offreTest = \App\Models\OffreStage::find($offre->id);
    echo "Notre offre de test: {$offreTest->titre} (statut: {$offreTest->statut})\n";
    
    if ($offreTest->statut === 'affectee') {
        echo "✅ SUCCÈS: Le changement de statut fonctionne correctement!\n";
    } else {
        echo "❌ ÉCHEC: Le changement de statut ne fonctionne pas.\n";
    }
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
    exit(1);
}
