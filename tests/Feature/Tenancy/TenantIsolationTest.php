<?php

namespace Tests\Feature\Tenancy;

use App\Models\Vertical;
use App\Models\Prestation;
use App\Models\RendezVous;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    public function test_vertical_a_cannot_see_vertical_b_bookings(): void
    {
        // 1. Création de deux verticales distinctes avec leurs propres clés API
        $verticalA = Vertical::create([
            'slug' => 'salon_a',
            'nom' => 'Salon A',
            'ville' => 'Douala',
            'ouverture' => '08:00:00',
            'fermeture' => '20:00:00',
            'capacites_par_categorie' => json_encode(['1' => 2]),
            'categories' => json_encode(['1' => 'Soins']),
            'booking_api_secret' => 'secret_a',
        ]);

        $verticalB = Vertical::create([
            'slug' => 'salon_b',
            'nom' => 'Salon B',
            'ville' => 'Yaoundé',
            'ouverture' => '09:00:00',
            'fermeture' => '18:00:00',
            'capacites_par_categorie' => json_encode(['1' => 1]),
            'categories' => json_encode(['1' => 'Soins']),
            'booking_api_secret' => 'secret_b',
        ]);

        // 2. Création d'un service "Massage" pour les deux (isolés par vertical_id)
        Prestation::create([
            'vertical_id' => $verticalA->id,
            'nom' => 'Massage',
            'categorie_id' => 1,
            'prix' => '5000',
            'duree_minutes' => 30,
        ]);

        Prestation::create([
            'vertical_id' => $verticalB->id,
            'nom' => 'Massage',
            'categorie_id' => 1,
            'prix' => '8000',
            'duree_minutes' => 45,
        ]);

        // 3. Création d'une réservation UNIQUEMENT pour le Salon A
        RendezVous::create([
            'vertical_id' => $verticalA->id,
            'ville' => 'Douala',
            'prenom' => 'Client Secret',
            'telephone' => '690000000',
            'categorie' => 'Soins',
            'service' => 'Massage',
            'date_rdv' => now()->addDay()->format('Y-m-d'),
            'heure_rdv' => '10:00',
            'statut' => 'confirmé',
        ]);

        // 4. Le Salon B demande sa liste de réservations
        $responseB = $this->withHeaders([
            'x-api-key' => 'secret_b',
        ])->getJson('/api/salon_b/reservations');

        // 5. ASSERTIONS D'ISOLATION
        $responseB->assertStatus(200);

        // Le Salon B doit avoir une liste VIDE
        $this->assertEmpty(
            $responseB->json('data'),
            'Fuite de données : Le Salon B a vu les réservations du Salon A !'
        );

        // 6. Vérification croisée : Le Salon A DOIT voir sa réservation
        $responseA = $this->withHeaders([
            'x-api-key' => 'secret_a',
        ])->getJson('/api/salon_a/reservations');

        $responseA->assertStatus(200);
        $this->assertCount(1, $responseA->json('data'));
        $this->assertEquals('Client Secret', $responseA->json('data.0.prenom'));
    }
}
