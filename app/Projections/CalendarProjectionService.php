<?php

namespace App\Projections;

use App\Infrastructure\Dashboard\DashboardApiClient;

class CalendarProjectionService
{
    public function __construct(
        private DashboardApiClient $dashboard
    ) {}

    public function bookingCreated(int $bookingId): void
    {
        // Sprint suivant
    }

    public function bookingUpdated(int $bookingId): void {}

    public function bookingCancelled(int $bookingId): void {}
}
