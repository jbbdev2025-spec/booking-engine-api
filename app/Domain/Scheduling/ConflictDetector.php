<?php

namespace App\Domain\Scheduling;

use App\Domain\Booking\BookingRules;
use App\Domain\Scheduling\SchedulingRules;
use App\Domain\Shared\Time\TimeCalculator;
use App\Models\Prestation;
use App\Models\RendezVous;
use App\Models\Vertical;

class ConflictDetector
{
    public function __construct(
        private BookingRepository $bookingRepository
    ) {}

    public function count(
        Vertical $vertical,
        string $date,
        int $categorieId,
        int $debutMin,
        int $dureeMin
    ): int {

        $rdvs = $this->bookingRepository
            ->getReservationsForDay(
                $vertical->id,
                $date
            );

        $prestations = Prestation::where('vertical_id', $vertical->id)
            ->get()
            ->keyBy('nom');

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
