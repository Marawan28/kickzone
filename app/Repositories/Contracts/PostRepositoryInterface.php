<?php





// FILE: app/Repositories/Contracts/PostRepositoryInterface.php
// ============================================================
namespace App\Repositories\Contracts;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PostRepositoryInterface
{
    public function paginate(int $cityId, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Post;
    public function toggleLike(int $postId, int $userId): bool; // returns true=liked false=unliked
    public function findById(int $id): ?Post;
}

// ============================================================