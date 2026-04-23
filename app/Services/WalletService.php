<?php


// FILE: app/Services/WalletService.php
// Implements a Ledger pattern: every balance change has a
// corresponding Transaction record.
// ============================================================
namespace App\Services;

use App\Enums\{TransactionStatus, TransactionType};
use App\Repositories\Contracts\{
    TransactionRepositoryInterface,
    UserRepositoryInterface
};

class WalletService
{
    public function __construct(
        private readonly UserRepositoryInterface        $userRepo,
        private readonly TransactionRepositoryInterface $transactionRepo,
    ) {}

    public function topUp(int $userId, float $amount, string $reference): void
    {
        $this->userRepo->creditBalance($userId, $amount);
        $this->transactionRepo->create([
            'user_id' => $userId,
            'amount'  => $amount,
            'type'    => TransactionType::Deposit,
            'status'  => TransactionStatus::Accepted,
        ]);
    }

    public function deductForBooking(int $userId, float $amount, string $label): void
    {
        $this->userRepo->debitBalance($userId, $amount);
        $this->transactionRepo->create([
            'user_id' => $userId,
            'amount'  => $amount,
            'type'    => TransactionType::Payment,
            'status'  => TransactionStatus::Accepted,
        ]);
    }

    public function refund(int $userId, float $amount, string $reason): void
    {
        $this->userRepo->creditBalance($userId, $amount);
        $this->transactionRepo->create([
            'user_id' => $userId,
            'amount'  => $amount,
            'type'    => TransactionType::Deposit,
            'status'  => TransactionStatus::Accepted,
        ]);
    }

    public function withdraw(int $userId, float $amount): void
    {
        $this->userRepo->debitBalance($userId, $amount);
        $this->transactionRepo->create([
            'user_id' => $userId,
            'amount'  => $amount,
            'type'    => TransactionType::Withdraw,
            'status'  => TransactionStatus::Pending,
        ]);
    }

    public function getBalance(int $userId): float
    {
        return (float) $this->userRepo->findById($userId)->balance;
    }

    public function getTransactionHistory(int $userId): \Illuminate\Support\Collection
    {
        return $this->transactionRepo->findByUser($userId);
    }
}

// ============================================================