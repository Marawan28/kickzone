<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // If already corrected, do nothing.
        if (Schema::hasTable('team_requests')) {
            return;
        }

        // Create the correct table structure.
        Schema::create('team_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            // Use user_id consistently in app code.
            $table->foreignId('player_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // Migrate data from legacy singular table if it exists.
        if (Schema::hasTable('team_request')) {
            Schema::table('team_requests', function (Blueprint $table): void {
                // no-op; keep structure
            });

            \DB::statement('INSERT INTO team_requests (id, team_id, player_id, status, created_at, updated_at) ' .
                'SELECT id, team_id, player_id, status, created_at, updated_at FROM team_request');

            Schema::drop('team_request');
        }

        // Ensure the FK column naming expected by the app models.
        // App\Models\TeamRequest expects player_id for the requester.
    }

    public function down(): void
    {
        // Recreate legacy table if needed.
        Schema::dropIfExists('team_requests');

        if (Schema::hasTable('team_request')) {
            return;
        }

        Schema::create('team_request', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }
};

