<?php


// FILE: app/DTOs/Booking/CreateBookingDTO.php
// ============================================================
namespace App\DTOs\Booking;

final class CreateBookingDTO
{
    public function __construct(
        public readonly int    $userId,
         
        public readonly int    $slotId,
        public readonly string $paymentMethod,
        public readonly ?string $discountCode = null,
    ) {}

    public static function fromArray(array $data, int $userId): self
    {
        return new self(
            userId:        $userId,

            slotId:        (int) $data['slot_id'],
            paymentMethod: $data['payment_method'],
            discountCode:  $data['discount_code'] ?? null,
        );
    }
}

// ============================================================