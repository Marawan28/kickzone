<?php


// FILE: app/Repositories/Eloquent/PostRepository.php
// ============================================================
namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostRepository implements PostRepositoryInterface
{
    public $timestamps = false;
    public function paginate(int $cityId, int $perPage = 15): LengthAwarePaginator
    {
        return Post::with(['user', 'city'])
                   ->withCount(['likes', 'comments'])
                   ->where('city_id', $cityId)
                   ->latest()
                   ->paginate($perPage);
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function toggleLike(int $postId, int $userId): bool
    {
        $post   = Post::findOrFail($postId);
        $exists = $post->likes()->where('user_id', $userId)->exists();

        if ($exists) {
            $post->likes()->detach($userId);
            return false;
        }

        $post->likes()->attach($userId);
        return true;
    }

    public function findById(int $id): ?Post
    {
        return Post::with(['user', 'comments.user'])->find($id);
    }
}
