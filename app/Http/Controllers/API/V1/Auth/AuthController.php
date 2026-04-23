<?php

// FILE: app/Http/Controllers/API/AuthController.php
// ============================================================
declare(strict_types=1);

namespace App\Http\Controllers\API\V1\Auth;

use App\DTOs\Auth\{LoginDTO, RegisterOwnerDTO, RegisterPlayerDTO};
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\{
    ForgotPasswordRequest,
    LoginRequest,
    OnboardingRequest,
    RegisterOwnerRequest,
    RegisterPlayerRequest,
    ResetPasswordRequest,
    VerifyOtpRequest
};
use App\Http\Resources\Auth\AuthResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Auth", description="Authentication endpoints")
 */
class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    /**
     * @OA\Post(
     *     path="/api/register/player",
     *     tags={"Auth"},
     *     summary="Register a new player",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/RegisterPlayerRequest")),
     *     @OA\Response(response=201, description="Player registered — OTP sent")
     * )
     */
    public function registerPlayer(RegisterPlayerRequest $request): JsonResponse
    {
        
        $user = $this->authService->registerPlayer(
            RegisterPlayerDTO::fromArray($request->validated())
        );

        return response()->json([
            'message' => 'Account created. Please verify your phone number.',
            'data'    => new AuthResource($user),
        ], 201);
    }

    /**
     * @OA\Post(path="/api/register/owner", tags={"Auth"})
     */
    public function registerOwner(RegisterOwnerRequest $request): JsonResponse
    {
        $user = $this->authService->registerOwner(
            RegisterOwnerDTO::fromArray($request->validated())
        );

        return response()->json([
            'message' => 'Owner account created. Add your stadium next.',
            'data'    => new AuthResource($user),
        ], 201);
    }

    /**
     * @OA\Post(path="/api/login", tags={"Auth"})
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            LoginDTO::fromArray($request->validated())
        );

        return response()->json([
            'message' => 'Login successful.',
            'data'    => new AuthResource($result['user']),
            'token'   => $result['token'],
        ]);
    }

    /**
     * @OA\Post(path="/api/verify-otp", tags={"Auth"})
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $verified = $this->authService->verifyOtp(
            $request->phone,
            $request->otp,
        );

        return response()->json([
            'message'  => $verified ? 'Phone verified.' : 'Invalid OTP.',
            'verified' => $verified,
        ], $verified ? 200 : 422);
    }

    /**
     * @OA\Post(path="/api/forgot-password", tags={"Auth"})
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authService->sendPasswordResetOtp($request->phone);
        return response()->json(['message' => 'OTP sent to your phone number.']);
    }

    /**
     * @OA\Post(path="/api/reset-password", tags={"Auth"})
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->authService->resetPassword(
            $request->phone,
            $request->otp,
            $request->password,
        );
        return response()->json(['message' => 'Password reset successfully.']);
    }

    /**
     * @OA\Post(path="/api/google/callback", tags={"Auth"})
     */
    public function googleCallback(Request $request): JsonResponse
    {
        // In production, validate the Google ID token server-side
        $result = $this->authService->handleGoogleCallback($request->all());
        return response()->json([
            'data'  => new AuthResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    /**
     * @OA\Post(path="/api/onboarding", tags={"Auth"})
     */
    public function onboarding(OnboardingRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update([
            'name'               => $request->nickname,
            'preferred_position' => $request->preferred_position,
        ]);
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['skill_level' => $request->skill_level],
        );
        return response()->json(['message' => 'Profile setup complete.']);
    }

    /**
     * @OA\Post(path="/api/logout", tags={"Auth"}, security={{"sanctum":{}}})
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }
}
