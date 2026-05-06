<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Création de la table entreprise).
     */
    public function up(): void
    {
        // Crée la table 'entreprises' dans la base de données
        Schema::create('entreprises', function (Blueprint $table) {
            
            // Identifiant unique de l'entreprise (Clé primaire)
            $table->id(); 
            
            // Nom de l'entreprise (ex: "Monoprix", "Société X")
            $table->string('nom'); 
            
            // Description des activités de l'entreprise (optionnel)
            $table->text('description')->nullable(); 
            
            // Le domaine de l'entreprise (ex: Informatique, Commerce, Industrie)
            $table->string('secteur_activite')->nullable(); 
            
            // Adresse physique du siège ou de l'agence
            $table->string('adresse'); 
            
            // Ville où se situe l'entreprise (ex: Nabeul, Tunis)
            $table->string('ville'); 
            
            // Code postal de la localité
            $table->string('code_postal'); 
            
            // Pays de l'entreprise (par défaut mis sur 'France', tu peux le changer en 'Tunisie')
            $table->string('pays')->default('France'); 
            
            // Numéro de téléphone de contact de l'entreprise
            $table->string('telephone'); 
            
            // Adresse email de contact professionnel (optionnel)
            $table->string('email')->nullable(); 
            
            // Lien vers le site internet de l'entreprise (optionnel)
            $table->string('site_web')->nullable(); 
            
            // Chemin vers le fichier logo de l'entreprise pour l'affichage
            $table->string('logo_path')->nullable(); 
            
            // Détails spécifiques sur les modalités de stage propres à cette boîte
            $table->text('conditions_stage')->nullable(); 
            
            // Document ou texte expliquant les règles à suivre dans l'entreprise
            $table->text('reglement_interne')->nullable(); 
            
            // État de l'entreprise : active (true) ou en pause (false)
            $table->boolean('active')->default(true); 
            
            // Crée les colonnes 'created_at' et 'updated_at' pour le suivi
            $table->timestamps(); 
            
            // Index pour accélérer la recherche des entreprises actives par leur nom
            $table->index(['active', 'nom']); 
        });
    }

    /**
     * Reverse the migrations (Annulation).
     */
    public function down(): void
    {
        // Supprime la table 'entreprises' si on fait un rollback
        Schema::dropIfExists('entreprises'); 
    }
};