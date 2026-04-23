<?php

// FILE: app/DTOs/Auth/RegisterPlayerDTO.php
// ============================================================
declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Enums\UserRole;

final class RegisterPlayerDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
        public readonly string $password,
        public readonly int    $cityId,
        public readonly UserRole $role = UserRole::Player,
        public readonly ?string $email = null ,
        public readonly ?string $preferredPosition = null,
        public readonly ?string $bio = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:     $data['name'],
            phone:    $data['phone'],
            password: $data['password'],
            cityId:   (int) $data['city_id'],
            bio: $data['bio'] ?? null,
            email: $data['email'] ?? null ,
            preferredPosition: $data['preferred_position'] ?? null,
            role: $data['role'] ?? UserRole::Player,
        );
    }
}

// ============================================================