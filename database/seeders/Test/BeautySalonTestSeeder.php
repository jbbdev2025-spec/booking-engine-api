<?php

namespace Database\Seeders\Test;

use Illuminate\Database\Seeder;
use App\Models\Vertical;
use App\Models\Prestation;

class BeautySalonTestSeeder extends Seeder
{
    public function run(): void
    {
        $vertical = Vertical::create([
            'slug' => 'beauty_salon',
            'nom' => 'Institut Gladstone',
            'ville' => 'Douala',
            'ouverture' => '08:00:00',
            'fermeture' => '20:00:00',
            'capacites_par_categorie' => ['1' => 2],
            'categories' => ['1' => 'Soins'],
            'booking_api_secret' => env('BOOKING_API_SECRET', 'test-secret-key'),
        ]);

        Prestation::create([
            'vertical_id' => $vertical->id,
            'nom' => 'Manucure',
            'categorie_id' => 1,
            'prix' => '5000',
            'duree_minutes' => 30,
        ]);
    }
}
