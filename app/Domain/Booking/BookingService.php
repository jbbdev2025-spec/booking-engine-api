<?php

namespace App\Domain\Booking;

use App\Models\Vertical;
use App\Domain\Catalog\CatalogService;
use App\Domain\Booking\BookingFactory;
use App\Domain\Booking\BookingValidator;
use App\Domain\Scheduling\SchedulingService;
use App\Contracts\Repositories\BookingRepositoryInterface;

class BookingService
{
    public function __construct(
        private SchedulingService $schedulingService,
        private CatalogService $catalogService,
        private BookingRepositoryInterface $bookingRepository,
        private BookingFactory $bookingFactory,
        private BookingValidator $bookingValidator
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
        return $this->schedulingService->verifierDisponibilite(
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
        $verif = $this->verifierDisponibilite(
            $vertical,
            $service,
            $date,
            $heure
        );

        $validation = $this->bookingValidator->validate($verif);

        if ($validation !== null) {
            return $validation;
        }

        // Résolution du prix
        $montant = $this->catalogService->getPrice(
            $vertical,
            $service
        );

        // Construction de la réservation
        $data = $this->bookingFactory->make($vertical, [
            'prenom'      => $prenom,
            'telephone'   => $telephone,
            'service'     => $service,
            'date'        => $date,
            'heure'       => $heure,
            'categorieId' => $verif['categorieId'],
            'montant'     => $montant,
        ]);

        // Création de la réservation via le contrat Repository
        $rdv = $this->bookingRepository->create($data);

        return [
            'success' => true,
            'confirmation' => true,
            'evenement_id' => $rdv->id,
            'lien' => null,
        ];
    }
}
