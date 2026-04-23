<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE: database/migrations/2024_01_01_000005_create_fields_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 100);
            $table->text('address');
            $table->integer('price_per_hour');
            $table->foreignId('city_id')->constrained()->OnDelete('set null');
            $table->text('descreption')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('fields'); }
};

// ============================================================