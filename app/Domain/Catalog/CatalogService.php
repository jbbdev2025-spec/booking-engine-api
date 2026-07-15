<?php

namespace App\Domain\Catalog;

use App\Models\Vertical;
use App\Contracts\Repositories\CatalogRepositoryInterface;
use App\Models\Prestation;
use RuntimeException;

class CatalogService
{
    public function __construct(
        private CatalogRepositoryInterface $catalogRepository,
    ) {}

    public function findService(
        Vertical $vertical,
        string $service
    ): ?Prestation {
        return $this->catalogRepository->findService(
            $vertical->id,
            $service
        );
    }

    public function getPrice(
        Vertical $vertical,
        string $service
    ): ?int {
        $prestation = $this->findService(
            $vertical,
            $service
        );

        if (!$prestation || empty($prestation->prix)) {
            return null;
        }

        $prix = preg_replace('/\s/', '', $prestation->prix);

        if (preg_match('/(\d+)/', $prix, $match)) {
            return (int) $match[1];
        }

        return null;
    }

    public function getDuration(
        Vertical $vertical,
        string $service
    ): int {
        $prestation = $this->findService($vertical, $service);

        if (!$prestation) {
            throw new RuntimeException(
                "Service introuvable : {$service}"
            );
        }

        if ($prestation->duree_minutes === null) {
            throw new RuntimeException(
                "La durée est absente pour le service : {$service}"
            );
        }

        return (int) $prestation->duree_minutes;
    }
}
