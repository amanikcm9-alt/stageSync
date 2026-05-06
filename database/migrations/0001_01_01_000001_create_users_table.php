<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Création de la table).
     */
    public function up(): void
    {
        // Crée la table 'users' dans la base de données
        Schema::create('users', function (Blueprint $table) {
            
            // Crée une colonne 'id' auto-incrémentée (Clé primaire)
            $table->id(); 
            
            // --- Informations personnelles ---
            
            // Colonne pour le nom de famille (type texte court)
            $table->string('nom'); 
            
            // Colonne pour le prénom (type texte court)
            $table->string('prenom'); 
            
            // Email unique : deux utilisateurs ne peuvent pas avoir le même email
            $table->string('email')->unique(); 
            
            // Stocke la date de vérification de l'email (peut être vide au début)
            $table->timestamp('email_verified_at')->nullable(); 
            
            // Colonne pour stocker le mot de passe crypté
            $table->string('password'); 
            
            // Date de naissance (optionnelle grâce à nullable)
            $table->date('date_naissance')->nullable(); 
            
            // Numéro de téléphone (optionnel)
            $table->string('telephone')->nullable(); 
            
            // Adresse complète (type text pour de longs paragraphes, optionnel)
            $table->text('adresse')->nullable(); 
            
            // Chemin vers le fichier de la photo de profil (optionnel)
            $table->string('photo_path')->nullable(); 
            
            // --- Rôle et relations ---
            
            // Lien vers la table 'roles' pour savoir si c'est un stagiaire ou encadrant
            $table->foreignId('role_id')->nullable(); 
            
            // Identifiant de l'encadrant principal lié à cet utilisateur
            $table->foreignId('encadrant_id')->nullable(); 
            
            // Identifiant spécifique pour l'encadrant côté ISET
            $table->foreignId('encadrant_faculte_id')->nullable(); 
            
            // Identifiant spécifique pour le tuteur côté entreprise
            $table->foreignId('encadrant_entreprise_id')->nullable(); 
            
            // --- Relations avec les offres et stages ---
            
            // Indique l'offre de stage que l'étudiant a choisie
            $table->foreignId('offre_stage_id')->nullable(); 
            
            // Planning ou calendrier des tâches (format texte long, optionnel)
            $table->text('planning')->nullable(); 
            
            // --- Champs système ---
            
            // Jeton pour la fonction "Se souvenir de moi" lors de la connexion
            $table->rememberToken(); 
            
            // Crée automatiquement les colonnes 'created_at' et 'updated_at'
            $table->timestamps(); 
            
            // --- Index (Pour accélérer les recherches en base de données) ---
            
            // Accélère la recherche par rôle et par email (très utilisé pour le login)
            $table->index(['role_id', 'email']); 
            
            // Accélère la recherche pour savoir quels étudiants un encadrant possède
            $table->index(['encadrant_id']); 
            
            // Accélère la recherche des utilisateurs par offre de stage
            $table->index(['offre_stage_id']); 
        });
    }

    /**
     * Reverse the migrations (Suppression de la table).
     */
    public function down(): void
    {
        // Supprime la table 'users' si on veut annuler la migration
        Schema::dropIfExists('users'); 
    }
};