<?php namespace Boarworm\Restapi;

use Boarworm\Restapi\Repositories\Contracts\PostRepositoryInterface;
use Boarworm\Restapi\Repositories\Contracts\ProjectRepositoryInterface;
use Boarworm\Restapi\Repositories\Contracts\SiteSettingsRepositoryInterface;
use Boarworm\Restapi\Repositories\Eloquent\PostRepository;
use Boarworm\Restapi\Repositories\Eloquent\ProjectRepository;
use Boarworm\Restapi\Repositories\Eloquent\SiteSettingsRepository;
use System\Classes\PluginBase;
use Config;
//use Fruitcake\Cors\CorsServiceProvider;
//use Fruitcake\Cors\HandleCors;
//use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Contracts\Http\Kernel;

/**
 * Restapi Plugin Information File
 */
class Plugin extends PluginBase
{
	/**
	 * Returns information about this plugin.
	 *
	 * @return array
	 */
	public function pluginDetails()
	{
		return [
			'name' => 'Restapi',
			'description' => 'No description provided yet...',
			'author' => 'Boarworm',
			'icon' => 'icon-leaf'
		];
	}

	/**
	 * Boot method, called right before the request route.
	 *
	 * @return array
	 */
	public function boot(): void
	{
		Config::set('cors', Config::get('boarworm.restapi::cors'));
//		$this->app->register(CorsServiceProvider::class);
//		$this->app[Kernel::class]->pushMiddleware(HandleCors::class);
	}

    public function register()
    {
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(SiteSettingsRepositoryInterface::class, SiteSettingsRepository::class);
    }
}
