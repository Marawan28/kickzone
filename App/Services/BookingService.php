<?php

// FILE: app/Services/BookingService.php
// CRITICAL: High-concurrency double-booking prevention using
//           DB-level atomic UPDATE + SELECT FOR UPDATE
// ============================================================
namespace App\Services;
use App\Enums\{TransactionStatus, TransactionType};
use App\DTOs\Booking\CreateBookingDTO;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Repositories\Contracts\{
    BookingRepositoryInterface,
    FieldSlotRepositoryInterface,
    UserRepositoryInterface
};
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        public readonly BookingRepositoryInterface   $bookingRepo,
        private readonly FieldSlotRepositoryInterface $slotRepo,
        private readonly UserRepositoryInterface      $userRepo,
        private readonly WalletService               $walletService,
    ) {}

    /**
     * Create a confirmed booking atomically.
     *
     * Concurrency strategy:
     *  1. Wrap everything in a DB transaction.
     *  2. Use lockAndBook() which runs:
     *     UPDATE field_slots SET is_booked=1 WHERE id=? AND is_booked=0
     *  3. If 0 rows affected → slot already taken → throw exception.
     *  4. Deduct wallet balance (also inside the transaction).
     *  5. Create booking record.
     *
     * This ensures that even under race conditions, only one
     * request can claim the slot.
     */
 public function createBooking(CreateBookingDTO $dto): Booking
{
    return DB::transaction(function () use ($dto): Booking {
        // الخطوة 1: التأكد من أن الموعد موجود ومتاح (ونجيب بيانات الملعب معاه بخطوة واحدة لسرعة الأداء)
        $slot = $this->slotRepo->findAvailable($dto->slotId); // تأكد إن الـ Repo بيعمل eager load للملعب
        if (! $slot) {
            throw new \DomainException('This slot is no longer available.');
        }

        // الخطوة 2: حساب السعر (بقينا بنسحب السعر من الملعب المربوط بالـ slot)
        $price = $slot->field->price_per_hour;

        // الخطوة 3: التأكد من رصيد المحفظة
        $user = $this->userRepo->findById($dto->userId);
        if (! $user->hasSufficientBalance($price)) {
            throw new \DomainException('Insufficient wallet balance.');
        }

        // الخطوة 4: حجز الموعد قفله برمجياً (Atomic Lock) لمنع التكرار
        $locked = $this->slotRepo->lockAndBook($dto->slotId);
        if (! $locked) {
            throw new \DomainException('Slot was just booked by another user.');
        }

        // الخطوة 5: خصم المبلغ من المحفظة وتسجيل العملية المالية
        $this->walletService->deductForBooking($dto->userId, $price, $slot->field->name);

        // الخطوة 6: تسجيل عملية الدفع
        $payment = \App\Models\Payment::create([
            'user_id' => $dto->userId,
            'amount'  => $price,
            'status'  => \App\Enums\PaymentStatus::Paid,
        ]);

        // الخطوة 7: إنشاء الحجز (إضافة الـ field_id هنا هو التغيير اللي طلبته)
        return $this->bookingRepo->create([
            'user_id'    => $dto->userId,
            'field_id'   => $slot->field_id, // السطر ده حيوي جداً عشان الربط الجديد
            'slot_id'    => $dto->slotId,
            'payment_id' => $payment->id,
            'status'     => \App\Enums\BookingStatus::Confirmed,
            'booking_date' => now()->toDateString(), // تأكد إن الحقل ده موجود في الداتابيز أو الـ Repo بيتعامل معاه
        ]);

       
    });
}

    public function cancelBooking(int $bookingId, int $userId): void
    {
        DB::transaction(function () use ($bookingId, $userId): void {
            $booking = $this->bookingRepo->findById($bookingId);

            if ($booking->user_id !== $userId) {
                throw new \DomainException('Unauthorized.');
            }

            if ($booking->status !== BookingStatus::Confirmed) {
                throw new \DomainException('Booking cannot be cancelled.');
            }

            // Refund wallet
            $amount = $booking->payment->amount;
            $this->walletService->refund($userId, $amount, 'Booking cancellation refund');

            // Release slot
            $this->slotRepo->release($booking->slot_id);

            $this->bookingRepo->updateStatus($bookingId, BookingStatus::Cancelled->value);
        });
    }

    public function getUserBookings( $userId): \Illuminate\Support\Collection
    {
        return $this->bookingRepo->findByUser($userId);
    }
}

// ============================================================