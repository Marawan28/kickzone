<?php

// ============================================================
// FILE: app/Http/Controllers/API/V1/Profile/ProfileController.php
// ============================================================
namespace App\Http\Controllers\API\V1\Profile;

use App\DTOs\Profile\UpdateProfileDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\Profile\ProfileResource;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Profile", description="Player profile management")
 */
class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService,
    ) {}

    /**
     * @OA\Get(path="/api/v1/profile", tags={"Profile"}, security={{"sanctum":{}}})
     */
    public function show(Request $request): JsonResponse
    {
        $user  = $request->user()->load(['city', 'profile']);
        $stats = $this->profileService->getPlayerStats($request->user()->id);
        return response()->json(['data' => new ProfileResource($user), 'stats' => $stats]);
    }

    /**
     * @OA\Put(path="/api/v1/profile", tags={"Profile"}, security={{"sanctum":{}}})
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->profileService->updateProfile(
            $request->user()->id,
            UpdateProfileDTO::fromArray($request->validated()),
        );
        return response()->json(['message' => 'Profile updated.', 'data' => new ProfileResource($user)]);
    }

    /**
     * @OA\Post(path="/api/v1/profile/avatar", tags={"Profile"}, security={{"sanctum":{}}})
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate(['avatar' => ['required', 'image', 'max:2048']]);
        $user = $this->profileService->uploadAvatar($request->user()->id, $request->file('avatar'));
        return response()->json(['avatar_url' => $user->getFirstMediaUrl('avatar')]);
    }
}
