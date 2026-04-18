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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('fichier_path');
            $table->enum('type', ['support', 'ressource', 'modele', 'autre'])->default('support');
            $table->enum('statut', ['brouillon', 'publie', 'archive'])->default('brouillon');
            $table->integer('taille_octets')->nullable();
            $table->string('mime_type')->nullable();
            
            // Relations
            $table->foreignId('activity_id')->nullable()->constrained('activities')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            
            $table->index(['type', 'statut']);
            $table->index(['uploaded_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
