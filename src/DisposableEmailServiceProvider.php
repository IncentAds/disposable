<?php

namespace CristianPeter\LaravelDisposableContactGuard;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;
use CristianPeter\LaravelDisposableContactGuard\Console\UpdateDisposableDomainsCommand;
use CristianPeter\LaravelDisposableContactGuard\Validation\Indisposable;

class DisposableEmailServiceProvider extends ServiceProvider
{
    /**
     * The config source path.
     *
     * @var string
     */
    protected string $config = __DIR__.'/../config/disposable-guard.php';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands(UpdateDisposableDomainsCommand::class);
        }

        $this->publishes([
            $this->config => config_path('disposable-guard.php'),
        ], 'laravel-disposable-guard');

        $this->callAfterResolving('validator', function (Factory $validator) {
            $validator->extend('indisposable', Indisposable::class.'@validate', Indisposable::$errorMessage);
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom($this->config, 'disposable-guard');

        $this->app->singleton('disposable_email.domains', function ($app) {
            // Only build and pass the requested cache store if caching is enabled.
            if ($app['config']['disposable-guard.email.cache.enabled']) {
                $store = $app['config']['disposable-guard.email.cache.store'];
                $cache = $app['cache']->store($store == 'default' ? $app['config']['cache.default'] : $store);
            }

            $instance = new DisposableDomains($cache ?? null);

            $instance->setIncludeSubdomains($app['config']['disposable-guard.email.include_subdomains']);
            $instance->setStoragePath($app['config']['disposable-guard.email.storage']);
            $instance->setCacheKey($app['config']['disposable-guard.email.cache.key']);
            $instance->setWhitelist($app['config']['disposable-guard.email.whitelist']);
            $instance->setBlacklist($app['config']['disposable-guard.email.blacklist']);

            return $instance->bootstrap();
        });

        $this->app->alias('disposable_email.domains', DisposableDomains::class);
    }
}
