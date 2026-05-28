<?php

// FILE: app/Services/ProfileService.php
// ============================================================
namespace App\Services;

use App\DTOs\Profile\UpdateProfileDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProfileService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepo,
        private readonly DsrService              $dsrService,
    ) {}

    public function updateProfile(int $userId, UpdateProfileDTO $dto): User
    {
        $user = $this->userRepo->findById($userId);
        return $this->userRepo->update($user, [
            'name'               => $dto->name,
            'phone'              => $dto->phone,
            'email'              => $dto->email,
            'preferred_position' => $dto->preferredPosition,
            'city_id'            => $dto->cityId,
            'bio'                => $dto->bio,
        ]);
    }

    public function uploadAvatar(int $userId, $file): User
    {
        $user = $this->userRepo->findById($userId);
        $user->addMedia($file)
             ->toMediaCollection('avatar');
        return $user->fresh();
    }

    public function getPlayerStats(int $userId): array
    {
        $user    = User::withCount([
            'matchesPlayed',
        ])->find($userId);

        $wins = $user->matchesPlayed()
                     ->wherePivotIn('result', ['win'])
                     ->count();

        return [
            'matches'    => $user->matches_played_count,
            'wins'       => $wins,
            'dsr_score'  => $user->dsr_score,
            'dsr_label'  => $this->dsrService->getDsrLabel((float) $user->dsr_score),
        ];
    }
}
