<?php

namespace Tests\Feature\Booking;

use App\Models\RendezVous;
use Tests\TestCase;

class UpdateBookingTest extends TestCase
{
    public function test_update_booking_status_and_amount(): void
    {
        // 1. On crée un RDV directement en base pour avoir un ID réel
        $rdv = RendezVous::create([
            'vertical_id' => 1,
            'ville'       => 'Douala',
            'prenom'      => 'Client A Modifier',
            'telephone'   => '690000000',
            'categorie'   => 'Test',
            'service'     => 'Manucure',
            'date_rdv'    => now()->addDay()->format('Y-m-d'),
            'heure_rdv'   => '14:00',
            'statut'      => 'confirmé',
            'montant'     => null,
        ]);

        // 2. On modifie le statut ET le montant
        $response = $this->withHeaders([
            'x-api-key' => env('BOOKING_API_SECRET'),
        ])->patchJson("/api/beauty_salon/reservations/{$rdv->id}", [
            'statut'  => 'honoré',
            'montant' => 5000,
        ]);

        // 3. Vérification
        $response->assertStatus(200);

        // On vérifie que la base de données a bien été mise à jour
        $this->assertDatabaseHas('rendez_vous', [
            'id'      => $rdv->id,
            'statut'  => 'honoré',
            'montant' => 5000,
        ]);
    }
}