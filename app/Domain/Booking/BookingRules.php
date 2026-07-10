<?php

namespace App\Domain\Booking;

final class BookingRules
{
    /**
     * Statut d'une réservation validée.
     */
    public const STATUS_CONFIRMED = 'confirmé';

    /**
     * Mot-clé utilisé pour exclure les rendez-vous annulés.
     */
    public const STATUS_CANCELLED = 'annulé';

    /**
     * Statut d'une réservation en attente de validation.
     */
    public const STATUS_PENDING = 'en attente';
}
