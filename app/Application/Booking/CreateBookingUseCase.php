<?php

namespace App\Application\Booking;

use App\Domain\Booking\BookingService;
use App\Models\Vertical;

class CreateBookingUseCase
{
    public function __construct(
        private BookingService $bookingService
    ) {}

    public function execute(
        Vertical $vertical,
        string $prenom,
        string $telephone,
        string $service,
        string $date,
        string $heure
    ): array {
        return $this->bookingService->creerReservation(
            $vertical,
            $prenom,
            $telephone,
            $service,
            $date,
            $heure
        );
    }
}