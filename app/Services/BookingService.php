<?php

namespace App\Services;

use App\Models\Prestation;
use App\Models\RendezVous;
use App\Models\Vertical;
use App\Domain\Booking\BookingRules;
use App\Domain\Scheduling\AvailabilityChecker;

class BookingService
{
    public function __construct(
        private AvailabilityChecker $availabilityChecker
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
        return $this->availabilityChecker->check(
            $vertical,
            $service,
            $date,
            $heure
        );
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
