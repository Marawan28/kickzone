<?php



// FILE: app/DTOs/Financial/SplitBillDTO.php
// ============================================================
namespace App\DTOs\Financial;

final class SplitBillDTO
{
    public function __construct(
        public readonly int   $bookingId,
        public readonly int   $initiatorId,
        /** @var int[] */
        public readonly array $recipientIds,
        public readonly float $totalAmount,
    ) {}

    public function getAmountPerPerson(): float
    {
        $count = count($this->recipientIds);
        return $count > 0
            ? round($this->totalAmount / ($count + 1), 2)
            : $this->totalAmount;
    }

    public static function fromArray(array $data, int $initiatorId): self
    {
        return new self(
            bookingId:    (int) $data['booking_id'],
            initiatorId:  $initiatorId,
            recipientIds: $data['recipient_ids'],
            totalAmount:  (float) $data['total_amount'],
        );
    }
}

// ============================================================