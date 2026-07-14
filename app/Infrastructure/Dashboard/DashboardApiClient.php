<?php

namespace App\Infrastructure\Dashboard;

use Illuminate\Support\Facades\Http;

class DashboardApiClient
{
    public function createBooking(int $bookingId): void
    {
        Http::withHeaders([
            'X-API-Key' => config('services.dashboard.api_key'),
        ])->post(
            config('services.dashboard.url') . '/api/bookings',
            [
                'booking_id' => $bookingId,
            ]
        );
    }

    public function updateBooking(int $bookingId): void
    {
        // TODO B-027
    }

    public function cancelBooking(int $bookingId): void
    {
        // TODO B-028
    }
}
