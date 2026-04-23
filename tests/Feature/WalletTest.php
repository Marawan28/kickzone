<?php

// ============================================================
// FILE: tests/Feature/WalletTest.php
// ============================================================
namespace Tests\Feature;

use App\Models\{City, User};
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_top_up_credits_balance_and_creates_transaction(): void
    {
        $city   = City::factory()->create();
        $player = User::factory()->create(['role' => 'player', 'balance' => 0, 'city_id' => $city->id]);

        app(WalletService::class)->topUp($player->id, 200.0, 'ref-abc');

        $this->assertDatabaseHas('users', ['id' => $player->id, 'balance' => 200]);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $player->id,
            'amount'  => 200,
            'type'    => 'deposit',
            'status'  => 'accepted',
        ]);
    }

    public function test_insufficient_balance_throws_exception(): void
    {
        $city   = City::factory()->create();
        $player = User::factory()->create(['role' => 'player', 'balance' => 50, 'city_id' => $city->id]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Insufficient balance.');

        app(WalletService::class)->withdraw($player->id, 200.0);
    }
}
