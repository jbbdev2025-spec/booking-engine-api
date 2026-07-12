<?php

namespace App\Contracts\Repositories;

use App\Models\Prestation;
use Illuminate\Support\Collection;

interface CatalogRepositoryInterface
{
    public function findService(
        int $verticalId,
        string $service
    ): ?Prestation;

    public function getServices(
        int $verticalId
    ): Collection;

    public function getServicesIndexedByName(
        int $verticalId
    ): Collection;
}
