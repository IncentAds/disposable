<?php

namespace Incentads\Disposable;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;
use Incentads\Disposable\Console\UpdateDisposableDomainsCommand;
use Incentads\Disposable\Console\UpdateDisposableNumbersCommand;
use Incentads\Disposable\Core\Phone\PhoneDecisionNode;
use Incentads\Disposable\Validation\DisposableEmail;
use Incentads\Disposable\Validation\DisposableNumber;

class DisposableServiceProvider extends ServiceProvider
{
    /**
     * The config source path.
     *
     * @var string
     */
    protected string $config = __DIR__ . '/../config/disposable.php';

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
            $this->config => config_path('disposable.php'),
        ], 'disposable-config');

        $this->callAfterResolving('validator', function (Factory $validator): void {
            $validator->extend('disposable_email', DisposableEmail::class . '@validate', DisposableEmail::$errorMessage);
            $validator->extend('disposable_phone', DisposableNumber::class . '@validate', DisposableNumber::$errorMessage);
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom($this->config, 'disposable');

        $this->bindDisposableService('disposable_email.domains', DisposableDomains::class, 'email');
        $this->bindDisposableService('disposable_phone.numbers', DisposableNumbers::class, 'phone');

        $this->app->singleton(PhoneDecisionNode::class, function ($app) {
            $nodes = $app['config']["disposable.nodes"];
            $nodes = $this->buildInstances($nodes, $app);
            return new PhoneDecisionNode($nodes);
        });
    }

    private function bindDisposableService(string $key, string $class, string $configKey): void
    {
        $this->app->singleton($key, function ($app) use ($class, $configKey) {
            // Only build and pass the requested cache store if caching is enabled.
            if ($app['config']["disposable.{$configKey}.cache.enabled"]) {
                $store = $app['config']["disposable.{$configKey}.cache.store"];
                $cache = $app['cache']->store('default' === $store ? $app['config']['cache.default'] : $store);
            }

            $instance = new $class($cache ?? null);

            $instance->setStoragePath($app['config']["disposable.{$configKey}.storage"]);
            $instance->setCacheKey($app['config']["disposable.{$configKey}.cache.key"]);
            $instance->setWhitelist($app['config']["disposable.{$configKey}.whitelist"]);
            $instance->setBlacklist($app['config']["disposable.{$configKey}.blacklist"]);

            if ('email' === $configKey) {
                $instance->setIncludeSubdomains($app['config']['disposable.email.include_subdomains']);
            }

            return $instance->bootstrap();
        });

        $this->app->alias($key, $class);
    }

    private function buildInstances(mixed $nodes, $app): array
    {
        $result = [];
        foreach ($nodes as $key => $node) {
            $result[] = $app[$node];
        }
        return $result;
    }
}
