<?php

namespace App\Domain\Scheduling;

use App\Domain\Booking\BookingRules;
use App\Domain\Scheduling\SchedulingRules;
use App\Domain\Shared\Time\TimeHelper;
use App\Models\Prestation;
use App\Models\RendezVous;
use App\Models\Vertical;

class ConflictDetector
{
    public function count(
        Vertical $vertical,
        string $date,
        int $categorieId,
        int $debutMin,
        int $dureeMin
    ): int {

        $rdvs = RendezVous::where('vertical_id', $vertical->id)
            ->where('date_rdv', $date)
            ->where('statut', 'not like', '%' . BookingRules::STATUS_CANCELLED . '%')
            ->get();

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

            $debutRdv = TimeHelper::toMinutes(
                $rdv->heure_rdv->format('H:i')
            );

            if (
                TimeHelper::overlap(
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
