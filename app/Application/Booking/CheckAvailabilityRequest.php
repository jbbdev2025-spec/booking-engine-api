<?php

namespace App\Application\Booking;

use App\Models\Vertical;

final readonly class CheckAvailabilityRequest
{
    public function __construct(
        public Vertical $vertical,
        public string $service,
        public string $date,
        public string $heure,
        public string $ville,
    ) {}
}