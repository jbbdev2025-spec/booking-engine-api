<?php

namespace App\Domain\Catalog;

use App\Models\Prestation;
use App\Models\Vertical;

class PriceResolver
{
    /**
     * Résout le prix d'une prestation.
     */
    public function resolve(
        Vertical $vertical,
        string $service
    ): ?int {

        $prestation = Prestation::where('vertical_id', $vertical->id)
            ->where('nom', $service)
            ->first();

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
