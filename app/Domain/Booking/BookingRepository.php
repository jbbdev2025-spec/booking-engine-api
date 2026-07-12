<?php

namespace App\Domain\Booking;

use App\Models\RendezVous;
use Illuminate\Support\Collection;
use App\Contracts\Repositories\BookingRepositoryInterface;

class BookingRepository implements BookingRepositoryInterface
{
    public function create(array $data): RendezVous
    {
        return RendezVous::create($data);
    }

    /**
     * Récupère les réservations actives d'une verticale
     * pour une date donnée.
     */
    public function findForDate(
        int $verticalId,
        string $date
    ): Collection {
        return RendezVous::where('vertical_id', $verticalId)
            ->where('date_rdv', $date)
            ->where('statut', 'not like', '%annul%')
            ->get();
    }

    /**
     * Recherche une réservation précise.
     */
    public function findById(
        int $verticalId,
        int $id
    ): ?RendezVous {
        return RendezVous::where('vertical_id', $verticalId)
            ->find($id);
    }
}
