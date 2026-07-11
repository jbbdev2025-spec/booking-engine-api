<?php

namespace App\Domain\Booking;

class BookingValidator
{
    public function validate(array $availability): ?array
    {
        if (isset($availability['erreur'])) {
            return [
                'success' => false,
                'message' => $availability['erreur'],
            ];
        }

        if (!$availability['disponible']) {
            return [
                'success' => false,
                'message' => 'Ce créneau est déjà pris',
                'disponible' => false,
                'creneaux_alternatifs' => $availability['creneaux_alternatifs'],
            ];
        }

        return null;
    }
}
