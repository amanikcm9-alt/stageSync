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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, rh, encadrant, stagiaire
            $table->text('description')->nullable();
            $table->json('permissions')->nullable(); // Liste des permissions
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index(['name', 'active']);
        });

        // Insérer les rôles par défaut
        DB::table('roles')->insert([
            ['name' => 'admin', 'description' => 'Administrateur système', 'permissions' => json_encode(['*']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'rh', 'description' => 'Ressources Humaines', 'permissions' => json_encode(['entreprises.manage', 'offres.manage', 'candidatures.manage']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'encadrant', 'description' => 'Encadrant de stage', 'permissions' => json_encode(['activities.manage', 'evaluations.manage', 'discussions.manage']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'stagiaire', 'description' => 'Stagiaire', 'permissions' => json_encode(['activities.view', 'discussions.participate']), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
