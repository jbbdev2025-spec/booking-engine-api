<?php

namespace App\Services;

use App\Models\Prestation;
use App\Models\RendezVous;
use App\Models\Vertical;

class BookingService
{
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
        $debutMin = $this->heureVersMinutes($heure);

        // 3. Vérifier les horaires d'ouverture
        $ouvertureMin = $this->heureVersMinutes($vertical->ouverture->format('H:i'));
        $fermetureMin = $this->heureVersMinutes($vertical->fermeture->format('H:i'));

        if ($debutMin < $ouvertureMin || $debutMin + $dureeMin > $fermetureMin) {
            return [
                'disponible' => false,
                'creneaux_alternatifs' => $this->trouverAlternatives(
                    $vertical, $date, $categorieId, $dureeMin, $capacite, $ouvertureMin
                ),
                'dureeMinutes' => $dureeMin,
                'categorieId' => $categorieId,
            ];
        }

        // 4. Compter les conflits
        $conflits = $this->compterConflits(
            $vertical, $date, $categorieId, $debutMin, $dureeMin
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
                $vertical, $date, $categorieId, $dureeMin, $capacite, $debutMin
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
            'statut' => 'confirmé',
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

    private function heureVersMinutes(string $heure): int
    {
        $parts = explode(':', $heure);
        return (int) $parts[0] * 60 + (int) ($parts[1] ?? 0);
    }

    private function minutesVersHeure(int $minutes): string
    {
        $h = str_pad((string) floor($minutes / 60), 2, '0', STR_PAD_LEFT);
        $m = str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT);
        return "{$h}:{$m}";
    }

    private function chevauchent(int $debutA, int $dureeA, int $debutB, int $dureeB): bool
    {
        return $debutA < $debutB + $dureeB && $debutB < $debutA + $dureeA;
    }

    private function compterConflits(
        Vertical $vertical,
        string $date,
        int $categorieId,
        int $debutMin,
        int $dureeMin
    ): int {
        // Récupérer tous les RDV du jour pour cette verticale
        $rdvs = RendezVous::where('vertical_id', $vertical->id)
            ->where('date_rdv', $date)
            ->where('statut', 'not like', '%annul%')
            ->get();

        // Index des prestations pour obtenir catégorie et durée
        $prestations = Prestation::where('vertical_id', $vertical->id)->get()->keyBy('nom');

        $conflits = 0;
        foreach ($rdvs as $rdv) {
            $info = $prestations->get($rdv->service);
            if (!$info || $info->categorie_id !== $categorieId) {
                continue; // Catégorie différente = pas de conflit de ressource
            }

            $dureeRdv = $info->duree_minutes ?: 30;
            $debutRdv = $this->heureVersMinutes($rdv->heure_rdv->format('H:i'));

            if ($this->chevauchent($debutMin, $dureeMin, $debutRdv, $dureeRdv)) {
                $conflits++;
            }
        }

        return $conflits;
    }

    private function trouverAlternatives(
        Vertical $vertical,
        string $date,
        int $categorieId,
        int $dureeMin,
        int $capacite,
        int $partirDe
    ): array {
        $pas = 15;
        $alternatives = [];

        $ouvertureMin = $this->heureVersMinutes($vertical->ouverture->format('H:i'));
        $fermetureMin = $this->heureVersMinutes($vertical->fermeture->format('H:i'));

        $candidat = (int) (ceil(max($partirDe, $ouvertureMin) / $pas) * $pas);

        while ($candidat + $dureeMin <= $fermetureMin && count($alternatives) < 3) {
            $conflits = $this->compterConflits($vertical, $date, $categorieId, $candidat, $dureeMin);
            if ($conflits < $capacite) {
                $alternatives[] = $this->minutesVersHeure($candidat);
            }
            $candidat += $pas;
        }

        return $alternatives;
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