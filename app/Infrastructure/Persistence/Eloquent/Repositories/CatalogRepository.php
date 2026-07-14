<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Models\Prestation;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Repositories\CatalogRepositoryInterface;

class CatalogRepository implements CatalogRepositoryInterface
{
    public function findService(
        int $verticalId,
        string $service
    ): ?Prestation {
        return Prestation::where('vertical_id', $verticalId)
            ->where('nom', $service)
            ->first();
    }

    public function getServices(
        int $verticalId
    ): Collection {
        return Prestation::where('vertical_id', $verticalId)
            ->get();
    }

    public function getServicesIndexedByName(
        int $verticalId
    ): Collection {
        return Prestation::where('vertical_id', $verticalId)
            ->get()
            ->keyBy('nom');
    }
}
