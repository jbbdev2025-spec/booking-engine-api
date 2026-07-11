<?php

namespace App\Domain\Catalog;

use App\Models\Prestation;
use Illuminate\Database\Eloquent\Collection;

class CatalogRepository
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
}
