<?php

// FILE: database/migrations/2024_01_01_000001_create_cities_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 255);
        });
    }
    public function down(): void { Schema::dropIfExists('cities'); }
};
