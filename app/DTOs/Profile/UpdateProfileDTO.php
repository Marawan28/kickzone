<?php



// FILE: app/DTOs/Profile/UpdateProfileDTO.php
// ============================================================
namespace App\DTOs\Profile;

final class UpdateProfileDTO
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $phone,
        public readonly ?string $email,
        public readonly string  $preferredPosition,
        public readonly int     $cityId,
        public readonly ?string $bio,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:              $data['name'],
            phone:             $data['phone'],
            email:             $data['email'] ?? null,
            preferredPosition: $data['preferred_position'],
            cityId:            (int) $data['city_id'],
            bio:               $data['bio'] ?? null,
        );
    }
}
