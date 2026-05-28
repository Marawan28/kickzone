<?php





// FILE: app/Repositories/Eloquent/FieldSlotRepository.php
// ============================================================
namespace App\Repositories\Eloquent;

use App\Models\FieldSlot;
use App\Repositories\Contracts\FieldSlotRepositoryInterface;

class FieldSlotRepository implements FieldSlotRepositoryInterface
{
    public function findAvailable(int $slotId): ?FieldSlot
    {
        return FieldSlot::where('id', $slotId)
                        ->where('is_booked', false)
                        ->first();
    }

    /**
     * Pessimistic lock — prevents double-booking under concurrency.
     */
    public function lockAndBook(int $slotId): bool
    {
        $updated = FieldSlot::where('id', $slotId)
                            ->where('is_booked', false)
                            ->update(['is_booked' => true]);
        return $updated > 0;
    }

    public function release(int $slotId): void
    {
        FieldSlot::where('id', $slotId)->update(['is_booked' => false]);
    }
}

// ============================================================