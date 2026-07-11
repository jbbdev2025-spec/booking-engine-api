<?php

namespace App\Domain\Booking;

use App\Models\Vertical;
use App\Domain\Catalog\PriceResolver;
use App\Domain\Booking\BookingFactory;
use App\Domain\Booking\BookingRepository;
use App\Domain\Scheduling\AvailabilityChecker;

class BookingService
{
    public function __construct(
        private AvailabilityChecker $availabilityChecker,
        private PriceResolver $priceResolver,
        private BookingRepository $bookingRepository,
        private BookingFactory $bookingFactory
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

        // Résolution du prix
        $montant = $this->priceResolver->resolve(
            $vertical,
            $service
        );

        // Résoudre le nom de catégorie
        $data = $this->bookingFactory->make($vertical, [
            'prenom'      => $prenom,
            'telephone'   => $telephone,
            'service'     => $service,
            'date'        => $date,
            'heure'       => $heure,
            'categorieId' => $verif['categorieId'],
            'montant'     => $montant,
        ]);

        // Création de la réservation
        $rdv = $this->bookingRepository->create($data);

        // Retourne la confirmation
        return [
            'success' => true,
            'confirmation' => true,
            'evenement_id' => $rdv->id,
            'lien' => null,
        ];
    }

    // ─── Méthodes privées ─────────────────────────────────────────


}
