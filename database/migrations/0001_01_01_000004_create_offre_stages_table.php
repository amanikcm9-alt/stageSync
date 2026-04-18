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
        Schema::create('offre_stages', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->text('missions');
            $table->string('secteur')->nullable();
            $table->string('lieu');
            $table->integer('duree_semaines');
            $table->decimal('remuneration', 8, 2)->nullable();
            $table->enum('statut', ['brouillon', 'publiee', 'cloturee'])->default('brouillon');
            $table->enum('type_stage', ['entreprise', 'pfe', 'initiation', 'perfectionnement', 'benefolat'])->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            
            // Relations
            $table->foreignId('entreprise_id')->constrained()->onDelete('cascade');
            $table->foreignId('rh_id')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            
            $table->index(['statut', 'date_debut']);
            $table->index(['type_stage']);
            $table->index(['entreprise_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offre_stages');
    }
};
