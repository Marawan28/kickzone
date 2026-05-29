<?php

declare(strict_types=1);

// ============================================================
// FILE: app/Services/MatchmakingQueueService.php
// ============================================================
namespace App\Services;

use App\DTOs\Matchmaking\JoinQueueDTO;
use App\Enums\MatchmakingStatus;
use App\Enums\MatchStatus;
use App\Events\MatchmakingMatchedEvent;
use App\Models\Field;
use App\Models\MatchGame;
use App\Models\MatchmakingEntry;
use App\Models\Team;
use App\Models\User;
use App\Notifications\MatchmakingMatchedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MatchmakingQueueService
{
    /**
     * Skill tolerance for matching (± DSR points).
     * Players/teams within this range are considered compatible.
     */
    private const SKILL_TOLERANCE = 10.0;

    // ── Public API ─────────────────────────────────────────

    /**
     * Add a user or team to the matchmaking queue.
     * Immediately attempts to find a match after joining.
     *
     * @throws \DomainException if already in queue
     */
    public function joinQueue(JoinQueueDTO $dto): array
    {
        // Prevent duplicate queue entries
        $existing = MatchmakingEntry::where('matchable_type', $dto->matchableType)
            ->where('matchable_id', $dto->matchableId)
            ->waiting()
            ->first();

        if ($existing) {
            throw new \DomainException('أنت موجود بالفعل في طابور الانتظار.');
        }

        // Create the queue entry
        $entry = MatchmakingEntry::create([
            'matchable_type' => $dto->matchableType,
            'matchable_id'   => $dto->matchableId,
            'type'           => $dto->type,
            'player_count'   => $dto->playerCount,
            'skill_level'    => $dto->skillLevel,
            'preferred_time' => $dto->preferredTime,
            'city_id'        => $dto->cityId,
            'status'         => MatchmakingStatus::Waiting,
        ]);

        // Attempt to find a match immediately
        $match = $this->attemptMatch($entry);

        return [
            'entry'   => $entry->fresh(),
            'matched' => $match !== null,
            'match'   => $match,
        ];
    }

    /**
     * Cancel a queue entry.
     * Only the owner (or team captain) can cancel.
     *
     * @throws \DomainException if entry not found or not cancellable
     */
    public function cancelQueue(int $entryId, User $user): void
    {
        $entry = MatchmakingEntry::findOrFail($entryId);

        // Authorization: verify the user owns this entry
        $this->authorizeEntry($entry, $user);

        if ($entry->status !== MatchmakingStatus::Waiting) {
            throw new \DomainException('لا يمكن إلغاء طلب تم معالجته بالفعل.');
        }

        $entry->update(['status' => MatchmakingStatus::Cancelled]);
    }

    /**
     * Get the current queue status for a user.
     * Returns the active (waiting) entry if any.
     */
    public function getStatus(User $user): ?MatchmakingEntry
    {
        return MatchmakingEntry::where(function ($query) use ($user) {
                // Check for solo entries by this user
                $query->where(function ($q) use ($user) {
                    $q->where('matchable_type', User::class)
                      ->where('matchable_id', $user->id);
                })
                // Also check for team entries where this user is a member
                ->orWhere(function ($q) use ($user) {
                    $teamIds = $user->teams ?? collect();
                    // Get team IDs where user is a member
                    $memberTeamIds = \App\Models\TeamMember::where('user_id', $user->id)
                        ->pluck('team_id');

                    if ($memberTeamIds->isNotEmpty()) {
                        $q->where('matchable_type', Team::class)
                          ->whereIn('matchable_id', $memberTeamIds);
                    }
                });
            })
            ->whereIn('status', [MatchmakingStatus::Waiting, MatchmakingStatus::Matched])
            ->latest()
            ->first();
    }

    // ── Private Matching Logic ─────────────────────────────

    /**
     * Attempt to find a match for the given entry.
     * Routes to the appropriate strategy based on type.
     */
    private function attemptMatch(MatchmakingEntry $entry): ?MatchGame
    {
        return match ($entry->type) {
            'team' => $this->matchTeamVsTeam($entry),
            'solo' => $this->matchSoloPlayers($entry),
            default => null,
        };
    }

    /**
     * Team vs Team matching.
     *
     * Finds another waiting team entry with compatible criteria
     * and creates a match between them.
     *
     * Uses DB transaction + row locking (FOR UPDATE) to prevent
     * race conditions where two concurrent requests match the same team.
     */
    private function matchTeamVsTeam(MatchmakingEntry $entry): ?MatchGame
    {
        return DB::transaction(function () use ($entry): ?MatchGame {
            // Lock and find a compatible opponent team
            $opponent = MatchmakingEntry::waiting()
                ->ofType('team')
                ->inCity($entry->city_id)
                ->forPlayerCount($entry->player_count)
                ->atTime($entry->preferred_time)
                ->withinSkillRange($entry->skill_level, self::SKILL_TOLERANCE)
                ->where('id', '!=', $entry->id) // Exclude self
                ->lockForUpdate()               // 🔒 Prevent race conditions
                ->oldest()                       // First come, first served
                ->first();

            if (!$opponent) {
                return null;
            }

            // Also lock the current entry to prevent concurrent modifications
            $entry = MatchmakingEntry::lockForUpdate()->find($entry->id);

            // Double-check both are still waiting (guard against race)
            if (
                $entry->status !== MatchmakingStatus::Waiting ||
                $opponent->status !== MatchmakingStatus::Waiting
            ) {
                return null;
            }

            return $this->createMatchFromEntries(collect([$entry, $opponent]));
        });
    }

    /**
     * Solo player grouping.
     *
     * Finds enough solo players (including this entry) to fill a match.
     * Groups them by compatible criteria and creates a match when the
     * required player_count is reached.
     *
     * Uses DB transaction + row locking to prevent double-matching.
     */
    private function matchSoloPlayers(MatchmakingEntry $entry): ?MatchGame
    {
        return DB::transaction(function () use ($entry): ?MatchGame {
            // Find all compatible waiting solo entries (including the current one)
            $candidates = MatchmakingEntry::waiting()
                ->ofType('solo')
                ->inCity($entry->city_id)
                ->forPlayerCount($entry->player_count)
                ->atTime($entry->preferred_time)
                ->withinSkillRange($entry->skill_level, self::SKILL_TOLERANCE)
                ->lockForUpdate()   // 🔒 Lock all candidates
                ->oldest()          // First come, first served
                ->get();

            // Not enough players yet — stay in queue
            if ($candidates->count() < $entry->player_count) {
                return null;
            }

            // Take exactly the number of players needed
            $selected = $candidates->take($entry->player_count);

            // Double-check all are still waiting
            $allWaiting = $selected->every(
                fn (MatchmakingEntry $e) => $e->status === MatchmakingStatus::Waiting
            );

            if (!$allWaiting) {
                return null;
            }

            return $this->createMatchFromEntries($selected);
        });
    }

    /**
     * Create a new match from the matched queue entries.
     *
     * 1. Finds an available field in the preferred city
     * 2. Creates a MatchGame record
     * 3. Adds all players to the match
     * 4. Updates queue entries to 'matched'
     * 5. Dispatches event + notifications
     *
     * This runs INSIDE a DB transaction (called from matchTeamVsTeam or matchSoloPlayers).
     */
    private function createMatchFromEntries(Collection $entries): MatchGame
    {
        $firstEntry = $entries->first();

        // Find an available field in the preferred city
        $field = Field::where('city_id', $firstEntry->city_id)
            ->inRandomOrder()
            ->first();

        if (!$field) {
            throw new \DomainException('لا يوجد ملعب متاح في المدينة المحددة.');
        }

        // Determine match date based on preferred time
        $matchDate = $this->resolveMatchDate($firstEntry->preferred_time);

        // Create the match
        $match = MatchGame::create([
            'creator_id'  => $this->resolveCreatorId($firstEntry),
            'field_id'    => $field->id,
            'match_date'  => $matchDate,
            'max_players' => $firstEntry->player_count,
            'status'      => MatchStatus::Open,
        ]);

        // Create a chat room for the match
        \App\Models\Chat::create(['match_id' => $match->id]);

        // Collect all user IDs to add as players
        $userIds = $this->resolveAllUserIds($entries);

        // Add all players to the match
        $match->players()->syncWithoutDetaching($userIds);

        // Mark the match as full if we have all players
        if ($match->players()->count() >= $match->max_players) {
            $match->update(['status' => MatchStatus::Full->value]);
        }

        // Update all queue entries to 'matched'
        MatchmakingEntry::whereIn('id', $entries->pluck('id'))
            ->update([
                'status'           => MatchmakingStatus::Matched,
                'matched_match_id' => $match->id,
            ]);

        // Refresh entries for the event
        $matchedEntries = MatchmakingEntry::whereIn('id', $entries->pluck('id'))->get();

        // ── Dispatch real-time event ───────────────────────
        event(new MatchmakingMatchedEvent($match, $matchedEntries));

        // ── Send database notifications to all players ─────
        $users = User::whereIn('id', $userIds)->get();
        foreach ($users as $user) {
            $user->notify(new MatchmakingMatchedNotification($match));
        }

        return $match->load(['field.city', 'players', 'creator']);
    }

    // ── Helper Methods ─────────────────────────────────────

    /**
     * Resolve all user IDs from a collection of queue entries.
     * For solo entries: the matchable_id IS the user_id.
     * For team entries: get all team member user IDs.
     */
    private function resolveAllUserIds(Collection $entries): array
    {
        $userIds = [];

        foreach ($entries as $entry) {
            if ($entry->matchable_type === User::class) {
                $userIds[] = $entry->matchable_id;
            } elseif ($entry->matchable_type === Team::class) {
                $team = Team::with('members')->find($entry->matchable_id);
                if ($team) {
                    foreach ($team->members as $member) {
                        $userIds[] = $member->user_id;
                    }
                }
            }
        }

        return array_unique($userIds);
    }

    /**
     * Resolve the creator_id for the match.
     * Solo → the first player. Team → the first team member (captain).
     */
    private function resolveCreatorId(MatchmakingEntry $entry): int
    {
        if ($entry->matchable_type === User::class) {
            return $entry->matchable_id;
        }

        // For team: use the first team member as creator
        $team = Team::with('members')->find($entry->matchable_id);
        return $team?->members->first()?->user_id ?? $entry->matchable_id;
    }

    /**
     * Resolve a match date/time from the preferred time slot.
     * Uses today's or tomorrow's date with an appropriate hour.
     */
    private function resolveMatchDate(string $preferredTime): string
    {
        $now  = now();
        $date = $now->copy();

        $hour = match ($preferredTime) {
            'morning' => 9,
            'evening' => 17,
            'night'   => 21,
            default   => 18,
        };

        $date->setTime($hour, 0);

        // If the time already passed today, schedule for tomorrow
        if ($date->lte($now)) {
            $date->addDay();
        }

        return $date->toDateTimeString();
    }

    /**
     * Verify the user is authorized to manage this queue entry.
     *
     * @throws \DomainException
     */
    private function authorizeEntry(MatchmakingEntry $entry, User $user): void
    {
        if ($entry->matchable_type === User::class && $entry->matchable_id !== $user->id) {
            throw new \DomainException('غير مصرح لك بإلغاء هذا الطلب.');
        }

        if ($entry->matchable_type === Team::class) {
            $isMember = \App\Models\TeamMember::where('team_id', $entry->matchable_id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$isMember) {
                throw new \DomainException('غير مصرح لك بإلغاء هذا الطلب.');
            }
        }
    }
}
