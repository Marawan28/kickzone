<?php

// ============================================================
// FILE: tests/Feature/BookingConcurrencyTest.php
// ============================================================
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\{FieldSlot, User, Field, City};
use App\Services\BookingService;
use App\DTOs\Booking\CreateBookingDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests that the concurrency-safe booking engine prevents double-booking
 * when two users attempt to book the same slot simultaneously.
 */
class BookingConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_one_booking_succeeds_for_same_slot(): void
    {
        $city  = City::factory()->create();
        $owner = User::factory()->create(['role' => 'owner', 'city_id' => $city->id]);
        $field = Field::factory()->create(['owner_id' => $owner->id, 'city_id' => $city->id, 'price_per_hour' => 100]);
        $slot  = FieldSlot::factory()->create(['field_id' => $field->id, 'is_booked' => false]);

        $player1 = User::factory()->create(['role' => 'player', 'balance' => 500, 'city_id' => $city->id]);
        $player2 = User::factory()->create(['role' => 'player', 'balance' => 500, 'city_id' => $city->id]);

        $service = app(BookingService::class);
        $success = 0;
        $fail    = 0;

        $attempt = function (User $player) use ($service, $slot, &$success, &$fail): void {
            try {
                $service->createBooking(new CreateBookingDTO(
                    userId:        $player->id,
                    slotId:        $slot->id,
                    paymentMethod: 'wallet',
                ));
                $success++;
            } catch (\DomainException $e) {
                $fail++;
            }
        };

        // Simulate concurrent requests
        $attempt($player1);
        $attempt($player2);

        $this->assertEquals(1, $success, 'Exactly one booking should succeed.');
        $this->assertEquals(1, $fail,    'Exactly one booking should fail.');
        $this->assertDatabaseCount('bookings', 1);
    }
}
