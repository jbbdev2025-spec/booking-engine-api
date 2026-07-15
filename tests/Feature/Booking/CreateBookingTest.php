<?php

namespace Tests\Feature\Booking;

use Tests\TestCase;

class CreateBookingTest extends TestCase
{
    public function test_booking_creation_and_saturation_cycle(): void
    {
        $date = now()->addDay()->format('Y-m-d');
        
        $payload = [
            'prenom'    => 'Client Test',
            'telephone' => '690000000',
            'service'   => 'Manucure',
            'date'      => $date,
            'heure'     => '12:00',
            'ville'     => 'Douala',
        ];

        // 1ère réservation : Succès (1/2)
        $response1 = $this->withHeaders([
            'x-api-key' => env('BOOKING_API_SECRET'),
        ])->postJson('/api/beauty_salon/reservation', $payload);

        $response1
            ->assertStatus(200)
            ->assertJson([
                'success'      => true,
                'confirmation' => true,
            ])
            ->assertJsonPath('evenement_id', fn ($id) => !empty($id));

        // 2ème réservation : Succès (2/2) - Capacité atteinte
        $payload['prenom'] = 'Client Test 2';
        $payload['telephone'] = '690000001';
        
        $response2 = $this->withHeaders([
            'x-api-key' => env('BOOKING_API_SECRET'),
        ])->postJson('/api/beauty_salon/reservation', $payload);

        $response2
            ->assertStatus(200)
            ->assertJson([
                'success'      => true,
                'confirmation' => true,
            ]);

        // 3ème réservation : Échec (Saturé) -> 409 + Alternatives
        $payload['prenom'] = 'Client Test 3';
        $payload['telephone'] = '690000002';

        $response3 = $this->withHeaders([
            'x-api-key' => env('BOOKING_API_SECRET'),
        ])->postJson('/api/beauty_salon/reservation', $payload);

        // Le UseCase doit détecter la saturation et le Controller renvoie 409
        $response3
            ->assertStatus(409)
            ->assertJson([
                'success'    => false,
                'disponible' => false,
            ])
            ->assertJsonPath('creneaux_alternatifs', function ($alternatives) {
                return count($alternatives) > 0;
            });
    }
}