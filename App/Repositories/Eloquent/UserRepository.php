<?php


// FILE: app/Repositories/Eloquent/UserRepository.php
// ============================================================
namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    public function findByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)->first();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    /**
     * Credit wallet balance — atomic DB operation.
     */
    public function creditBalance(int $userId, float $amount): void
    {
        User::where('id', $userId)->increment('balance', $amount);
    }

    /**
     * Debit wallet balance — atomic DB operation.
     */
    public function debitBalance(int $userId, float $amount): void
    {
        DB::transaction(function () use ($userId, $amount): void {
            $user = User::lockForUpdate()->find($userId);
            if ($user->balance < $amount) {
                throw new \DomainException('Insufficient balance.');
            }
            $user->decrement('balance', $amount);
        });
    }

    public function updateDsrScore(int $userId, float $score): void
    {
        User::where('id', $userId)->update(['dsr_score' => $score]);
    }
}

// ============================================================