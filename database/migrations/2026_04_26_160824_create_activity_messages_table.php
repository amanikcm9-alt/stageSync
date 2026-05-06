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
        Schema::create('activity_messages', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->string('type')->default('question');
            $table->foreignId('activity_id')->constrained();
            $table->foreignId('expediteur_id')->constrained('users');
            $table->foreignId('destinataire_id')->constrained('users');
            $table->boolean('lu')->default(false);
            $table->timestamp('date_lecture')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_messages');
    }
};
