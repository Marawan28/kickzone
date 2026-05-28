<?php




// FILE: app/Repositories/Contracts/TransactionRepositoryInterface.php
// ============================================================
namespace App\Repositories\Contracts;

use App\Models\transaction;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface
{
    public function create(array $data): Transaction;
    public function findByUser(int $userId): Collection;
}

// ============================================================