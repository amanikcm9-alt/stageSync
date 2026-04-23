<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Correction des stagiaires avec offre_stage_id NULL ===\n";

// 1. Trouver tous les stagiaires
$stagiaires = \App\Models\User::whereHas('role', function($q) {
    $q->where('name', 'stagiaire');
})->get();

echo "Nombre total de stagiaires: " . $stagiaires->count() . "\n\n";

$stagiairesCorriges = 0;

foreach ($stagiaires as $stagiaire) {
    echo "Stagiaire: " . $stagiaire->prenom . " " . $stagiaire->nom . " (ID: " . $stagiaire->id . ")\n";
    echo "  - offre_stage_id: " . ($stagiaire->offre_stage_id ?? 'NULL') . "\n";
    
    // Si offre_stage_id est NULL, chercher l'offre associée
    if (!$stagiaire->offre_stage_id) {
        echo "  - Recherche de l'offre associée...\n";
        
        // Méthode 1: Chercher via candidatures acceptées
        $candidature = \App\Models\Candidature::where('user_id', $stagiaire->id)
            ->where('statut', 'accepte')
            ->with('offreStage')
            ->first();
            
        if ($candidature && $candidature->offreStage) {
            $offre = $candidature->offreStage;
            echo "  - Offre trouvée via candidature: " . $offre->titre . " (ID: " . $offre->id . ")\n";
            
            // Mettre à jour le stagiaire
            $stagiaire->offre_stage_id = $offre->id;
            $stagiaire->save();
            echo "  - ✅ offre_stage_id mis à jour: " . $offre->id . "\n";
            $stagiairesCorriges++;
        } else {
            // Méthode 2: Chercher toutes les candidatures de ce stagiaire
            $toutesCandidatures = \App\Models\Candidature::where('user_id', $stagiaire->id)->get();
            echo "  - Candidatures trouvées: " . $toutesCandidatures->count() . "\n";
            
            foreach ($toutesCandidatures as $cand) {
                echo "    - Candidature ID: " . $cand->id . ", offre_stage_id: " . $cand->offre_stage_id . ", statut: " . $cand->statut . "\n";
            }
            
            // Méthode 3: Chercher la dernière offre créée
            $derniereOffre = \App\Models\OffreStage::latest()->first();
            if ($derniereOffre) {
                echo "  - Dernière offre disponible: " . $derniereOffre->titre . " (ID: " . $derniereOffre->id . ")\n";
                
                // Vérifier si le stagiaire a une candidature pour cette offre
                $aCandidature = \App\Models\Candidature::where('offre_stage_id', $derniereOffre->id)
                    ->where('user_id', $stagiaire->id)
                    ->exists();
                    
                if ($aCandidature) {
                    echo "  - Le stagiaire a une candidature pour cette offre\n";
                    
                    // Mettre à jour le stagiaire
                    $stagiaire->offre_stage_id = $derniereOffre->id;
                    $stagiaire->save();
                    echo "  - ✅ offre_stage_id mis à jour: " . $derniereOffre->id . "\n";
                    $stagiairesCorriges++;
                } else {
                    echo "  - ❌ Aucune candidature trouvée pour cette offre\n";
                }
            } else {
                echo "  - ❌ Aucune offre trouvée dans la base\n";
            }
            
            // Solution manuelle: Créer une candidature et lier l'offre
            if ($derniereOffre && !$stagiaire->offre_stage_id) {
                echo "  - Création d'une candidature manuelle...\n";
                
                // Créer la candidature
                \App\Models\Candidature::create([
                    'nom' => $stagiaire->nom,
                    'prenom' => $stagiaire->prenom,
                    'date_naissance' => now()->subYears(20),
                    'email' => $stagiaire->email,
                    'telephone' => $stagiaire->telephone ?? 'Non renseigné',
                    'adresse' => 'Adresse à compléter',
                    'dernier_diplome' => 'À compléter',
                    'etablissement' => 'Établissement à compléter',
                    'annee_diplome' => now()->year - 1,
                    'cv_path' => '',
                    'lettre_motivation_path' => '',
                    'lettre_motivation' => 'Candidature créée manuellement pour corriger la liaison',
                    'offre_stage_id' => $derniereOffre->id,
                    'message' => 'Stagiaire existant - liaison manuelle',
                    'statut' => 'accepte',
                    'date_decision' => now(),
                    'commentaire' => 'Liaison manuelle pour corriger offre_stage_id',
                    'stagiaire_id' => $stagiaire->id,
                ]);
                
                echo "  - ✅ Candidature créée avec ID\n";
                
                // Mettre à jour le stagiaire
                $stagiaire->offre_stage_id = $derniereOffre->id;
                $stagiaire->save();
                echo "  - ✅ offre_stage_id mis à jour: " . $derniereOffre->id . "\n";
                $stagiairesCorriges++;
            }
        }
    } else {
        echo "  - ✅ offre_stage_id déjà défini\n";
    }
    
    echo "\n";
}

echo "=== Résumé ===\n";
echo "Stagiaires corrigés: " . $stagiairesCorriges . "\n";
echo "Correction terminée.\n";
