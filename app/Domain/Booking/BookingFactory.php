<?php

namespace App\Domain\Booking;

use App\Models\Vertical;

class BookingFactory
{
    public function make(
        Vertical $vertical,
        array $booking
    ): array {

        return [

            'vertical_id' => $vertical->id,

            'ville' => $vertical->ville,

            'prenom' => $booking['prenom'],

            'telephone' => $booking['telephone'],

            'categorie' => $vertical->categories[$booking['categorieId']] ?? null,

            'service' => $booking['service'],

            'date_rdv' => $booking['date'],

            'heure_rdv' => $booking['heure'] . ':00',

            'statut' => BookingRules::STATUS_CONFIRMED,

            'montant' => $booking['montant'],
        ];
    }
}
