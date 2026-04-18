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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->text('objectifs')->nullable();
            $table->enum('statut', ['proposee', 'assignee', 'en_cours', 'soumise', 'validee', 'refusee', 'terminee'])->default('proposee');
            $table->enum('priorite', ['basse', 'moyenne', 'haute', 'urgente'])->default('moyenne');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->date('date_limite')->nullable();
            $table->text('livrables_attendus')->nullable();
            $table->text('commentaires')->nullable();
            $table->integer('progression')->default(0); // 0-100
            
            // Relations
            $table->foreignId('encadrant_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('stagiaire_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('offre_stage_id')->nullable()->constrained('offre_stages')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Timestamps
            $table->timestamps();
            $table->timestamp('date_soumission')->nullable();
            $table->timestamp('date_validation')->nullable();
            
            // Index
            $table->index(['statut', 'encadrant_id']);
            $table->index(['stagiaire_id', 'statut']);
            $table->index(['offre_stage_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
