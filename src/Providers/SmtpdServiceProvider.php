<?php

namespace Smtpd\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Smtpd\Auth\SmtpGuard;
use Smtpd\Commands\Listen;
use Smtpd\ServerManager;

class SmtpdServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerAuthGuard();
        $this->registerCommands();
    }

    /**
     * Register the config file.
     *
     * @return void
     */
    protected function registerConfig()
    {
        // Config file path.
        $dist = __DIR__ . '/../../config/smtpd.php';
        // If we're installing in to a Lumen project, config_path
        // won't exist so we can't auto-publish the config
        if (function_exists('config_path')) {
            // Publishes config File.
            $this->publishes([
                                 $dist => config_path('smtpd.php'),
                             ]);
        }
        // Merge config.
        $this->mergeConfigFrom($dist, 'smtpd');
    }

    /**
     * Register the auth guard
     *
     * @return void
     */
    protected function registerAuthGuard()
    {
        Auth::extend('smtp', function ($app, $name, array $config) {
            return new SmtpGuard(
                Auth::createUserProvider($config['provider']),
                Arr::get($config, 'username_field')
            );
        });
    }

    /**
     * Register the console commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                                Listen::class,
                            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ServerManager::class, function ($app) {
            return new ServerManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ServerManager::class];
    }

}
