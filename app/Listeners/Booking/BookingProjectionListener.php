<?php

namespace App\Listeners\Booking;

use App\Domain\Booking\Events\BookingCreated;
use App\Projections\CalendarProjectionService;

class BookingProjectionListener
{
    private CalendarProjectionService $calendarProjection;

    public function __construct(
        CalendarProjectionService $calendarProjection
    ){}

    public function handle(BookingCreated $event): void
    {
        $this->calendarProjection->bookingCreated($event->bookingId);
    }
}
