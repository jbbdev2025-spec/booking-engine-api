<?php

namespace App\Infrastructure\Dashboard;

use Illuminate\Support\Facades\Http;

class DashboardApiClient
{
    private function client()
    {
        return Http::withHeaders([
            'X-API-Key' => config('services.dashboard.api_key'),
            'Accept' => 'application/json',
        ]);
    }

    public function createBooking(int $bookingId): void
    {
        $this->client()->post(
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
