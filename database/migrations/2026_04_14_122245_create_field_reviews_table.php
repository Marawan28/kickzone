<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE: database/migrations/2024_01_01_000007_create_field_reviews_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('field_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('users')->cascadeOnDelete();
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('field_reviews'); }
};

// ============================================================