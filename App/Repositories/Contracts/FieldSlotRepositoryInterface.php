<?php




// FILE: app/Repositories/Contracts/FieldSlotRepositoryInterface.php
// ============================================================
namespace App\Repositories\Contracts;

use App\Models\FieldSlot;

interface FieldSlotRepositoryInterface
{
    public function findAvailable(int $slotId): ?FieldSlot;
    public function lockAndBook(int $slotId): bool;
    public function release(int $slotId): void;
}

// ============================================================