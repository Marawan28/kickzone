<?php




// FILE: app/Repositories/Contracts/UserRepositoryInterface.php
// ============================================================
declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByPhone(string $phone): ?User;
    public function findById(int $id): ?User;
    public function create(array $data): User;
    public function update(User $user, array $data): User;
    public function creditBalance(int $userId, float $amount): void;
    public function debitBalance(int $userId, float $amount): void;
    public function updateDsrScore(int $userId, float $score): void;
}

// ============================================================