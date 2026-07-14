<?php

namespace App\Domain\Scheduling;

use App\Models\Vertical;

class SchedulingService
{
    public function __construct(
        private AvailabilityChecker $availabilityChecker
    ) {}

    public function verifierDisponibilite(
        Vertical $vertical,
        string $service,
        string $date,
        string $heure
    ): array {

        return $this->availabilityChecker->check(
            $vertical,
            $service,
            $date,
            $heure
        );
    }
}
