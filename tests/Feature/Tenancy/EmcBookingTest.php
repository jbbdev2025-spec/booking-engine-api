<?php

namespace Tests\Feature\Tenancy;

use App\Models\Vertical;
use App\Models\Prestation;
use App\Models\RendezVous;
use Tests\TestCase;

class EmcBookingTest extends TestCase
{
    public function test_can_book_nammi_01_test_drive(): void
    {
        // 1. Création du contexte EMC directement dans le test
        $emc = Vertical::create([
            'slug' => 'electric_motors',
            'nom' => 'Electric Motors Cameroon',
            'ville' => 'Douala',
            'ouverture' => '10:00:00',
            'fermeture' => '16:00:00',
            'capacites_par_categorie' => ['1' => 3, '2' => 2],
            'categories' => ['1' => 'Vehicules & Essais', '2' => 'SAV & Maintenance'],
            'booking_api_secret' => 'emc_super_secret_key_2026',
        ]);

        Prestation::create([
            'vertical_id' => $emc->id,
            'nom' => 'Essai Nammi 01',
            'categorie_id' => 1,
            'prix' => 'Gratuit',
            'duree_minutes' => 30,
        ]);

        $date = now()->addDay()->format('Y-m-d');

        // 2. Réservation
        $response = $this->withHeaders([
            'x-api-key' => 'emc_super_secret_key_2026',
        ])->postJson('/api/electric_motors/reservation', [
            'prenom'    => 'Jean Prospect',
            'telephone' => '699000000',
            'service'   => 'Essai Nammi 01',
            'date'      => $date,
            'heure'     => '10:30',
            'ville'     => 'Douala',
        ]);

        // 3. Vérifications
        $response->assertStatus(200)
            ->assertJson([
                'success'      => true,
                'confirmation' => true,
            ]);

        $this->assertDatabaseMissing('rendez_vous', [
            'prenom'      => 'Jean Prospect',
            'vertical_id' => 1, // ID de Gladstone
        ]);

        $this->assertDatabaseHas('rendez_vous', [
            'prenom'  => 'Jean Prospect',
            'service' => 'Essai Nammi 01',
        ]);
    }
}
