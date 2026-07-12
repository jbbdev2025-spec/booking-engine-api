<?php

namespace App\Domain\Catalog;

use App\Models\Vertical;
use App\Contracts\Repositories\CatalogRepositoryInterface;

class CatalogService
{
    public function __construct(
        private CatalogRepositoryInterface $catalogRepository,
        private PriceResolver $priceResolver,
    ) {}

    public function findService(
        Vertical $vertical,
        string $service
    ): ?object
    {
        return $this->catalogRepository->findService(
            $vertical->id,
            $service
        );
    }

    public function getPrice(
        Vertical $vertical,
        string $service
    ): int
    {
        return $this->priceResolver->resolve(
            $vertical->id,
            $service
        );
    }

    public function getDuration(
        Vertical $vertical,
        string $service
    ): int
    {
        $prestation = $this->findService(
            $vertical,
            $service
        );

        return $prestation->duree_min;
    }
}