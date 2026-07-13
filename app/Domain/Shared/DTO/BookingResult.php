namespace App\Domain\Booking\DTO;

final readonly class BookingResult
{
    public function __construct(
        public bool $success,
        public bool $confirmation,
        public ?int $evenementId,
        public ?string $lien = null,
        public ?string $message = null,
        public array $creneauxAlternatifs = [],
    ) {}
}