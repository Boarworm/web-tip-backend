<?php namespace Boarworm\Restapi\Http\Controllers\v1;

use Boarworm\Restapi\Repositories\Contracts\ProjectRepositoryInterface;
use Boarworm\Restapi\Http\Resources\ProjectResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ProjectsController extends Controller
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository
    )
    {
    }

    public function index()
    {
        $projects = $this->projectRepository->getAll();

        return response()->json([
            'status' => 'success',
            'message' => 'Projects retrieved successfully',
            'data' => ProjectResource::collection($projects)
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $project = $this->projectRepository->getById($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Project retrieved successfully',
            'data' => ProjectResource::make($project)
        ], 200);
    }
}
