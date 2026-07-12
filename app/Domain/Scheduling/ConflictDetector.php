<?php

namespace App\Domain\Scheduling;

use App\Models\Vertical;
use App\Contracts\Repositories\BookingRepositoryInterface;
use App\Contracts\Repositories\CatalogRepositoryInterface;
use App\Domain\Shared\Time\TimeCalculator;

class ConflictDetector
{
    public function __construct(
        private BookingRepositoryInterface $bookingRepository,
        private CatalogRepositoryInterface $catalogRepository
    ) {}

    public function count(
        Vertical $vertical,
        string $date,
        int $categorieId,
        int $debutMin,
        int $dureeMin
    ): int {

        $rdvs = $this->bookingRepository->findForDate(
            $vertical->id,
            $date
        );

        $prestations = $this->catalogRepository->getServicesIndexedByName(
            $vertical->id
        );

        $conflits = 0;

        foreach ($rdvs as $rdv) {

            $info = $prestations->get($rdv->service);

            if (!$info) {
                continue;
            }

            if ($info->categorie_id !== $categorieId) {
                continue;
            }

            // Fallback to default duration if prestation duration is not set
            $dureeRdv = $info->duree_minutes
                ?: SchedulingRules::DEFAULT_DURATION_MINUTES;

            $debutRdv = TimeCalculator::toMinutes(
                $rdv->heure_rdv->format('H:i')
            );

            if (
                TimeCalculator::overlap(
                    $debutMin,
                    $dureeMin,
                    $debutRdv,
                    $dureeRdv
                )
            ) {
                $conflits++;
            }
        }

        return $conflits;
    }
}
