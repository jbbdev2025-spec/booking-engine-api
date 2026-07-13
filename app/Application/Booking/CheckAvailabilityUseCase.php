<?php

namespace App\Application\Booking;

use App\Domain\Booking\BookingService;
use App\Models\Vertical;

class CheckAvailabilityUseCase
{
    public function __construct(
        private BookingService $bookingService
    ) {}

    public function execute(
        Vertical $vertical,
        string $service,
        string $date,
        string $heure
    ): array {
        return $this->bookingService->verifierDisponibilite(
            $vertical,
            $service,
            $date,
            $heure
        );
    }
}