<?php

namespace Tests\Feature\Availability;

use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    /**
     * Vérifie qu'un créneau libre est annoncé disponible.
     */
    public function test_available_slot_returns_success(): void
    {
        $response = $this->withHeaders([
            'x-api-key' => env('BOOKING_API_SECRET'),
        ])->postJson('/api/beauty_salon/disponibilite', [
            'service' => 'Manucure',
            'date'    => now()->addDay()->format('Y-m-d'),
            'heure'   => '10:00',
            'ville'   => 'Douala',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'disponible',
                'creneaux_alternatifs',
                'timestamp',
            ]);
    }

    /**
     * Vérifie qu'un créneau passé est rejeté.
     */ 
    public function test_past_date_is_rejected(): void
    {
        $response = $this->withHeaders([
            'x-api-key' => env('BOOKING_API_SECRET'),
        ])->postJson('/api/beauty_salon/disponibilite', [
            'service' => 'Manucure',
            'date'    => now()->subDay()->format('Y-m-d'),
            'heure'   => '10:00',
            'ville'   => 'Douala',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonPath(
                'error',
                'Impossible de réserver un créneau passé.'
            );
    }

    /**
     * Vérifie qu'un service inconnu est rejeté.
     */
    public function test_unknown_service_is_rejected(): void
    {
        $response = $this->withHeaders([
            'x-api-key' => env('BOOKING_API_SECRET'),
        ])->postJson('/api/beauty_salon/disponibilite', [
            'service' => 'Service imaginaire',
            'date'    => now()->addDay()->format('Y-m-d'),
            'heure'   => '10:00',
            'ville'   => 'Douala',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonPath(
                'error',
                'Service inconnu : "Service imaginaire"'
            );
    }

    /**
     * Vérifie qu'un créneau avant l'ouverture est rejeté.
     */
    public function test_before_opening_shows_alternatives(): void
    {
        $response = $this->withHeaders([
            'x-api-key' => env('BOOKING_API_SECRET'),
        ])->postJson('/api/beauty_salon/disponibilite', [
            'service' => 'Manucure',
            'date'    => now()->addDay()->format('Y-m-d'),
            'heure'   => '06:00', // Avant 09:30
            'ville'   => 'Douala',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success'    => true,   // La requête est valide
                'disponible' => false,  // Mais le créneau ne l'est pas
            ])
            // On s'assure que le bot a bien calculé des alternatives (ex: 08:00, 08:15...)
            ->assertJsonPath('creneaux_alternatifs', function ($alternatives) {
                return count($alternatives) > 0;
            });
    }

    /**
     * Vérifie qu'un créneau après la fermeture est rejeté.
     */
    public function test_after_closing_shows_alternatives(): void
    {
        $response = $this->withHeaders([
            'x-api-key' => env('BOOKING_API_SECRET'),
        ])->postJson('/api/beauty_salon/disponibilite', [
            'service' => 'Manucure',
            'date'    => now()->addDay()->format('Y-m-d'),
            'heure'   => '21:00', // Après 20:00 (fermeture par défaut)
            'ville'   => 'Douala',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success'    => true,
                'disponible' => false,
            ])
            ->assertJsonPath('creneaux_alternatifs', function ($alternatives) {
                return count($alternatives) > 0;
            });
    }

    /**
     * Vérifie qu'un créneau saturé (capacité atteinte) est rejeté et propose des alternatives.
     */    
    public function test_saturated_slot_shows_alternatives(): void
    {
        $date = now()->addDay()->format('Y-m-d');
        $heure = '10:00';
        $verticalId = 1; // beauty_salon

        // La capacité par catégorie étant de 2, on crée 2 RDV actifs pour saturer le créneau.
        \App\Models\RendezVous::create([
            'vertical_id' => $verticalId,
            'ville'       => 'Douala',
            'prenom'      => 'Client A',
            'telephone'   => '690000001',
            'categorie'   => 'Test', // Le ConflictDetector ignore ce champ, il utilise le JOIN prestations
            'service'     => 'Manucure',
            'date_rdv'    => $date,
            'heure_rdv'   => $heure,
            'statut'      => 'confirmé',
        ]);

        \App\Models\RendezVous::create([
            'vertical_id' => $verticalId,
            'ville'       => 'Douala',
            'prenom'      => 'Client B',
            'telephone'   => '690000002',
            'categorie'   => 'Test',
            'service'     => 'Manucure',
            'date_rdv'    => $date,
            'heure_rdv'   => $heure,
            'statut'      => 'confirmé',
        ]);

        // On demande ce même créneau
        $response = $this->withHeaders([
            'x-api-key' => env('BOOKING_API_SECRET'),
        ])->postJson('/api/beauty_salon/disponibilite', [
            'service' => 'Manucure',
            'date'    => $date,
            'heure'   => $heure,
            'ville'   => 'Douala',
        ]);

        // Le moteur doit détecter la saturation (2/2) et proposer des alternatives
        $response
            ->assertStatus(200)
            ->assertJson([
                'success'    => true,
                'disponible' => false,
            ])
            ->assertJsonPath('creneaux_alternatifs', function ($alternatives) {
                return count($alternatives) > 0;
            });
    }    


    /**
     * Vérifie qu'une mauvaise clé API est rejetée.
     */
    public function test_invalid_api_key_is_rejected(): void
    {
        $response = $this->withHeaders([
            'x-api-key' => 'mauvaise-cle-secrete',
        ])->postJson('/api/beauty_salon/disponibilite', [
            'service' => 'Manucure',
            'date'    => now()->addDay()->format('Y-m-d'),
            'heure'   => '10:00',
            'ville'   => 'Douala',
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    /**
     * Vérifie que les champs manquants sont rejetés.
     */
    public function test_missing_fields_are_rejected(): void
    {
        $response = $this->withHeaders([
            'x-api-key' => env('BOOKING_API_SECRET'),
        ])->postJson('/api/beauty_salon/disponibilite', [
            'service' => 'Manucure',
            // Il manque 'date' et 'heure'
            'ville'   => 'Douala',
        ]);

        // Laravel doit renvoyer une erreur de validation (422 Unprocessable Entity)
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['date', 'heure']);
    }
}