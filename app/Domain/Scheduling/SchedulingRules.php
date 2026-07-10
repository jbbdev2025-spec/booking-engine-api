<?php

namespace App\Domain\Scheduling;

final class SchedulingRules
{
    /**
     * Pas de génération des créneaux.
     */
    public const SLOT_STEP_MINUTES = 15;

    /**
     * Nombre maximum de créneaux alternatifs proposés.
     */
    public const MAX_ALTERNATIVES = 3;

    /**
     * Durée par défaut lorsqu'une prestation ne précise pas sa durée.
     */
    public const DEFAULT_DURATION_MINUTES = 30;
}
