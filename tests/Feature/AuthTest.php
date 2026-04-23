<?php

// ============================================================
// FILE: tests/Feature/AuthTest.php
// ============================================================
namespace Tests\Feature;

use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_can_register(): void
    {
        $city = City::factory()->create(['name' => 'Zagazig']);

        $response = $this->postJson('/api/v1/auth/register/player', [
            'name'                  => 'Omar Nooh',
            'phone'                 => '01201728505',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'city_id'               => $city->id,
            'terms'                 => true,
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.name', 'Omar Nooh');

        $this->assertDatabaseHas('users', ['phone' => '01201728505', 'role' => 'player']);
    }

    public function test_player_can_login(): void
    {
        $city   = City::factory()->create();
        $player = \App\Models\User::factory()->create([
            'phone'    => '01201728505',
            'password' => bcrypt('password123'),
            'role'     => 'player',
            'city_id'  => $city->id,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'phone'    => '01201728505',
            'password' => 'password123',
            'role'     => 'player',
        ]);

        $response->assertStatus(200)->assertJsonStructure(['token', 'data']);
    }
}