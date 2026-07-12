<?php

namespace App\Contracts\Repositories;

use App\Models\RendezVous;
use Illuminate\Support\Collection;

interface BookingRepositoryInterface
{
    public function create(array $data): RendezVous;

    public function findForDate(
        int $verticalId,
        string $date
    ): Collection;

    public function findById(
        int $verticalId,
        int $id
    ): ?RendezVous;
}
