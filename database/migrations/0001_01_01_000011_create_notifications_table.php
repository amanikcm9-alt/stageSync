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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('sujet');
            $table->text('contenu');
            $table->enum('type', ['email', 'sms', 'system'])->default('system');
            $table->string('destinataire'); // Email ou numéro de téléphone
            $table->enum('statut', ['envoye', 'en_attente', 'erreur'])->default('en_attente');
            $table->timestamp('date_envoi')->nullable();
            
            // Polymorphic relation pour les notifiables
            $table->morphs('notifiable');
            
            $table->timestamps();
            
            $table->index(['type', 'statut']);
            $table->index(['date_envoi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
