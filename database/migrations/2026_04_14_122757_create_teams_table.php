<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE: database/migrations/2024_01_01_000019_create_teams_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->string('name', 50);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('teams'); }
};

// ============================================================