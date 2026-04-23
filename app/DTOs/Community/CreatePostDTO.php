<?php




// FILE: app/DTOs/Community/CreatePostDTO.php
// ============================================================
namespace App\DTOs\Community;

final class CreatePostDTO
{
    public $timestamps = false;
    public function __construct(
        public readonly int     $userId,
        public readonly int     $cityId,
        public readonly string  $content,
        public readonly ?string $visibility = 'public',
    ) {}

    public static function fromArray(array $data, int $userId): self
    {
        return new self(
            userId:     $userId,
            cityId:     (int) $data['city_id'],
            content:    $data['content'],
            visibility: $data['visibility'] ?? 'public',
        );
    }
}

// ============================================================