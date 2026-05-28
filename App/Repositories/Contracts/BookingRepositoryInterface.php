<?php





// FILE: app/Repositories/Contracts/BookingRepositoryInterface.php
// ============================================================
namespace App\Repositories\Contracts;

use App\Models\Booking;
use Illuminate\Support\Collection;

interface BookingRepositoryInterface
{
    public function create(array $data): Booking;
    public function findByUser(int $userId): Collection;
    public function findById(int $id): ?Booking;
    public function updateStatus(int $bookingId, string $status): void;
}

// ============================================================