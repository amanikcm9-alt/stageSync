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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // create, update, delete, login, logout, etc.
            $table->text('description')->nullable();
            $table->enum('niveau', ['info', 'warning', 'error'])->default('info');
            
            // Relations
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('model_type')->nullable(); // App\Models\Activity, App\Models\User, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            
            // Métadonnées
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
