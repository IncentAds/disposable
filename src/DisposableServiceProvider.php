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

        $this->bindDisposableService('disposable_email.domains', DisposableDomains::class, 'email');
        $this->bindDisposableService('disposable_phone.numbers', DisposableNumbers::class, 'phone');
    }

    private function bindDisposableService(string $key, string $class, string $configKey): void
    {
        $this->app->singleton($key, function ($app) use ($class, $configKey) {
            // Only build and pass the requested cache store if caching is enabled.
            if ($app['config']["disposable-guard.$configKey.cache.enabled"]) {
                $store = $app['config']["disposable-guard.$configKey.cache.store"];
                $cache = $app['cache']->store($store == 'default' ? $app['config']['cache.default'] : $store);
            }

            $instance = new $class($cache ?? null);

            $instance->setStoragePath($app['config']["disposable-guard.$configKey.storage"]);
            $instance->setCacheKey($app['config']["disposable-guard.$configKey.cache.key"]);
            $instance->setWhitelist($app['config']["disposable-guard.$configKey.whitelist"]);
            $instance->setBlacklist($app['config']["disposable-guard.$configKey.blacklist"]);

            if ($configKey === 'email') {
                $instance->setIncludeSubdomains($app['config']['disposable-guard.email.include_subdomains']);
            }

            return $instance->bootstrap();
        });

        $this->app->alias($key, $class);
    }
}
