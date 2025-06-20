<?php namespace Boarworm\Extender;

use System\Classes\PluginBase;

/**
 * Extender Plugin Information File
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
			'name' => 'Extender',
			'description' => 'No description provided yet...',
			'author' => 'Boarworm',
			'icon' => 'icon-leaf'
		];
	}

	/**
	 * Register method, called when the plugin is first registered.
	 *
	 * @return void
	 */
	public function register()
	{

	}

	/**
	 * Boot method, called right before the request route.
	 *
	 * @return array
	 */

	public function boot()
	{
	}

	public function registerMarkupTags()
	{
		return [
			'functions' => [
				'custom_twig_format_tel' => function ($tel) {
					return str_replace(array(' ', '(', ')', '-'), '', $tel);
				}
			]
		];
	}

	/**
	 * Registers any front-end components implemented in this plugin.
	 *
	 * @return array
	 */
	public function registerComponents()
	{
		return [];
	}

	/**
	 * Registers any back-end permissions used by this plugin.
	 *
	 * @return array
	 */
	public function registerPermissions()
	{
		return [];
	}

	/**
	 * Registers back-end navigation items for this plugin.
	 *
	 * @return array
	 */
	public function registerNavigation()
	{
		return [];
	}
}
