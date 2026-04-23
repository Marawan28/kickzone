<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE: database/migrations/2024_01_01_000012_create_matches_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->dateTime('match_date');
            $table->integer('max_players');
            $table->enum('status', ['open', 'full', 'finished'])->default('open');
            $table->timestamps();
            $table->index(['status', 'match_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('matches'); }
};

// ============================================================