<?php

namespace App\Application\Booking;

use App\Models\Vertical;

final readonly class UpdateBookingRequest
{
    public function __construct(
        public Vertical $vertical,
        public int $bookingId,
        public ?string $statut = null,
        public ?float $montant = null,
    ) {}
}
