<?php

// FILE: app/Services/DashboardService.php
// ============================================================
declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Field;
use App\Enums\BookingStatus;
use Illuminate\Support\Carbon;

class DashboardService
{
    /**
     * Gather all dashboard statistics for a given owner.
     *
     * @param  int  $ownerId
     * @return array{
     *     owner_name: string,
     *     today_profits: float,
     *     upcoming_bookings_count: int,
     *     stadium_rating: float,
     *     rating_label: string,
     * }
     */
    public function getOwnerStats(int $ownerId): array
    {
        // ── 1. Collect all field IDs owned by this user ──────────────────
        $fieldIds = Field::where('owner_id', $ownerId)->pluck('id');

        // ── 2. Today's Profits ───────────────────────────────────────────
        // Sum the amounts from payments whose booking is confirmed,
        // belongs to one of the owner's fields, and was made today.
        $todayProfits = Booking::query()
            ->whereIn('field_id', $fieldIds)
            ->where('status', BookingStatus::Confirmed)
            ->whereDate('created_at', Carbon::today())
            ->join('payments', 'payments.id', '=', 'bookings.payment_id')
            ->sum('payments.amount');

        // ── 3. Upcoming Bookings ─────────────────────────────────────────
        // Confirmed bookings whose slot starts in the future.
        $upcomingBookingsCount = Booking::query()
            ->whereIn('field_id', $fieldIds)
            ->where('status', BookingStatus::Confirmed)
            ->join('field_slots', 'field_slots.id', '=', 'bookings.slot_id')
            ->where('field_slots.start_time', '>', Carbon::now())
            ->count();

        // ── 4. Stadium Rating ────────────────────────────────────────────
        // Weighted average across ALL reviews for ALL fields of this owner.
        $averageRating = Field::query()
            ->where('owner_id', $ownerId)
            ->withAvg('reviews', 'rating')
            ->get()
            ->avg('reviews_avg_rating') ?? 0.0;

        $averageRating = round((float) $averageRating, 1);

        return [
            'owner_name'              => $this->resolveOwnerName($ownerId),
            'today_profits'           => (float) $todayProfits,
            'upcoming_bookings_count' => $upcomingBookingsCount,
            'stadium_rating'          => $averageRating,
            'rating_label'            => $this->ratingLabel($averageRating),
        ];
    }

    // ── Private helpers ──────────────────────────────────────────────────

    /**
     * Resolve a friendly display name for the owner.
     */
    private function resolveOwnerName(int $ownerId): string
    {
        $user = \App\Models\User::find($ownerId);

        return $user?->name ?? 'Owner';
    }

    /**
     * Map a numeric rating to a human-readable label.
     */
    private function ratingLabel(float $rating): string
    {
        return match (true) {
            $rating >= 4.5 => 'Excellent',
            $rating >= 4.0 => 'Very Good',
            $rating >= 3.0 => 'Good',
            $rating >= 2.0 => 'Fair',
            default        => 'Poor',
        };
    }
}

// ============================================================
