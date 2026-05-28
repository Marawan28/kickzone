<?php


// FILE: app/DTOs/Auth/RegisterOwnerDTO.php
// ============================================================
namespace App\DTOs\Auth;

use App\Enums\UserRole;

final class RegisterOwnerDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
        public readonly string $email,
        public readonly string $password,
        public readonly UserRole $role = UserRole::Owner,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:     $data['name'],
            phone:    $data['phone'],
            email:    $data['email'],
            password: $data['password'],
        );
    }
}

// ============================================================