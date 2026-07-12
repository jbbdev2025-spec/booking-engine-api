<?php

namespace App\Domain\Catalog;

use App\Models\Vertical;

class CatalogService
{
    public function __construct(
        private CatalogService $catalogService,
        private PriceResolver $priceResolver,
    ) {}

    public function findService(Vertical $vertical, string $nomService): ?object
    {
        return $this->catalogService->findService($vertical, $nomService);
    }

    public function getPrice(
        Vertical $vertical,
        string $service
    ): int
        {
            return $this->catalogService->getPrice($vertical, $service);
        }

    public function getDuration(
        Vertical $vertical,
        string $service
    ): int
        {
            return $this->catalogService->getDuration($vertical, $service);
        }

    public function resolvePrice(
        Vertical $vertical,
        string $service
    ): int
    {
        return $this->priceResolver->resolve($vertical, $service);
    }
}