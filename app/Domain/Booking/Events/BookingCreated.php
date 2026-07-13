<?php

namespace App\Domain\Booking\Events;

final readonly class BookingCreated
{
    public function __construct(
        public int $bookingId,
    ) {}
}