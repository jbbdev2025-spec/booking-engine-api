<?php

namespace App\Http\Controllers\Api;

use App\Models\Vertical;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Application\Booking\CreateBookingUseCase;
use App\Application\Booking\CreateBookingRequest;
use App\Application\Booking\UpdateBookingRequest;
use App\Application\Booking\UpdateBookingUseCase;
use App\Application\Booking\CancelBookingUseCase;
use App\Application\Booking\CancelBookingRequest;
use App\Application\Booking\CheckAvailabilityUseCase;
use App\Application\Booking\CheckAvailabilityRequest;

class BookingController extends Controller
{
    public function __construct(
        private CheckAvailabilityUseCase $checkAvailability,
        private CreateBookingUseCase $createBooking,
        private UpdateBookingUseCase $updateBooking,
        private CancelBookingUseCase $cancelBooking,
    ) {}

    /**
     * POST /api/{vertical}/disponibilite
     *
     * Body : { "service": "Hammam Simple", "date": "2026-07-16", "heure": "15:30", "ville": "Douala" }
     * Réponse conforme au contrat du bot Node.js :
     *   { success, disponible, creneaux_alternatifs, timestamp }
     */
    public function verifierDisponibilite(Request $request): JsonResponse
    {
        $request->validate([
            'service' => 'required|string',
            'date'    => 'required|date',
            'heure'   => 'required|date_format:H:i',
            'ville'   => 'required|string|max:100',
        ]);

        $vertical = $request->attributes->get('vertical');

        $availabilityRequest = new CheckAvailabilityRequest(
            vertical: $vertical,
            service: $request->service,
            date: $request->date,
            heure: $request->heure,
            ville: $request->ville,
        );

        $resultat = $this->checkAvailability->execute($availabilityRequest);

        if (isset($resultat['erreur'])) {
            return response()->json([
                'success' => false,
                'error' => $resultat['erreur'],
            ], 422);
        }

        return response()->json([
            'success'              => true,
            'disponible'           => $resultat['disponible'],
            'creneaux_alternatifs' => $resultat['creneaux_alternatifs'],
            'timestamp'            => now()->toIso8601String(),
        ]);
    }

    /**
     * POST /api/{vertical}/reservation
     *
     * Body : { "prenom": "Thierry", "telephone": "699999999",
     *          "service": "Hammam simple", "date": "2026-06-21", "heure": "15:30", "ville": "Douala" }
     * Réponse :
     *   { success, confirmation, evenement_id, lien, timestamp }
     */
    public function creerReservation(Request $request): JsonResponse
    {
        $request->validate([
            'prenom'    => 'required|string|max:100',
            'telephone' => 'required|string|max:20',
            'service'   => 'required|string',
            'date'      => 'required|date',
            'heure'     => 'required|date_format:H:i',
            'ville'     => 'required|string|max:100',
        ]);

        $vertical = $request->attributes->get('vertical');

        $requestDto = new CreateBookingRequest(
            vertical: $vertical,
            prenom: $request->prenom,
            telephone: $request->telephone,
            service: $request->service,
            date: $request->date,
            heure: $request->heure,
            ville: $request->ville,
        );

        $resultat = $this->createBooking->execute($requestDto);

        if (!$resultat['success']) {
            return response()->json([
                'success'              => false,
                'message'              => $resultat['message'],
                'disponible'           => $resultat['disponible'] ?? false,
                'creneaux_alternatifs' => $resultat['creneaux_alternatifs'] ?? [],
            ], 409);
        }

        return response()->json([
            'success'       => true,
            'confirmation'  => $resultat['confirmation'],
            'evenement_id'  => $resultat['evenement_id'],
            'lien'          => $resultat['lien'],
            'timestamp'     => now()->toIso8601String(),
        ]);
    }

    /**
     * DELETE /api/{vertical}/reservation/{id}
     *
     * Réponse :
     *   { success, message, timestamp }
     */

    public function destroy(string $vertical, int $id): JsonResponse
    {
        $vertical = request()->attributes->get('vertical');

        $requestDto = new CancelBookingRequest(
            vertical: $vertical,
            bookingId: $id,
        );

        $result = $this->cancelBooking->execute($requestDto);

        if (!$result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }

    public function index(Request $request, string $vertical): JsonResponse
    {
        $verticalRecord = Vertical::where('slug', $vertical)->first();
        if (!$verticalRecord) {
            return response()->json(['success' => false, 'error' => 'Verticale non trouvée'], 404);
        }

        $query = \App\Models\RendezVous::where('vertical_id', $verticalRecord->id);

        // Filtre par ville si demandé
        if ($request->has('ville')) {
            $query->where('ville', $request->ville);
        }

        $reservations = $query
            ->orderBy('date_rdv', 'desc')
            ->orderBy('heure_rdv', 'desc')
            ->get()
            ->map(function ($r) {
                return [
                    'id'         => $r->id,
                    'prenom'     => $r->prenom,
                    'telephone'  => $r->telephone,
                    'service'    => $r->service,
                    'date_rdv'   => $r->date_rdv?->toDateString(),
                    'heure_rdv'  => $r->heure_rdv?->format('H:i'),
                    'statut'     => $r->statut ?? 'confirmé',
                    'categorie'  => $r->categorie,
                    'ville'      => $r->ville ?? '',
                    'montant'    => $r->montant,
                    'created_at' => $r->created_at?->toISOString(),
                ];
            });

        return response()->json(['success' => true, 'data' => $reservations]);
    }



    public function update(Request $request, string $vertical, int $id): JsonResponse
    {
        $validated = $request->validate([
            'statut'  => 'sometimes|string',
            'montant' => 'sometimes|nullable|numeric',
        ]);

        $vertical = $request->attributes->get('vertical');

        $requestDto = new UpdateBookingRequest(
            vertical: $vertical,
            bookingId: $id,
            statut: $validated['statut'] ?? null,
            montant: $validated['montant'] ?? null,
        );

        $result = $this->updateBooking->execute($requestDto);

        if (!$result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }
}
