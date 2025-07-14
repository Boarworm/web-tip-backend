<?php namespace Boarworm\Restapi;

use Boarworm\Restapi\Http\Controllers\v1\ProjectsController;
use Boarworm\Restapi\Http\Controllers\v1\PostsController;
use Boarworm\Restapi\Http\Controllers\v1\SiteSettingsController;
use Boarworm\Restapi\Http\Middleware\AuthMiddleware;
use Boarworm\Restapi\Http\Middleware\ExceptionsMiddleware;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api/v1',
    'middleware' => [
        AuthMiddleware::class,
        ExceptionsMiddleware::class,
    ]
], function () {
    Route::get('portfolio/projects', [ProjectsController::class, 'index']);
    Route::get('portfolio/projects/{id}', [ProjectsController::class, 'show']);
    Route::get('blog/posts', [PostsController::class, 'index']);
    Route::get('blog/posts/{slug}', [PostsController::class, 'show']);
    Route::get('site/settings', [SiteSettingsController::class, 'index']);
});
