<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ============================================================
// FILE: database/migrations/2026_05_29_200002_make_teams_match_id_nullable.php
// ============================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            // Allow teams to exist independently (before being matched to a game)
            $table->foreignId('match_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->foreignId('match_id')->nullable(false)->change();
        });
    }
};
