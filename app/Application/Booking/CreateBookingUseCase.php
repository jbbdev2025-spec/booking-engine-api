<?php

namespace App\Application\Booking;

use App\Domain\Booking\BookingService;
use App\Application\Booking\CreateBookingRequest;

class CreateBookingUseCase
{
    public function __construct(
        private BookingService $bookingService
    ) {}

    public function execute(
        CreateBookingRequest $request
    ): array {
        return $this->bookingService->creerReservation(
            $request->vertical,
            $request->prenom,
            $request->telephone,
            $request->service,
            $request->date,
            $request->heure
        );
    }
}