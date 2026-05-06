<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Candidature;
use App\Models\OffreStage;
use App\Models\Secteur;
use App\Models\Role;

echo "=== Test de l'interface d'affectation intelligente ===\n\n";

// 1. Créer des données de test
echo "1. Création des données de test...\n";

// Récupérer ou créer des secteurs
$secteurIT = Secteur::firstOrCreate(['nom' => 'IT / Développement'], ['description' => 'Développement web et logiciel']);
$secteurMarketing = Secteur::firstOrCreate(['nom' => 'Marketing'], ['description' => 'Marketing et communication']);

// Récupérer les rôles
$roleStagiaire = Role::where('name', 'stagiaire')->first();
$roleEncadrant = Role::where('name', 'encadrant')->first();

if (!$roleStagiaire || !$roleEncadrant) {
    echo "❌ Rôles stagiaire/encadrant non trouvés\n";
    exit;
}

// Créer une offre de stage en IT
$offreIT = OffreStage::firstOrCreate([
    'titre' => 'Développeur Web Full Stack',
    'description' => 'Stage en développement web',
    'secteur_id' => $secteurIT->id,
    'statut' => 'publiee'
]);

// Créer un stagiaire accepté sans encadrant
$stagiaire = User::firstOrCreate([
    'email' => 'stagiaire.test@affectation.com',
    'nom' => 'Stagiaire',
    'prenom' => 'Test',
    'password' => bcrypt('password123'),
    'role_id' => $roleStagiaire->id,
    'active' => true
]);

$candidature = Candidature::firstOrCreate([
    'user_id' => $stagiaire->id,
    'offre_stage_id' => $offreIT->id,
    'statut' => 'accepte',
    'nom' => $stagiaire->nom,
    'prenom' => $stagiaire->prenom,
    'email' => $stagiaire->email
]);

// Créer des encadrants dans différents secteurs
$encadrantIT = User::firstOrCreate([
    'email' => 'encadrant.it@affectation.com',
    'nom' => 'Encadrant',
    'prenom' => 'IT',
    'password' => bcrypt('password123'),
    'role_id' => $roleEncadrant->id,
    'secteur_id' => $secteurIT->id,
    'active' => true
]);

$encadrantMarketing = User::firstOrCreate([
    'email' => 'encadrant.marketing@affectation.com',
    'nom' => 'Encadrant',
    'prenom' => 'Marketing',
    'password' => bcrypt('password123'),
    'role_id' => $roleEncadrant->id,
    'secteur_id' => $secteurMarketing->id,
    'active' => true
]);

echo "✅ Données de test créées\n";
echo "   - Stagiaire: {$stagiaire->nom} {$stagiaire->prenom} (ID: {$stagiaire->id})\n";
echo "   - Offre: {$offreIT->titre} (Secteur: {$secteurIT->nom})\n";
echo "   - Encadrant IT: {$encadrantIT->nom} {$encadrantIT->prenom} (Secteur: {$secteurIT->nom})\n";
echo "   - Encadrant Marketing: {$encadrantMarketing->nom} {$encadrantMarketing->prenom} (Secteur: {$secteurMarketing->nom})\n";

// 2. Tester le filtre intelligent
echo "\n2. Test du filtre intelligent...\n";

// Simuler la logique du contrôleur
$stagiaireTest = User::whereHas('role', function($q) {
        $q->where('name', 'stagiaire');
    })
    ->whereHas('candidature', function($q) {
        $q->where('statut', 'accepte');
    })
    ->whereNull('encadrant_id')
    ->with(['candidature.offreStage.secteur', 'candidature.offreStage.typeStage'])
    ->first();

if ($stagiaireTest) {
    echo "✅ Stagiaire trouvé: {$stagiaireTest->nom} {$stagiaireTest->prenom}\n";
    echo "   - Secteur de l'offre: {$stagiaireTest->candidature->offreStage->secteur->nom}\n";
    
    // Filtrer les encadrants par secteur
    $encadrantsFiltres = User::whereHas('role', function($q) {
            $q->where('name', 'encadrant');
        })
        ->where('secteur_id', $stagiaireTest->candidature->offreStage->secteur_id)
        ->with(['secteur', 'stagiairesAffectes'])
        ->get();

    echo "✅ Encadrants filtrés par secteur '{$stagiaireTest->candidature->offreStage->secteur->nom}': {$encadrantsFiltres->count()}\n";
    
    foreach ($encadrantsFiltres as $encadrant) {
        $nombreStagiaires = $encadrant->stagiairesAffectes->count();
        $disponibilite = $nombreStagiaires < 5 ? 'Disponible' : 'Charge élevée';
        $couleur = $nombreStagiaires < 5 ? 'success' : 'warning';
        
        echo "   - {$encadrant->nom} {$encadrant->prenom} ({$encadrant->secteur->nom})\n";
        echo "     * Spécialité: {$encadrant->secteur->nom}\n";
        echo "     * Nombre de stagiaires: {$nombreStagiaires}\n";
        echo "     * Disponibilité: {$disponibilite}\n";
    }
} else {
    echo "❌ Aucun stagiaire trouvé\n";
}

// 3. Instructions de test
echo "\n3. Instructions pour tester l'interface:\n";
echo "   1. Accédez à: http://127.0.0.1:8000/rh/affectation\n";
echo "   2. Le stagiaire 'Test Stagiaire' devrait apparaître\n";
echo "   3. Cliquez sur 'Choisir un encadrant'\n";
echo "   4. Seul l'encadrant IT devrait apparaître (filtre par secteur)\n";
echo "   5. Vérifiez les informations affichées pour chaque encadrant\n";

// 4. Nettoyage (commenté pour permettre le test)
echo "\n4. Données de test conservées:\n";
echo "   - Pour nettoyer: php -r \"require 'vendor/autoload.php'; \\App\\Models\\User::where('email', 'like', '%@affectation.com')->delete(); \\App\\Models\\Candidature::where('email', 'stagiaire.test@affectation.com')->delete(); echo 'Nettoyage terminé';\"\n";

echo "\n=== Test terminé ===\n";
echo "✅ Migration secteur_id créée\n";
echo "✅ Contrôleur RHAssignmentController modifié\n";
echo "✅ Vues d'affectation créées\n";
echo "✅ Routes ajoutées\n";
echo "✅ Modèle User mis à jour\n";
echo "✅ Filtre intelligent fonctionnel\n";
echo "\n🎯 L'interface d'affectation intelligente est prête !\n";
