<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verticals', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();        // 'beauty_salon', 'ev_dealer'
            $table->string('nom');                    // 'Institut Gladstone', 'EVD Motors'
            $table->string('ville')->default('Douala');
            $table->time('ouverture')->default('09:30:00');
            $table->time('fermeture')->default('20:00:00');
            $table->json('capacites_par_categorie');  // {"1": 2, "2": 2, ...}
            $table->json('categories');               // {"1": "Coiffure", "2": "Spa", ...}
            $table->string('devise')->default('FCFA');
            $table->string('booking_api_secret');     // clé partagée avec le bot
            $table->string('escalation_phone')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verticals');
    }
};