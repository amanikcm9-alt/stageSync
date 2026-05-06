<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            
            // Informations personnelles du candidat
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance')->nullable();
            $table->string('email');
            $table->string('telephone');
            $table->text('adresse')->nullable();
            
            // Formation
            $table->string('dernier_diplome');
            $table->string('etablissement');
            $table->year('annee_diplome');
            
            // Documents
            $table->string('cv_path');
            $table->string('lettre_motivation_path');
            $table->text('lettre_motivation')->nullable(); // Champ texte pour la lettre
            $table->string('portfolio_path')->nullable();
            
            // Relations et statut
            $table->foreignId('offre_stage_id')->constrained()->onDelete('cascade');
            $table->enum('statut', ['recue', 'en_cours', 'accepte', 'refuse'])->default('recue');
            $table->text('motif_refus')->nullable();
            $table->date('date_decision')->nullable();
            
            // Entretien
            $table->dateTime('date_entretien')->nullable();
            $table->string('heure_entretien')->nullable();
            $table->string('lieu_entretien')->nullable();
            $table->text('notes_entretien')->nullable();
            
            // Messages et commentaires
            $table->text('message')->nullable();
            $table->text('commentaire')->nullable();
            
            // Archivage
            $table->timestamp('archived_at')->nullable();
            
            // Si accepté, lien vers le compte stagiaire créé
            $table->foreignId('stagiaire_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            $table->index(['statut', 'offre_stage_id']);
            $table->index(['email', 'offre_stage_id']);
            $table->index(['archived_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
};
