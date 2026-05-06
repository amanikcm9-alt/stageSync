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
        Schema::table('entretiens', function (Blueprint $table) {
            // Modifier le champ note_evaluation pour accepter des notes jusqu'à 20
            $table->decimal('note_evaluation', 5, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entretiens', function (Blueprint $table) {
            // Revenir à l'ancienne définition
            $table->decimal('note_evaluation', 3, 2)->change();
        });
    }
};
