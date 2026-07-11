<?php

namespace App\Domain\Booking;

use App\Models\RendezVous;

class BookingRepository
{
    public function create(array $data): RendezVous
    {
        return RendezVous::create($data);
    }

    public function getReservationsForDay(
        int $verticalId,
        string $date
    ) {
        return RendezVous::where('vertical_id', $verticalId)
            ->where('date_rdv', $date)
            ->where('statut', 'not like', '%annul%')
            ->get();
    }

    public function findById(
        int $verticalId,
        int $id
    ): ?RendezVous {
        return RendezVous::where('vertical_id', $verticalId)
            ->find($id);
    }
}
