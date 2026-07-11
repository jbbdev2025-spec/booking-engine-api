<?php

namespace App\Domain\Catalog;

use App\Models\Prestation;
use App\Models\Vertical;

class PriceResolver
{
    private CatalogRepository $catalogRepository;

    public function __construct(CatalogRepository $catalogRepository)
    {
        $this->catalogRepository = $catalogRepository;
    }

    /**
     * Résout le prix d'une prestation.
     */
    public function resolve(
        Vertical $vertical,
        string $service
    ): ?int {

        $prestation = $this->catalogRepository->findService($vertical->id, $service);

        if (!$prestation || empty($prestation->prix)) {
            return null;
        }

        // Exemple :
        // "15 000" → 15000
        // "À partir de 50 000" → 50000

        $prix = preg_replace('/\s/', '', $prestation->prix ?? '');

        if (preg_match('/(\d+)/', $prix, $match)) {
            return (int) $match[1];
        }

        return null;
    }
}
