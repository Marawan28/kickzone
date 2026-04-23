<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE: database/migrations/2024_01_01_000008_create_field_slots_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('field_slots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->tinyInteger('is_booked')->default(0);
            $table->timestamps();
            // Index for fast availability queries
            $table->index(['field_id', 'is_booked', 'start_time']);
        });
    }
    public function down(): void { Schema::dropIfExists('field_slots'); }
};

// ============================================================
