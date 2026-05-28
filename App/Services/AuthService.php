<?php


// FILE: app/Services/AuthService.php
// ============================================================
declare(strict_types=1);

namespace App\Services;

use App\DTOs\Auth\{LoginDTO, RegisterPlayerDTO, RegisterOwnerDTO};
use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\UnauthorizedException;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepo,
        private readonly OtpService              $otpService,
    ) {}

public function registerPlayer(RegisterPlayerDTO $dto): User
{
    
    $user = $this->userRepo->create([
        'name'               => $dto->name,
        'phone'              => $dto->phone,
        'email'              => $dto->email, // السطر ده كان ناقص
        'password'           => Hash::make($dto->password),
        'role'               => UserRole::Player,
        'city_id'            => $dto->cityId,
        'preferred_position' => $dto->preferredPosition, // والسطر ده كمان
        'dsr_score'          => 50.00,
        'balance'            => 0.00,
        'bio'                => $dto->bio,
    ]);

    $this->otpService->send($user->phone);

    return $user;
}

    public function registerOwner(RegisterOwnerDTO $dto): User
    {
        return $this->userRepo->create([
            'name'     => $dto->name,
            'phone'    => $dto->phone,
            'email'    => $dto->email,
            'password' => Hash::make($dto->password),
            'role'     => UserRole::Owner,
            'balance'  => 0.00,
        ]);
    }

    public function login(LoginDTO $dto): array
    {
        $user = $this->userRepo->findByPhone($dto->phone);

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw new UnauthorizedException('Invalid credentials.');
        }

        if ($user->role->value !== $dto->role) {
            throw new UnauthorizedException('Role mismatch.');
        }

        $token = $user->createToken('kickzone-token')->plainTextToken;

        return compact('user', 'token');
    }

    public function handleGoogleCallback(array $googleUser): array
    {
        $user = User::firstOrCreate(
            ['google_id' => $googleUser['id']],
            [
                'name'      => $googleUser['name'],
                'email'     => $googleUser['email'],
                'role'      => UserRole::Player,
                'dsr_score' => 50.00,
                'balance'   => 0.00,
            ]
        );

        $token = $user->createToken('kickzone-google')->plainTextToken;

        return compact('user', 'token');
    }

    public function verifyOtp(string $phone, string $otp): bool
    {
        return $this->otpService->verify($phone, $otp);
    }

    public function sendPasswordResetOtp(string $phone): void
    {
        $user = $this->userRepo->findByPhone($phone);
        if ($user) {
            $this->otpService->send($phone);
        }
    }

    public function resetPassword(string $phone, string $otp, string $newPassword): void
    {
        if (! $this->otpService->verify($phone, $otp)) {
            throw new \DomainException('Invalid or expired OTP.');
        }

        $user = $this->userRepo->findByPhone($phone);
        $this->userRepo->update($user, ['password' => Hash::make($newPassword)]);
    }
}

// ============================================================