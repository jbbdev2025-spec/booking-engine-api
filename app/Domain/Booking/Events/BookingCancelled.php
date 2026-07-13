<?php

namespace App\Domain\Booking\Events;

final readonly class BookingCancelled
{
    public function __construct(
        public int $bookingId,
    ) {}
}