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
        $this->dashboard->createBooking($bookingId);
    }

    public function bookingUpdated(int $bookingId): void
    {
        $this->dashboard->updateBooking($bookingId);
    }

    public function bookingCancelled(int $bookingId): void
    {
        $this->dashboard->cancelBooking($bookingId);
    }
}
