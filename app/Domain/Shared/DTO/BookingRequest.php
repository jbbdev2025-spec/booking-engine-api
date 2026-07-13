namespace App\Domain\Booking\DTO;

use App\Models\Vertical;

final readonly class BookingRequest
{
    public function __construct(
        public Vertical $vertical,
        public string $prenom,
        public string $telephone,
        public string $service,
        public string $date,
        public string $heure,
    ) {}
}