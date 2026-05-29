<?php

// FILE: routes/api.php
// ============================================================
declare(strict_types=1);

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Field\FieldController;
use App\Http\Controllers\API\V1\Booking\BookingController;
use App\Http\Controllers\API\V1\Community\CommunityController;
use App\Http\Controllers\API\V1\Financial\WalletController;
use App\Http\Controllers\API\V1\Match\MatchController;
use App\Http\Controllers\API\V1\Profile\ProfileController;
use App\Http\Controllers\API\V1\Team\TeamController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\NotificationController;
use App\Http\Controllers\API\V1\Chat\ChatController;

// ── Public routes (no auth) ────────────────────────────────
Route::prefix('v1')->group(function (): void {

    Route::prefix('auth')->group(function (): void {
        Route::post('registerPlayer',  [AuthController::class, 'registerPlayer']);
        Route::post('registerOwner',   [AuthController::class, 'registerOwner']);
        Route::post('login',            [AuthController::class, 'login']);
        Route::post('verify-otp',       [AuthController::class, 'verifyOtp']);
        Route::post('forgot-password',  [AuthController::class, 'forgotPassword']);
        Route::post('reset-password',   [AuthController::class, 'resetPassword']);
        Route::post('googleCallback',  [AuthController::class, 'googleCallback']);
     
});
    // ── Protected routes (Sanctum auth) ───────────────────
    Route::middleware('auth:sanctum')->group(function (): void {

        // Auth
        Route::post('auth/logout',    [AuthController::class, 'logout']);
        Route::post('auth/onboarding',[AuthController::class, 'onboarding']);

        // Profile
        Route::get ('profile',          [ProfileController::class, 'show']);
        Route::put ('profile',          [ProfileController::class, 'update']);
        Route::post('profileAvatar',   [ProfileController::class, 'uploadAvatar']);

        // Cities (read-only, for dropdowns)
        Route::get('cities', fn() => response()->json(
            \App\Models\City::all(['id','name'])
        ));

        // Fields (owners manage, players browse)
        Route::apiResource('fields', FieldController::class);
        Route::get('field/{field}/slots', [FieldController::class, 'slots']);

        // Bookings
        Route::get   ('bookings',       [BookingController::class, 'index']);
        Route::post  ('bookings',       [BookingController::class, 'store']);
        Route::get   ('bookings/{id}',  [BookingController::class, 'show']);
        Route::delete('bookings/{id}',  [BookingController::class, 'cancel']);

        // Wallet
        Route::get ('wallet',            [WalletController::class, 'balance']);
        Route::post('wallet/top-up',     [WalletController::class, 'topUp']);
        Route::post('wallet/withdraw',   [WalletController::class, 'withdraw']);
        Route::post('wallet/split-bill', [WalletController::class, 'splitBill']);

        // Matches & Matchmaking
        Route::post('matches',              [MatchController::class, 'store']);
        Route::get ('matches/{id}',         [MatchController::class, 'show']);
        Route::post('matches/{id}/join',    [MatchController::class, 'join']);
        Route::post('matches/{id}/rate',    [MatchController::class, 'submitRating']);
        Route::patch('matches/{id}/finish', [MatchController::class, 'finish']);

        // AI recommendations
        Route::post('matchmaking',          [MatchController::class, 'matchmaking']);

        // Queue-based matchmaking
        Route::post('matchmaking/queue',              [MatchController::class, 'joinQueue']);
        Route::delete('matchmaking/queue/{id}',      [MatchController::class, 'cancelQueue']);
        Route::get('matchmaking/queue/status',        [MatchController::class, 'status']);

        // Community
        Route::get ('community/feed',                     [CommunityController::class, 'feed']);
        Route::post('community/posts',                    [CommunityController::class, 'store']);
        Route::post('community/posts/{id}/like',          [CommunityController::class, 'toggleLike']);
        Route::post('community/posts/{id}/comments',      [CommunityController::class, 'addComment']);
        Route::get ('community/forums',                   [CommunityController::class, 'forums']);

        // Teams
        Route::apiResource('teams', TeamController::class);
        Route::post('teams/{id}/request', [TeamController::class, 'request']);
        Route::patch('team-requests/{id}', [TeamController::class, 'respondToRequest']);

        // Messages
        Route::get ('chats',                   [ChatController::class, 'index']);
        Route::get ('chats/{id}/messages',     [ChatController::class, 'messages']);
        Route::post('chats/{id}/messages',     [ChatController::class, 'send']);
        

       Route::get('notifications', [NotificationController::class, 'index']);
       Route::patch('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    });
});

Route::get('/v1/test', function () {
    return response()->json([
        'message' => 'API works 🚀'
    ]);
});



// ============================================================