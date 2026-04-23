<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE: database/migrations/2024_01_01_000015_create_messages_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->timestamp('sent_at')->useCurrent();
            $table->index(['chat_id', 'sent_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('messages'); }
};

// ============================================================