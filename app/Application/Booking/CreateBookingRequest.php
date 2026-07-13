<?php

namespace App\Application\Booking;

use App\Models\Vertical;

final readonly class CreateBookingRequest
{
    public function __construct(
        public Vertical $vertical,
        public string $prenom,
        public string $telephone,
        public string $service,
        public string $date,
        public string $heure,
        public string $ville,
    ) {}
}