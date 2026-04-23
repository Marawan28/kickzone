<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE: database/migrations/2024_01_01_000004_create_profiles_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('skill_level')->default(1);
            $table->string('preferred_position', 50)->nullable();
            $table->integer('points')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('profiles'); }
};

// ============================================================
