<?php


// FILE: app/Services/CommunityService.php
// ============================================================
namespace App\Services;

use App\DTOs\Community\CreatePostDTO;
use App\Models\{City, Comment, Post};
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CommunityService
{
    public $timestamps = false;
    public function __construct(
        private readonly PostRepositoryInterface $postRepo,
    ) {}

    public function getFeed(int $cityId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->postRepo->paginate($cityId, $perPage);
    }

    public function createPost(CreatePostDTO $dto): Post
    {
        
        return $this->postRepo->create([
            'user_id' => $dto->userId,
            'city_id' => $dto->cityId,
            'content' => $dto->content,
        ]);
    }

    public function toggleLike(int $postId, int $userId): array
    {
        $liked = $this->postRepo->toggleLike($postId, $userId);
        $count = Post::withCount('likes')->find($postId)->likes_count;
        return ['liked' => $liked, 'count' => $count];
    }

    public function addComment(int $postId, int $userId, string $content): Comment
    {
        return Comment::create([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content,
        ]);
    }

    public function getCityForums(): \Illuminate\Database\Eloquent\Collection
    {
        return City::withCount(['posts as message_count'])->get();
    }
}

// ============================================================