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
            $table->dropColumn('statut');
        });
        
        Schema::table('offre_stages', function (Blueprint $table) {
            $table->enum('statut', ['brouillon', 'publiee', 'cloturee', 'affectee'])->default('brouillon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offre_stages', function (Blueprint $table) {
            $table->dropColumn('statut');
        });
        
        Schema::table('offre_stages', function (Blueprint $table) {
            $table->enum('statut', ['brouillon', 'publiee', 'cloturee'])->default('brouillon');
        });
    }
};
