<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


// FILE: database/migrations/2024_01_01_000018_create_post_likes_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('post_likes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['post_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('post_likes'); }
};

// ============================================================