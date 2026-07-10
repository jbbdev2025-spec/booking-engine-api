<?php

namespace App\Domain\Shared\Time;

class TimeHelper
{
    /**
     * Convertit HH:mm en nombre de minutes depuis minuit.
     */
    public static function toMinutes(string $heure): int
    {
        [$h, $m] = explode(':', $heure);

        return ((int) $h * 60) + (int) $m;
    }

    /**
     * Convertit un nombre de minutes en HH:mm.
     */
    public static function toTime(int $minutes): string
    {
        $h = str_pad((string) floor($minutes / 60), 2, '0', STR_PAD_LEFT);
        $m = str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT);

        return "{$h}:{$m}";
    }

    /**
     * Détermine si deux plages horaires se chevauchent.
     */
    public static function overlap(
        int $debutA,
        int $dureeA,
        int $debutB,
        int $dureeB
    ): bool {
        return $debutA < ($debutB + $dureeB)
            && $debutB < ($debutA + $dureeA);
    }
}
