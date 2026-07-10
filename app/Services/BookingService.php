<?php

namespace App\Services;

use App\Models\Prestation;
use App\Models\RendezVous;
use App\Models\Vertical;
use App\Domain\Booking\BookingRules;
use App\Domain\Scheduling\SchedulingRules;
use App\Domain\Shared\Time\TimeCalculator;
use App\Domain\Scheduling\ConflictDetector;

class BookingService
{
    public function __construct(
        private ConflictDetector $conflictDetector
    ) {}

    /**
     * Vérifie la disponibilité d'un créneau
     * Réplique exactement la logique de lib/booking.ts du dashboard Next.js
     */
    public function verifierDisponibilite(
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

        // 2. Convertir l'heure en minutes
        $debutMin = TimeCalculator::toMinutes($heure);

        // 3. Vérifier les horaires d'ouverture
        $ouvertureMin = TimeCalculator::toMinutes($vertical->ouverture->format('H:i'));
        $fermetureMin = TimeCalculator::toMinutes($vertical->fermeture->format('H:i'));

        if ($debutMin < $ouvertureMin || $debutMin + $dureeMin > $fermetureMin) {
            return [
                'disponible' => false,
                'creneaux_alternatifs' => $this->trouverAlternatives(
                    $vertical,
                    $date,
                    $categorieId,
                    $dureeMin,
                    $debutMin,
                    $capacite,
                    $ouvertureMin
                ),
                'dureeMinutes' => $dureeMin,
                'categorieId' => $categorieId,
            ];
        }

        // 4. Compter les conflits
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

        // 5. Pas disponible → chercher des alternatives
        return [
            'disponible' => false,
            'creneaux_alternatifs' => $this->trouverAlternatives(
                $vertical,
                $date,
                $categorieId,
                $dureeMin,
                $debutMin,
                $capacite,
                $debutMin
            ),
            'dureeMinutes' => $dureeMin,
            'categorieId' => $categorieId,
        ];
    }

    /**
     * Crée une réservation
     */
    public function creerReservation(
        Vertical $vertical,
        string $prenom,
        string $telephone,
        string $service,
        string $date,
        string $heure
    ): array {
        // Re-vérification avant écriture
        $verif = $this->verifierDisponibilite($vertical, $service, $date, $heure);

        if (isset($verif['erreur'])) {
            return [
                'success' => false,
                'message' => $verif['erreur'],
            ];
        }

        if (!$verif['disponible']) {
            return [
                'success' => false,
                'message' => 'Ce créneau est déjà pris',
                'disponible' => false,
                'creneaux_alternatifs' => $verif['creneaux_alternatifs'],
            ];
        }

        // Résoudre le nom de catégorie
        $categories = $vertical->categories;
        $categorieNom = $categories[$verif['categorieId']] ?? null;

        // Résoudre le prix depuis le catalogue
        $montant = $this->resoudrePrix($vertical, $service);

        $rdv = RendezVous::create([
            'vertical_id' => $vertical->id,
            'ville' => $vertical->ville,
            'prenom' => $prenom,
            'telephone' => $telephone,
            'categorie' => $categorieNom,
            'service' => $service,
            'date_rdv' => $date,
            'heure_rdv' => $heure . ':00',
            'statut' => BookingRules::STATUS_CONFIRMED,
            'montant' => $montant,
        ]);

        return [
            'success' => true,
            'confirmation' => true,
            'evenement_id' => $rdv->id,
            'lien' => null,
        ];
    }

    // ─── Méthodes privées ─────────────────────────────────────────

    private function trouverAlternatives(
        Vertical $vertical,
        string $date,
        int $categorieId,
        int $dureeMin,
        int $debutMin,
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

    private function resoudrePrix(Vertical $vertical, string $service): ?int
    {
        $prestation = Prestation::where('vertical_id', $vertical->id)
            ->where('nom', $service)
            ->first();

        if ($prestation) {
            // Extrait le premier nombre de la chaîne prix
            // "15 000" → 15000, "à partir de 50 000" → 50000
            $prix = preg_replace('/\s/', '', $prestation->prix);
            if (preg_match('/(\d+)/', $prix, $match)) {
                return (int) $match[1];
            }
        }

        return null;
    }
}
