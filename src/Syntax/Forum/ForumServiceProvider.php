<?php namespace Syntax\Forum;

use Illuminate\Support\ServiceProvider;

class ForumServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('syntax/forum');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->shareWithApp();
		$this->loadConfig();
		$this->registerViews();
		$this->registerAliases();
	}

	/**
	 * Share the package with application
	 *
	 * @return void
	 */
	protected function shareWithApp()
	{
		$this->app['forum'] = $this->app->share(function($app)
		{
			return true;
		});
	}

	/**
	 * Load the config for the package
	 *
	 * @return void
	 */
	protected function loadConfig()
	{
		$this->app['config']->package('syntax/forum', __DIR__.'/../../../config');
	}

	/**
	 * Register views
	 *
	 * @return void
	 */
	protected function registerViews()
	{
		$this->app['view']->addNamespace('forum', __DIR__.'/../../../views');
	}

	/**
	 * Register aliases
	 *
	 * @return void
	 */
	protected function registerAliases()
	{
		$aliases = [
			'ForumPost'                   => 'Syntax\Core\Forum\Facades\ForumPost',
			'Forum'                       => 'Syntax\Core\Forum',
			'Forum_Board'                 => 'Syntax\Core\Forum_Board',
			'Forum_Board_Type'            => 'Syntax\Core\Forum_Board_Type',
			'Forum_Category'              => 'Syntax\Core\Forum_Category',
			'Forum_Category_Type'         => 'Syntax\Core\Forum_Category_Type',
			'Forum_Moderation'            => 'Syntax\Core\Forum_Moderation',
			'Forum_Moderation_Log'        => 'Syntax\Core\Forum_Moderation_Log',
			'Forum_Moderation_Reply'      => 'Syntax\Core\Forum_Moderation_Reply',
			'Forum_View'                  => 'Syntax\Core\Forum_View',
			'Forum_Post'                  => 'Syntax\Core\Forum_Post',
			'Forum_Post_Edit'             => 'Syntax\Core\Forum_Post_Edit',
			'Forum_Post_Status'           => 'Syntax\Core\Forum_Post_Status',
			'Forum_Post_Type'             => 'Syntax\Core\Forum_Post_Type',
			'Forum_Post_View'             => 'Syntax\Core\Forum_Post_View',
			'Forum_Reply'                 => 'Syntax\Core\Forum_Reply',
			'Forum_Reply_Edit'            => 'Syntax\Core\Forum_Reply_Edit',
			'Forum_Reply_Roll'            => 'Syntax\Core\Forum_Reply_Roll',
			'Forum_Reply_Type'            => 'Syntax\Core\Forum_Reply_Type',
			'Forum_Support_Status'        => 'Syntax\Core\Forum_Support_Status',
		];

		$appAliases = \Config::get('core::nonCoreAliases');

		foreach ($aliases as $alias => $class) {
			if (!is_null($appAliases)) {
				if (!in_array($alias, $appAliases)) {
					\Illuminate\Foundation\AliasLoader::getInstance()->alias($alias, $class);
				}
			} else {
				\Illuminate\Foundation\AliasLoader::getInstance()->alias($alias, $class);
			}
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}