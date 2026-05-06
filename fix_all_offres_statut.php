<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Correction de toutes les offres avec candidatures acceptées ===\n\n";
    
    // Récupérer toutes les offres avec des candidatures acceptées
    $offres = \App\Models\OffreStage::with('candidatures')->get();
    
    foreach ($offres as $offre) {
        $hasAcceptedCandidature = $offre->candidatures->contains('statut', 'accepte');
        
        echo "Offre ID: {$offre->id} - Titre: {$offre->titre}\n";
        echo "- Statut actuel: {$offre->statut}\n";
        echo "- Nombre de candidatures: " . $offre->candidatures->count() . "\n";
        echo "- Candidatures acceptées: " . $offre->candidatures->where('statut', 'accepte')->count() . "\n";
        
        if ($hasAcceptedCandidature && $offre->statut !== 'affectee') {
            echo "-> MISE À JOUR: Changement de statut vers 'affectee'\n";
            $offre->update(['statut' => 'affectee']);
            echo "- Nouveau statut: {$offre->statut}\n";
        } elseif ($hasAcceptedCandidature && $offre->statut === 'affectee') {
            echo "-> DÉJÀ CORRECT: Statut déjà 'affectee'\n";
        } else {
            echo "-> PAS DE CHANGEMENT: Pas de candidature acceptée\n";
        }
        
        echo "\n";
    }
    
    // Vérification finale
    echo "=== Vérification finale ===\n";
    $offresPubliees = \App\Models\OffreStage::publiee()->get();
    echo "Offres publiées après correction: " . $offresPubliees->count() . "\n";
    
    $offresAffectees = \App\Models\OffreStage::where('statut', 'affectee')->get();
    echo "Offres affectées: " . $offresAffectees->count() . "\n";
    
    // Détail des offres affectées
    echo "\nDétail des offres affectées:\n";
    foreach ($offresAffectees as $offre) {
        $acceptedCandidatures = $offre->candidatures->where('statut', 'accepte');
        echo "- {$offre->titre} (ID: {$offre->id}) - {$acceptedCandidatures->count()} candidature(s) acceptée(s)\n";
    }
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
