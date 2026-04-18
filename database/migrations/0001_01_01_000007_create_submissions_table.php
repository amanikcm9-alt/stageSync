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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('fichier_path'); // Chemin vers le fichier soumis
            $table->enum('type', ['rapport', 'presentation', 'code', 'autre'])->default('autre');
            $table->enum('statut', ['soumis', 'en_revision', 'valide', 'refuse'])->default('soumis');
            $table->text('commentaires')->nullable();
            $table->decimal('note', 5, 2)->nullable(); // Note sur 20 ou 100
            
            // Relations
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('stagiaire_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('encadrant_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Timestamps
            $table->timestamp('date_soumission')->nullable();
            $table->timestamp('date_evaluation')->nullable();
            $table->timestamps();
            
            // Index
            $table->index(['activity_id', 'statut']);
            $table->index(['stagiaire_id', 'date_soumission']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
