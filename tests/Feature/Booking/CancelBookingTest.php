<?php

namespace Tests\Feature\Booking;

use App\Models\RendezVous;
use Tests\TestCase;

class CancelBookingTest extends TestCase
{
    public function test_cancel_booking_removes_it_from_database(): void
    {
        $verticalId = \App\Models\Vertical::where('slug', 'beauty_salon')->value('id');

        // 1. On crée un RDV en base
        $rdv = RendezVous::create([
            'vertical_id' => $verticalId, // <-- CHANGÉ ICI
            'ville'       => 'Douala',
            'prenom'      => 'Client A Supprimer',
            'telephone'   => '690000000',
            'categorie'   => 'Test',
            'service'     => 'Manucure',
            'date_rdv'    => now()->addDay()->format('Y-m-d'),
            'heure_rdv'   => '15:00',
            'statut'      => 'confirmé',
        ]);

        // 2. On exécute la demande d'annulation (DELETE)
        $response = $this->withHeaders([
            'x-api-key' => env('BOOKING_API_SECRET'),
        ])->deleteJson("/api/beauty_salon/reservations/{$rdv->id}");

        // 3. L'API doit répondre avec un succès
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // 4. Le RDV ne doit PLUS exister dans la base de données
        $this->assertDatabaseMissing('rendez_vous', [
            'id' => $rdv->id,
        ]);
    }
}
