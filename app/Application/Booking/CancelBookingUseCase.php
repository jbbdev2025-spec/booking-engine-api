<?php

namespace App\Application\Booking;

use App\Domain\Booking\BookingService;

class CancelBookingUseCase
{
    public function __construct(
        private BookingService $bookingService
    ) {}

    public function execute(CancelBookingRequest $request): array
    {
        return $this->bookingService->cancelReservation($request);
    }
}
