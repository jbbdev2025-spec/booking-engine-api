<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vertical_id')->constrained()->cascadeOnDelete();
            $table->string('prenom');
            $table->string('telephone');
            $table->string('categorie');
            $table->string('service');
            $table->date('date_rdv');
            $table->time('heure_rdv');
            $table->string('statut')->default('confirmé');
            $table->integer('montant')->nullable();
            $table->timestamps();

            // Index pour les requêtes fréquentes du booking engine
            $table->index(['vertical_id', 'date_rdv', 'statut']);
            $table->index(['vertical_id', 'service', 'date_rdv']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};