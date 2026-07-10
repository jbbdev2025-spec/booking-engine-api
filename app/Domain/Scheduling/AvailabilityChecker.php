<?php

namespace App\Domain\Scheduling;

use App\Domain\Shared\Time\TimeCalculator;
use App\Models\Prestation;
use App\Models\Vertical;

class AvailabilityChecker
{
    public function __construct(
        private ConflictDetector $conflictDetector
    ) {}

    public function check(
        Vertical $vertical,
        string $service,
        string $date,
        string $heure
    ): array {

        // 1. Trouver la prestation
        $prestation = Prestation::where('vertical_id', $vertical->id)
            ->where('nom', $service)
            ->first();

        if (!$prestation) {
            return [
                'disponible' => false,
                'creneaux_alternatifs' => [],
                'dureeMinutes' => 0,
                'categorieId' => -1,
                'erreur' => "Service inconnu : \"{$service}\"",
            ];
        }

        $categorieId = $prestation->categorie_id;
        $dureeMin = $prestation->duree_minutes;
        $capacite = $vertical->capacites_par_categorie[$categorieId] ?? 1;

        // Heure demandée
        $debutMin = TimeCalculator::toMinutes($heure);

        // Horaires d'ouverture
        $ouvertureMin = TimeCalculator::toMinutes(
            $vertical->ouverture->format('H:i')
        );

        $fermetureMin = TimeCalculator::toMinutes(
            $vertical->fermeture->format('H:i')
        );

        // Vérification des horaires
        if (
            $debutMin < $ouvertureMin ||
            $debutMin + $dureeMin > $fermetureMin
        ) {
            return [
                'disponible' => false,
                'creneaux_alternatifs' => $this->trouverAlternatives(
                    $vertical,
                    $date,
                    $categorieId,
                    $dureeMin,
                    $capacite,
                    $ouvertureMin
                ),
                'dureeMinutes' => $dureeMin,
                'categorieId' => $categorieId,
            ];
        }

        // Vérification des conflits
        $conflits = $this->conflictDetector->count(
            $vertical,
            $date,
            $categorieId,
            $debutMin,
            $dureeMin
        );

        if ($conflits < $capacite) {
            return [
                'disponible' => true,
                'creneaux_alternatifs' => [],
                'dureeMinutes' => $dureeMin,
                'categorieId' => $categorieId,
            ];
        }

        // Créneaux alternatifs
        return [
            'disponible' => false,
            'creneaux_alternatifs' => $this->trouverAlternatives(
                $vertical,
                $date,
                $categorieId,
                $dureeMin,
                $capacite,
                $debutMin
            ),
            'dureeMinutes' => $dureeMin,
            'categorieId' => $categorieId,
        ];
    }

    private function trouverAlternatives(
        Vertical $vertical,
        string $date,
        int $categorieId,
        int $dureeMin,
        int $capacite,
        int $heureDemandee
    ): array {

        $ouvertureMin = TimeCalculator::toMinutes($vertical->ouverture->format('H:i'));
        $fermetureMin = TimeCalculator::toMinutes($vertical->fermeture->format('H:i'));

        $apres = [];
        $avant = [];

        for (
            $heure = $ouvertureMin;
            $heure + $dureeMin <= $fermetureMin;
            $heure += SchedulingRules::SLOT_STEP_MINUTES
        ) {

            $conflits = $this->conflictDetector->count(
                $vertical,
                $date,
                $categorieId,
                $heure,
                $dureeMin
            );

            if ($conflits >= $capacite) {
                continue;
            }

            if ($heure >= $heureDemandee) {

                $apres[] = [
                    'heure' => $heure,
                    'distance' => $heure - $heureDemandee,
                ];
            } else {

                $avant[] = [
                    'heure' => $heure,
                    'distance' => $heureDemandee - $heure,
                ];
            }
        }

        // Les créneaux APRÈS sont prioritaires
        usort($apres, fn($a, $b) => $a['distance'] <=> $b['distance']);

        // Puis les créneaux AVANT
        usort($avant, fn($a, $b) => $a['distance'] <=> $b['distance']);

        $resultats = [];

        foreach ($apres as $slot) {
            $resultats[] = $slot;

            if (count($resultats) === SchedulingRules::MAX_ALTERNATIVES) {
                break;
            }
        }

        if (count($resultats) < SchedulingRules::MAX_ALTERNATIVES) {

            foreach ($avant as $slot) {

                $resultats[] = $slot;

                if (count($resultats) === SchedulingRules::MAX_ALTERNATIVES) {
                    break;
                }
            }
        }

        return array_map(
            fn($slot) => TimeCalculator::toTime($slot['heure']),
            $resultats
        );
    }
}
