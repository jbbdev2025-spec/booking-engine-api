namespace App\Domain\Booking\DTO;

final readonly class AvailabilityResult
{
    public function __construct(
        public bool $disponible,
        public array $creneauxAlternatifs,
        public int $dureeMinutes,
        public int $categorieId,
        public ?string $erreur = null,
    ) {}
}