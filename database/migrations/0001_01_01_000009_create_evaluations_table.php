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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('commentaires')->nullable();
            $table->decimal('note_technique', 5, 2)->nullable(); // Note sur 20
            $table->decimal('note_comportement', 5, 2)->nullable(); // Note sur 20
            $table->decimal('note_generale', 5, 2)->nullable(); // Note sur 20
            $table->enum('statut', ['brouillon', 'soumise', 'validee'])->default('brouillon');
            
            // Critères d'évaluation
            $table->text('critere1')->nullable();
            $table->text('critere2')->nullable();
            $table->text('critere3')->nullable();
            $table->text('critere4')->nullable();
            $table->text('critere5')->nullable();
            
            // Relations
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('evaluateur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('stagiaire_id')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            
            $table->index(['activity_id', 'statut']);
            $table->index(['evaluateur_id']);
            $table->index(['stagiaire_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
