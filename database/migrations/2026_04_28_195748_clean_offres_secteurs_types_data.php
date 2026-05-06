<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Supprimer toutes les candidatures existantes
        DB::table('candidatures')->delete();
        
        // Supprimer toutes les offres de stage existantes
        DB::table('offre_stages')->delete();
        
        // Supprimer tous les secteurs existants
        DB::table('secteurs')->delete();
        
        // Supprimer tous les types de stage existants
        DB::table('type_stages')->delete();
        
        // Réinitialiser les auto-incréments
        DB::statement('ALTER TABLE candidatures AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE offre_stages AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE secteurs AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE type_stages AUTO_INCREMENT = 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration ne peut pas être inversée car elle supprime des données
        // Pour inverser, il faudrait restaurer les données depuis une sauvegarde
    }
};
