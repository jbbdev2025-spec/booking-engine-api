<?php

namespace App\Application\Booking;

use App\Domain\Booking\BookingService;
use App\Domain\Shared\DTO\CheckAvailabilityRequest;
use App\Models\Vertical;

class CheckAvailabilityUseCase
{
    public function __construct(
        private BookingService $bookingService
    ) {}

    /**
     * @param mixed $request Expected to have properties: vertical, service, date, heure
     */
    public function execute(
        $request
    ): array {
        return $this->bookingService->verifierDisponibilite(
            $request->vertical,
            $request->service,
            $request->date,
            $request->heure
        );
    }
}