<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vertical_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->unsignedInteger('categorie_id');
            $table->string('prix');                    // "15 000", "à partir de 50 000"
            $table->unsignedInteger('duree_minutes')->default(30);
            $table->timestamps();

            $table->unique(['vertical_id', 'nom']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestations');
    }
};