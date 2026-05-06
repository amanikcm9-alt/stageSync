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
        Schema::table('offre_stages', function (Blueprint $table) {
            // Ajouter les clés étrangères pour secteur et type_stage
            $table->foreignId('secteur_id')->nullable()->after('missions')->constrained()->onDelete('set null');
            $table->foreignId('type_stage_id')->nullable()->after('secteur_id')->constrained()->onDelete('set null');
            
            // Garder les anciens champs pour la migration des données
            $table->string('secteur_old')->nullable()->after('type_stage_id');
            $table->string('type_stage_old')->nullable()->after('secteur_old');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offre_stages', function (Blueprint $table) {
            $table->dropForeign(['secteur_id']);
            $table->dropForeign(['type_stage_id']);
            $table->dropColumn(['secteur_id', 'type_stage_id', 'secteur_old', 'type_stage_old']);
        });
    }
};
