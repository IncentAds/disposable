<?php

namespace CristianPeter\LaravelDisposableContactGuard\Tests;

use CristianPeter\LaravelDisposableContactGuard\DisposableDomains;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var string
     */
    protected string $storagePath = __DIR__.'/domains.json';

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('disposable-guard.email.storage', $this->storagePath);
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->disposable()->flushStorage();
        $this->disposable()->flushCache();
    }

    /**
     * Clean up the testing environment before the next test.
     */
    public function tearDown(): void
    {
        $this->disposable()->flushStorage();
        $this->disposable()->flushCache();

        parent::tearDown();
    }

    /**
     * Package Service Providers
     *
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return ['CristianPeter\LaravelDisposableContactGuard\DisposableEmailServiceProvider'];
    }

    /**
     * Package Aliases
     *
     * @param  Application  $app
     * @return array
     */
    protected function getPackageAliases($app): array
    {
        return ['Indisposable' => 'CristianPeter\LaravelDisposableContactGuard\Facades\DisposableDomains'];
    }

    /**
     * @return DisposableDomains
     */
    protected function disposable(): DisposableDomains
    {
        return $this->app['disposable_email.domains'];
    }
}
