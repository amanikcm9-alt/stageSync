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
        // Mettre à jour les candidatures existantes qui n'ont pas de statut défini
        // ou qui ont un statut vide à 'en_cours' par défaut
        \DB::table('candidatures')
            ->where('statut', '')
            ->orWhereNull('statut')
            ->update(['statut' => 'en_cours']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            //
        });
    }
};
