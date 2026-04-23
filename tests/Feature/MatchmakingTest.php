<?php

// ============================================================
// FILE: tests/Feature/MatchmakingTest.php
// ============================================================
namespace Tests\Feature;

use App\Models\{City, Field, FieldSlot, MatchGame, User};
use App\Services\MatchService;
use App\DTOs\Match\MatchmakingDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchmakingTest extends TestCase
{
    use RefreshDatabase;

    public function test_matchmaking_returns_scored_matches(): void
    {
        $city    = City::factory()->create(['name' => 'Zagazig']);
        $owner   = User::factory()->create(['role' => 'owner', 'city_id' => $city->id]);
        $field   = Field::factory()->create([
            'owner_id'       => $owner->id,
            'city_id'        => $city->id,
            'price_per_hour' => 100,
        ]);
        $creator = User::factory()->create([
            'role'      => 'player',
            'dsr_score' => 70,
            'city_id'   => $city->id,
        ]);

        MatchGame::factory()->create([
            'creator_id'  => $creator->id,
            'field_id'    => $field->id,
            'status'      => 'open',
            'max_players' => 10,
        ]);

        $seeker = User::factory()->create([
            'role'               => 'player',
            'dsr_score'          => 72,
            'city_id'            => $city->id,
            'preferred_position' => 'defender',
        ]);

        $dto = new MatchmakingDTO(
            userId:     $seeker->id,
            position:   'defender',
            cityId:     $city->id,
            timeSlot:   'evening',
            customTime: null,
            minBudget:  50,
            maxBudget:  200,
            mode:       'solo',
        );

        $results = app(MatchService::class)->findMatchesForPlayer($dto);

        $this->assertNotEmpty($results);
        $this->assertArrayHasKey('score', $results->first());
        $this->assertArrayHasKey('match', $results->first());
    }
}
