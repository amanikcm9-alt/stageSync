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
        Schema::create('entreprises', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('secteur_activite')->nullable();
            $table->string('adresse');
            $table->string('ville');
            $table->string('code_postal');
            $table->string('pays')->default('France');
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->string('site_web')->nullable();
            $table->string('logo_path')->nullable();
            $table->text('conditions_stage')->nullable(); // Conditions internes de stage
            $table->text('reglement_interne')->nullable(); // Règlement interne
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index(['active', 'nom']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprises');
    }
};
