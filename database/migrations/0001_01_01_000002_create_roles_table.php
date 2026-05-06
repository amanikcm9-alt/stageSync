<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Création de la table et insertion des données).
     */
    public function up(): void
    {
        // Crée la table 'roles' pour définir les types d'utilisateurs
        Schema::create('roles', function (Blueprint $table) {
            
            // Identifiant unique du rôle (Clé primaire)
            $table->id(); 
            
            // Nom du rôle (ex: admin, stagiaire). Unique pour éviter les doublons.
            $table->string('name')->unique(); 
            
            // Description textuelle du rôle (optionnelle)
            $table->text('description')->nullable(); 
            
            // Stocke une liste de permissions au format JSON (ex: ["offres.manage"])
            $table->json('permissions')->nullable(); 
            
            // État du rôle : activé (true) ou désactivé (false). Activé par défaut.
            $table->boolean('active')->default(true); 
            
            // Crée les colonnes 'created_at' et 'updated_at' pour le suivi temporel
            $table->timestamps(); 
            
            // Index pour accélérer les recherches par nom et état d'activation
            $table->index(['name', 'active']); 
        });

        // --- Insertion automatique des rôles par défaut (Seeding) ---
        DB::table('roles')->insert([
            // Rôle Administrateur : possède toutes les permissions ('*')
            ['name' => 'admin', 'description' => 'Administrateur système', 'permissions' => json_encode(['*']), 'created_at' => now(), 'updated_at' => now()],
            
            // Rôle RH : peut gérer les entreprises, les offres et les candidatures
            ['name' => 'rh', 'description' => 'Ressources Humaines', 'permissions' => json_encode(['entreprises.manage', 'offres.manage', 'candidatures.manage']), 'created_at' => now(), 'updated_at' => now()],
            
            // Rôle Encadrant : peut gérer les activités, évaluations et discussions
            ['name' => 'encadrant', 'description' => 'Encadrant de stage', 'permissions' => json_encode(['activities.manage', 'evaluations.manage', 'discussions.manage']), 'created_at' => now(), 'updated_at' => now()],
            
            // Rôle Stagiaire : peut seulement voir ses activités et participer au chat
            ['name' => 'stagiaire', 'description' => 'Stagiaire', 'permissions' => json_encode(['activities.view', 'discussions.participate']), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations (Suppression de la table).
     */
    public function down(): void
    {
        // Supprime la table 'roles' si on annule la migration
        Schema::dropIfExists('roles'); 
    }
};