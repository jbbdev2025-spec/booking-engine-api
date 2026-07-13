namespace App\Domain\Booking\DTO;

use App\Models\Vertical;

final readonly class AvailabilityRequest
{
    public function __construct(
        public Vertical $vertical,
        public string $service,
        public string $date,
        public string $heure,
    ) {}
}