<?php

namespace App\Application\Booking\Queries;

final readonly class GetBookingsQuery
{
    public function __construct(
        public string $vertical,
        public ?string $ville = null,
    ) {}
}
