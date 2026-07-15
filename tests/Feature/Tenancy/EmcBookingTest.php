<?php

namespace Tests\Feature\Tenancy;

use App\Models\RendezVous;
use Tests\TestCase;

class EmcBookingTest extends TestCase
{
    public function test_can_book_nammi_01_test_drive(): void
    {
        $date = now()->addDay()->format('Y-m-d');

        $response = $this->withHeaders([
            'x-api-key' => 'emc_super_secret_key_2026',
        ])->postJson('/api/electric_motors/reservation', [
            'prenom'    => 'Thierry Francklin',
            'telephone' => '699000000',
            'service'   => 'Essai Nammi 01',
            'date'      => $date,
            'heure'     => '10:30',
            'ville'     => 'Douala',
        ]);

        // Vérifie que l'API EMC accepte la réservation
        $response->assertStatus(200)
            ->assertJson([
                'success'      => true,
                'confirmation' => true,
            ]);

        // Vérifie que Gladstone ne voit PAS la réservation d'EMC
        $this->assertDatabaseMissing('rendez_vous', [
            'prenom'      => 'Thierry Francklin',
            'vertical_id' => 1, // ID de Gladstone
        ]);

        // Vérifie que EMC a bien la réservation
        $this->assertDatabaseHas('rendez_vous', [
            'prenom'  => 'Thierry Francklin',
            'service' => 'Essai Nammi 01',
        ]);
    }
}
