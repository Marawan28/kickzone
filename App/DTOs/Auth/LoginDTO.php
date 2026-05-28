<?php

// FILE: app/DTOs/Auth/LoginDTO.php
// ============================================================
namespace App\DTOs\Auth;

final class LoginDTO
{
    public function __construct(
        public readonly string $phone,
        public readonly string $password,
        public readonly string $role,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            phone:    $data['phone'],
            password: $data['password'],
            role:     $data['role'],
        );
    }
}

// ============================================================