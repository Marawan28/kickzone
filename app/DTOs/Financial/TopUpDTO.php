<?php


// FILE: app/DTOs/Financial/TopUpDTO.php
// ============================================================
namespace App\DTOs\Financial;

final class TopUpDTO
{
    public function __construct(
        public readonly int   $userId,
        public readonly float $amount,
        public readonly string $method,
        public readonly string $reference,
    ) {}

    public static function fromArray(array $data, int $userId): self
    {
        return new self(
            userId:    $userId,
            amount:    (float) $data['amount'],
            method:    $data['method'],
            reference: $data['reference'],
        );
    }
}

// ============================================================