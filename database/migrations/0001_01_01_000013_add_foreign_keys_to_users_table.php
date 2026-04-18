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
        Schema::table('users', function (Blueprint $table) {
            // Ajouter les foreign keys après la création de toutes les tables
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('encadrant_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('encadrant_faculte_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('encadrant_entreprise_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('offre_stage_id')->references('id')->on('offre_stages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['encadrant_id']);
            $table->dropForeign(['encadrant_faculte_id']);
            $table->dropForeign(['encadrant_entreprise_id']);
            $table->dropForeign(['offre_stage_id']);
        });
    }
};
