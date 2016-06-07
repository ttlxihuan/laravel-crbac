<?php namespace Barryvdh\Debugbar;

use Laravel\Lumen\Application;

class LumenServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /** @var  Application */
    protected $app;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['crbac'] = $this->app->share(function ($app) {//取菜单，判断是否有权限
            return new Crbac($app['events'], $app);
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $app = $this->app;

        if ($app->runningInConsole()) {
            $this->app['config']->set('debugbar.enabled', false);
        }

        $routeConfig = [
            'namespace' => 'Barryvdh\Debugbar\Controllers',
            'prefix' => $this->app['config']->get('debugbar.route_prefix'),
        ];

        $this->app->group($routeConfig, function($router) {
            $router->get('open', [
                'uses' => 'OpenHandlerController@handle',
                'as' => 'debugbar.openhandler',
            ]);

            $router->get('assets/stylesheets', [
                'uses' => 'AssetController@css',
                'as' => 'debugbar.assets.css',
            ]);

            $router->get('assets/javascript', [
                'uses' => 'AssetController@js',
                'as' => 'debugbar.assets.js',
            ]);
        });

        $enabled = $this->app['config']->get('debugbar.enabled');

        // If enabled is null, set from the app.debug value
        if (is_null($enabled)) {
            $enabled = env('APP_DEBUG');
            $this->app['config']->set('debugbar.enabled', $enabled);
        }


        if ( ! $enabled) {
            return;
        }

        $this->app['config']->set('debugbar.options.logs.file', storage_path('logs/lumen.log'));

        /** @var LaravelDebugbar $debugbar */
        $debugbar = $this->app['debugbar'];
        $debugbar->boot();

        $app->middleware(['Barryvdh\Debugbar\Middleware\Debugbar']);


    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('debugbar', 'command.debugbar.clear');
    }
}
