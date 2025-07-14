<?php namespace Boarworm\Restapi\Http\Controllers\v1;

use Boarworm\Restapi\Repositories\Contracts\PostRepositoryInterface;
use Boarworm\Restapi\Http\Resources\PostResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class PostsController extends Controller
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    )
    {
    }

    public function index()
    {
        $posts = $this->postRepository->getAll();

        return response()->json([
            'status' => 'success',
            'message' => 'Posts retrieved successfully',
            'data' => PostResource::collection($posts)
        ], 200);
    }

    public function show(string $slug): JsonResponse
    {
        $post = $this->postRepository->getBySlug($slug);

        return response()->json([
            'status' => 'success',
            'message' => 'Post retrieved successfully',
            'data' => PostResource::make($post)
        ], 200);
    }
}
