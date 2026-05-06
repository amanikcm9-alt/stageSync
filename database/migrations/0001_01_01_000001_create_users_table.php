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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Informations personnelles
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('date_naissance')->nullable();
            $table->string('telephone')->nullable();
            $table->text('adresse')->nullable();
            $table->string('photo_path')->nullable();
            
            // Rôle et relations (sans foreign keys pour l'instant)
            $table->foreignId('role_id')->nullable();
            $table->foreignId('encadrant_id')->nullable();
            $table->foreignId('encadrant_faculte_id')->nullable();
            $table->foreignId('encadrant_entreprise_id')->nullable();
            
            // Relations avec les offres et stages
            $table->foreignId('offre_stage_id')->nullable();
            $table->text('planning')->nullable();
            
            // Champs système
            $table->rememberToken();
            $table->timestamps();
            
            // Index
            $table->index(['role_id', 'email']);
            $table->index(['encadrant_id']);
            $table->index(['offre_stage_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
