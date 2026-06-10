<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            if (!Schema::hasColumn('bookings', 'booking_date')) {
                $table->date('booking_date')->nullable();
            }

            if (!Schema::hasColumn('bookings', 'field_id')) {
                $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();
            }
        });

        // Backfill field_id + booking_date when possible.
        // This avoids nulls for existing rows.
        \DB::statement('UPDATE bookings b
            SET b.field_id = fs.field_id,
                b.booking_date = DATE(b.created_at)
            FROM field_slots fs
            WHERE b.slot_id = fs.id
              AND (b.field_id IS NULL OR b.booking_date IS NULL)');
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            if (Schema::hasColumn('bookings', 'booking_date')) {
                $table->dropColumn('booking_date');
            }
            if (Schema::hasColumn('bookings', 'field_id')) {
                $table->dropConstrainedForeignId('field_id');
                $table->dropColumn('field_id');
            }
        });
    }
};

