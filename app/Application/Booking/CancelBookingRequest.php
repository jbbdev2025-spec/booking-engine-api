<?php

namespace App\Application\Booking;

use App\Models\Vertical;

final readonly class CancelBookingRequest
{
    public function __construct(
        public Vertical $vertical,
        public int $bookingId,
    ) {}
}
