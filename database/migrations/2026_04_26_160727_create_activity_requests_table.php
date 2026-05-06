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
        Schema::create('activity_requests', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->text('objectifs')->nullable();
            $table->string('statut')->default('en_attente');
            $table->foreignId('stagiaire_id')->constrained('users');
            $table->foreignId('encadrant_id')->nullable()->constrained('users');
            $table->date('date_proposition');
            $table->date('date_limite')->nullable();
            $table->text('commentaires_encadrant')->nullable();
            $table->date('date_validation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_requests');
    }
};
