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
        Schema::create('entretiens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->constrained()->onDelete('cascade');
            $table->date('date_entretien');
            $table->datetime('heure_entretien');
            $table->string('lieu_entretien');
            $table->text('notes_entretien')->nullable();
            $table->decimal('note_evaluation', 3, 2)->nullable();
            $table->text('commentaires_evaluation')->nullable();
            $table->string('statut')->default('planifie');
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('evaluated_at')->nullable();
            $table->timestamps();
            
            $table->index(['date_entretien', 'heure_entretien']);
            $table->index('statut');
            $table->index('evaluated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entretiens');
    }
};
