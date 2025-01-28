<?php

namespace CristianPeter\LaravelDisposableContactGuard;

use CristianPeter\LaravelDisposableContactGuard\Console\UpdateDisposableDomainsCommand;
use CristianPeter\LaravelDisposableContactGuard\Console\UpdateDisposableNumbersCommand;
use CristianPeter\LaravelDisposableContactGuard\Validation\IndisposableEmail;
use CristianPeter\LaravelDisposableContactGuard\Validation\IndisposableNumber;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;

class DisposableServiceProvider extends ServiceProvider
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
            $this->commands(UpdateDisposableNumbersCommand::class);
        }

        $this->publishes([
            $this->config => config_path('disposable-guard.php'),
        ], 'laravel-disposable-guard');

        $this->callAfterResolving('validator', function (Factory $validator) {
            $validator->extend('indisposable_email', IndisposableEmail::class.'@validate', IndisposableEmail::$errorMessage);
            $validator->extend('indisposable_number', IndisposableNumber::class.'@validate', IndisposableNumber::$errorMessage);
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

        $this->app->singleton('disposable_phone.numbers', function ($app) {
            // Only build and pass the requested cache store if caching is enabled.
            if ($app['config']['disposable-guard.phone.cache.enabled']) {
                $store = $app['config']['disposable-guard.phone.cache.store'];
                $cache = $app['cache']->store($store == 'default' ? $app['config']['cache.default'] : $store);
            }

            $instance = new DisposableNumbers($cache ?? null);

            $instance->setStoragePath($app['config']['disposable-guard.phone.storage']);
            $instance->setCacheKey($app['config']['disposable-guard.phone.cache.key']);
            $instance->setWhitelist($app['config']['disposable-guard.phone.whitelist']);
            $instance->setBlacklist($app['config']['disposable-guard.phone.blacklist']);

            return $instance->bootstrap();
        });

        $this->app->alias('disposable_email.domains', DisposableDomains::class);
        $this->app->alias('disposable_phone.numbers', DisposableNumbers::class);

    }
}
