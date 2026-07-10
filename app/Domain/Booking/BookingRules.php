<?php

namespace App\Domain\Booking;

final class BookingRules
{
    /**
     * Pas de recherche des créneaux.
     */
    public const SLOT_STEP_MINUTES = 15;

    /**
     * Nombre maximum de propositions.
     */
    public const MAX_ALTERNATIVES = 3;

    /**
     * Durée utilisée lorsqu'une prestation n'en définit pas.
     */
    public const DEFAULT_DURATION_MINUTES = 30;

    /**
     * Statut d'une réservation validée.
     */
    public const STATUS_CONFIRMED = 'confirmé';

    /**
     * Mot-clé utilisé pour exclure les rendez-vous annulés.
     */
    public const CANCELLED_KEYWORD = 'annul';
}
