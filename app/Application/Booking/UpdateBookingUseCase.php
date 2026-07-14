<?php

namespace App\Application\Booking;

use App\Domain\Booking\BookingService;

class UpdateBookingUseCase
{
    public function __construct(
        private BookingService $bookingService
    ) {}

    public function execute(UpdateBookingRequest $request): array
    {
        return $this->bookingService->updateReservation($request);
    }
}
