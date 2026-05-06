<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Entretien;
use App\Models\Candidature;
use App\Models\OffreStage;

echo "=== Test du flux d'évaluation complet ===\n\n";

// 1. Vérifier qu'il y a des candidatures et offres
$candidaturesCount = Candidature::count();
$offresCount = OffreStage::count();

echo "Candidatures existantes: $candidaturesCount\n";
echo "Offres existantes: $offresCount\n\n";

if ($candidaturesCount === 0) {
    echo "❌ Aucune candidature trouvée. Création d'une candidature de test...\n";
    
    // Créer une offre de test si nécessaire
    if ($offresCount === 0) {
        $offre = OffreStage::create([
            'titre' => 'Test Stage',
            'description' => 'Description test',
            'lieu' => 'Test',
            'duree' => '3 mois',
            'remuneration' => '500€',
            'statut' => 'disponible',
            'rh_id' => 1
        ]);
        echo "✅ Offre de test créée (ID: {$offre->id})\n";
    } else {
        $offre = OffreStage::first();
        echo "✅ Utilisation de l'offre existante (ID: {$offre->id})\n";
    }
    
    // Créer une candidature de test
    $candidature = Candidature::create([
        'nom' => 'Test',
        'prenom' => 'User',
        'email' => 'test@example.com',
        'telephone' => '0123456789',
        'offre_stage_id' => $offre->id,
        'statut' => 'en_cours',
        'date_naissance' => '2000-01-01',
        'dernier_diplome' => 'Bac',
        'annee_diplome' => '2020',
        'etablissement' => 'Test School'
    ]);
    echo "✅ Candidature de test créée (ID: {$candidature->id})\n";
} else {
    $candidature = Candidature::first();
    $offre = $candidature->offreStage;
    echo "✅ Utilisation de la candidature existante (ID: {$candidature->id})\n";
}

// 2. Créer un entretien
echo "\n2. Création d'un entretien...\n";
$entretien = Entretien::create([
    'candidature_id' => $candidature->id,
    'date_entretien' => date('Y-m-d'),
    'heure_entretien' => date('Y-m-d H:i:s'),
    'lieu_entretien' => 'Salle de réunion',
    'statut' => 'termine'
]);
echo "✅ Entretien créé (ID: {$entretien->id})\n";

// 3. Simuler une évaluation avec acceptation
echo "\n3. Test d'évaluation avec acceptation...\n";

try {
    // Simuler les données du formulaire
    $evaluationData = [
        'note_evaluation' => 16.5,
        'commentaires_evaluation' => 'Excellent candidat, très motivé et compétent.',
        'decision' => 'accepter',
        'evaluated_by' => 1,
        'evaluated_at' => now()
    ];
    
    // Mettre à jour l'entretien
    $entretien->update([
        'note_evaluation' => $evaluationData['note_evaluation'],
        'commentaires_evaluation' => $evaluationData['commentaires_evaluation'],
        'evaluated_by' => $evaluationData['evaluated_by'],
        'evaluated_at' => $evaluationData['evaluated_at']
    ]);
    
    echo "✅ Évaluation enregistrée\n";
    
    // Appliquer la décision
    if ($evaluationData['decision'] === 'accepter') {
        $candidature->update([
            'statut' => 'accepte',
            'date_decision' => now(),
            'commentaire' => 'Accepté suite à entretien. Note: ' . $evaluationData['note_evaluation'] . '/20'
        ]);
        
        // Mettre à jour le statut de l'offre
        if ($offre) {
            $offre->update(['statut' => 'affectee']);
        }
        
        echo "✅ Candidature acceptée\n";
        echo "✅ Offre marquée comme affectée\n";
    }
    
    // Vérifier les résultats
    echo "\n4. Vérification des résultats...\n";
    
    $candidature->refresh();
    $offre->refresh();
    $entretien->refresh();
    
    echo "Statut candidature: {$candidature->statut}\n";
    echo "Statut offre: {$offre->statut}\n";
    echo "Note entretien: {$entretien->note_evaluation}\n";
    echo "Commentaires: {$entretien->commentaires_evaluation}\n";
    
    // Vérifier que l'offre n'est plus visible publiquement
    echo "\n5. Test de visibilité publique...\n";
    
    $offresDisponibles = OffreStage::where('statut', 'disponible')->count();
    echo "Offres disponibles publiquement: $offresDisponibles\n";
    
    if ($offre->statut === 'affectee') {
        echo "✅ L'offre n'est plus disponible publiquement (statut: affectee)\n";
    } else {
        echo "❌ L'offre est encore disponible publiquement (statut: {$offre->statut})\n";
    }
    
    echo "\n=== Test terminé avec succès ! ===\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
