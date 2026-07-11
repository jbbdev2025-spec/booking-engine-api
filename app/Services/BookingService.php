<?php

namespace App\Services;

use App\Models\RendezVous;
use App\Models\Vertical;
use App\Domain\Booking\BookingRules;
use App\Domain\Catalog\PriceResolver;
use App\Domain\Scheduling\AvailabilityChecker;

class BookingService
{
    public function __construct(
        private AvailabilityChecker $availabilityChecker,
        private PriceResolver $priceResolver
    ) {}

    /**
     * Vérifie la disponibilité d'un créneau
     * Réplique exactement la logique de lib/booking.ts du dashboard Next.js
     */
    public function verifierDisponibilite(
        Vertical $vertical,
        string $service,
        string $date,
        string $heure
    ): array {
        return $this->availabilityChecker->check(
            $vertical,
            $service,
            $date,
            $heure
        );
    }

    /**
     * Crée une réservation
     */
    public function creerReservation(
        Vertical $vertical,
        string $prenom,
        string $telephone,
        string $service,
        string $date,
        string $heure
    ): array {
        // Re-vérification avant écriture
        $verif = $this->verifierDisponibilite($vertical, $service, $date, $heure);

        if (isset($verif['erreur'])) {
            return [
                'success' => false,
                'message' => $verif['erreur'],
            ];
        }

        if (!$verif['disponible']) {
            return [
                'success' => false,
                'message' => 'Ce créneau est déjà pris',
                'disponible' => false,
                'creneaux_alternatifs' => $verif['creneaux_alternatifs'],
            ];
        }

        // Résoudre le nom de catégorie
        $categories = $vertical->categories;
        $categorieNom = $categories[$verif['categorieId']] ?? null;

        // Résoudre le prix depuis le catalogue
        $montant = $this->priceResolver->resolve(
            $vertical,
            $service
        );

        $rdv = RendezVous::create([
            'vertical_id' => $vertical->id,
            'ville' => $vertical->ville,
            'prenom' => $prenom,
            'telephone' => $telephone,
            'categorie' => $categorieNom,
            'service' => $service,
            'date_rdv' => $date,
            'heure_rdv' => $heure . ':00',
            'statut' => BookingRules::STATUS_CONFIRMED,
            'montant' => $montant,
        ]);

        return [
            'success' => true,
            'confirmation' => true,
            'evenement_id' => $rdv->id,
            'lien' => null,
        ];
    }

    // ─── Méthodes privées ─────────────────────────────────────────


}
