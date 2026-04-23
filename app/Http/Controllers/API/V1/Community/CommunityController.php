<?php

// ============================================================
// FILE: app/Http/Controllers/API/V1/Community/CommunityController.php
// ============================================================
namespace App\Http\Controllers\API\V1\Community;

use App\DTOs\Community\CreatePostDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Community\{AddCommentRequest, CreatePostRequest};
use App\Http\Resources\Community\{CommentResource, PostResource};
use App\Services\CommunityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Community", description="Posts, comments, likes, city forums")
 */
class CommunityController extends Controller
{
    public $timestamps = false;
    public function __construct(
        private readonly CommunityService $communityService,
    ) {}

    /**
     * @OA\Get(path="/api/v1/community/feed", tags={"Community"}, security={{"sanctum":{}}})
     */
    public function feed(Request $request): JsonResponse
    {
        $cityId = $request->query('city_id', $request->user()->city_id);
        $posts  = $this->communityService->getFeed((int) $cityId);
        return response()->json(['data' => PostResource::collection($posts)]);
    }

    /**
     * @OA\Post(path="/api/v1/community/posts", tags={"Community"}, security={{"sanctum":{}}})
     */
    public function store(CreatePostRequest $request): JsonResponse
    {
        $post = $this->communityService->createPost(
            CreatePostDTO::fromArray($request->validated(), $request->user()->id)
        );

        // Handle optional image upload via Spatie
        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')->toMediaCollection('post_images');
        }

        return response()->json(['data' => new PostResource($post)], 201);
    }

    /**
     * @OA\Post(path="/api/v1/community/posts/{id}/like", tags={"Community"}, security={{"sanctum":{}}})
     */
    public function toggleLike(Request $request, int $id): JsonResponse
    {
        $result = $this->communityService->toggleLike($id, $request->user()->id);
        return response()->json($result);
    }

    /**
     * @OA\Post(path="/api/v1/community/posts/{id}/comments", tags={"Community"}, security={{"sanctum":{}}})
     */
    public function addComment(AddCommentRequest $request, int $id): JsonResponse
    {
        $comment = $this->communityService->addComment(
            $id,
            $request->user()->id,
            $request->content,
        );
        return response()->json(['data' => new CommentResource($comment)], 201);
    }

    /**
     * @OA\Get(path="/api/v1/community/forums", tags={"Community"}, security={{"sanctum":{}}})
     */
    public function forums(): JsonResponse
    {
        $forums = $this->communityService->getCityForums();
        return response()->json(['data' => $forums]);
    }
}
