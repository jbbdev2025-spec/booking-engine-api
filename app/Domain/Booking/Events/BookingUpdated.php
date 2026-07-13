<?php

namespace App\Domain\Booking\Events;

final readonly class BookingUpdated
{
    public function __construct(
        public int $bookingId,
    ) {}
}