<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE: database/migrations/2024_01_01_000002_create_users_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->nullable()->unique();
            $table->string('phone', 20)->unique();
            $table->string('password', 255)->nullable();
            $table->string('google_id')->nullable()->unique();
            $table->enum('role', ['player', 'owner', 'admin'])->default('player');
            $table->decimal('dsr_score', 10)->default(50);
            $table->enum('preferred_position', ['attacker','midfielder','defender','goalkeeper'])->nullable();
            $table->decimal('balance', 10)->default(0);
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->text('bio')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('users'); }
};

// ============================================================