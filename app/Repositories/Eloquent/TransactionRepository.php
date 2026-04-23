<?php





// FILE: app/Repositories/Eloquent/TransactionRepository.php
// ============================================================
namespace App\Repositories\Eloquent;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Collection;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function findByUser(int $userId): Collection
    {
        return Transaction::where('user_id', $userId)
                          ->latest()
                          ->get();
    }
}

// ============================================================