<?php





// FILE: app/Repositories/Eloquent/BookingRepository.php
// ============================================================
namespace App\Repositories\Eloquent;

use App\Models\Booking;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Support\Collection;

class BookingRepository implements BookingRepositoryInterface
{
    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function findByUser(int $userId): Collection
    {
        return Booking::with(['slot.field', 'payment'])
                      ->where('user_id', $userId)
                      ->latest()
                      ->get();
    }

    public function findById($id): ?Booking
    {
        return Booking::with(['slot.field', 'payment', 'user'])->find($id);
    }

    public function updateStatus(int $bookingId, string $status): void
    {
        Booking::where('id', $bookingId)->update(['status' => $status]);
    }
}

// ============================================================