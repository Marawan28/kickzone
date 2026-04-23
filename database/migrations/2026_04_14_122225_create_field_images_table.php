<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// FILE: database/migrations/2024_01_01_000006_create_field_images_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('field_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->string('image_path', 255);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('field_images'); }
};

// ============================================================