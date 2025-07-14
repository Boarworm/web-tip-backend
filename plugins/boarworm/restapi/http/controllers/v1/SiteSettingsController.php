<?php namespace Boarworm\Restapi\Http\Controllers\v1;

use Boarworm\Restapi\Repositories\Contracts\SiteSettingsRepositoryInterface;
use Boarworm\Restapi\Http\Resources\SiteSettingsResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SiteSettingsController extends Controller
{
    public function __construct(
        private readonly SiteSettingsRepositoryInterface $siteSettingsRepository
    )
    {
    }

    public function index(): JsonResponse
    {
        $settings = $this->siteSettingsRepository->getAll();

        return response()->json([
            'status' => 'success',
            'message' => 'Site settings retrieved successfully',
            'data' => SiteSettingsResource::make($settings),
        ]);
    }
}
