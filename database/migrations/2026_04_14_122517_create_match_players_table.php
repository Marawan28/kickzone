<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE: database/migrations/2024_01_01_000013_create_match_players_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('match_players', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['match_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('match_players'); }
};

// ============================================================