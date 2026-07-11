<?php

namespace App\Domain\Scheduling;

use App\Models\Vertical;
use App\Domain\Booking\BookingRepository;
use App\Domain\Scheduling\SchedulingRules;
use App\Domain\Shared\Time\TimeCalculator;
use App\Domain\Catalog\CatalogRepository;

class ConflictDetector
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private CatalogRepository $catalogRepository
    ) {}

    public function count(
        Vertical $vertical,
        string $date,
        int $categorieId,
        int $debutMin,
        int $dureeMin
    ): int {

        $rdvs = $this->bookingRepository->getReservationsForDay(
            $vertical->id,
            $date
        );

        $prestations = $this->catalogRepository->getServicesIndexedByName($vertical->id);

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
            $dureeRdv = $info->duree_minutes ?: SchedulingRules::DEFAULT_DURATION_MINUTES;

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
