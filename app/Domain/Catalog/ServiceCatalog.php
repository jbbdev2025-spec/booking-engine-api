<?php

namespace App\Domain\Catalog;

use App\Models\Vertical;
use App\Models\Prestation;

class ServiceCatalog
{
    public function __construct(
        private CatalogRepository $catalogRepository
    ) {}

    public function find(
        Vertical $vertical,
        string $service
    ): ?Prestation {

        return $this->catalogRepository->findService(
            $vertical->id,
            $service
        );
    }
}
