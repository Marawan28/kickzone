<?php

// FILE: app/Services/SplitBillService.php
// ============================================================
namespace App\Services;

use App\DTOs\Financial\SplitBillDTO;
use Illuminate\Support\Facades\DB;

class SplitBillService
{
    public function __construct(
        private readonly WalletService $walletService,
    ) {}

    /**
     * Initiator sends a split-bill request. Each selected recipient
     * receives a push notification. When they accept, their wallet
     * is debited and the initiator is credited.
     */
    public function initiateSplit(SplitBillDTO $dto): void
    {
        $perPerson = $dto->getAmountPerPerson();

        DB::transaction(function () use ($dto, $perPerson): void {
            foreach ($dto->recipientIds as $recipientId) {
                // Create pending transaction for each recipient
                \App\Models\Transaction::create([
                    'user_id' => $recipientId,
                    'amount'  => $perPerson,
                    'type'    => \App\Enums\TransactionType::Payment,
                    'status'  => \App\Enums\TransactionStatus::Pending,
                ]);

                // TODO: Fire SplitBillRequestNotification to recipient
            }
        });
    }

    public function acceptSplit(int $transactionId, int $userId): void
    {
        DB::transaction(function () use ($transactionId, $userId): void {
            $tx = \App\Models\Transaction::lockForUpdate()->findOrFail($transactionId);

            if ($tx->user_id !== $userId) {
                throw new \DomainException('Unauthorized.');
            }

            $this->walletService->deductForBooking($userId, $tx->amount, 'Split bill payment');

            $tx->update(['status' => \App\Enums\TransactionStatus::Accepted]);
        });
    }
}

// ============================================================