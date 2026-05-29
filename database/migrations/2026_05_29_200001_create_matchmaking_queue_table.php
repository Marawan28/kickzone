<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ============================================================
// FILE: database/migrations/2026_05_29_200001_create_matchmaking_queue_table.php
// ============================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('matchmaking_queue', function (Blueprint $table): void {
            $table->id();

            // Polymorphic: can be App\Models\User (solo) or App\Models\Team (team)
            $table->morphs('matchable');

            // Explicit type for fast filtering without checking matchable_type string
            $table->enum('type', ['solo', 'team']);

            // Total players needed for the match (e.g. 6 for 3v3, 10 for 5v5, 14 for 7v7)
            $table->integer('player_count');

            // Snapshot of DSR score at queue time to avoid re-querying
            $table->decimal('skill_level', 5, 2);

            // When the user prefers to play
            $table->enum('preferred_time', ['morning', 'evening', 'night']);

            // Which city the user wants to play in
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();

            // Queue entry status
            $table->enum('status', ['waiting', 'matched', 'cancelled', 'expired'])
                  ->default('waiting');

            // Link to the match created after successful matchmaking
            $table->foreignId('matched_match_id')
                  ->nullable()
                  ->constrained('matches')
                  ->nullOnDelete();

            $table->timestamps();

            // Composite index for the matchmaking query
            $table->index(['type', 'status', 'city_id', 'player_count', 'preferred_time'], 'mmq_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaking_queue');
    }
};
