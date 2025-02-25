<?php

namespace Incentads\Disposable\Tests\Mail;

use Illuminate\Foundation\Application;
use Incentads\Disposable\DisposableDomains;
use Incentads\Disposable\Tests\TestCase;

abstract class EmailTestCase extends TestCase
{
    /**
     * @var string
     */
    protected string $storagePath = __DIR__ . '/disposable_domains.json';

    /**
     * Set up the test environment.
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
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('disposable.email.storage', $this->storagePath);
    }

    /**
     * Package Service Providers
     *
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return ['Incentads\Disposable\DisposableServiceProvider'];
    }

    /**
     * Package Aliases
     *
     * @param  Application  $app
     * @return array
     */
    protected function getPackageAliases($app): array
    {
        return ['Indisposable' => 'Incentads\Disposable\Facades\DisposableDomains'];
    }

    /**
     * @return DisposableDomains
     */
    protected function disposable(): DisposableDomains
    {
        return $this->app['disposable_email.domains'];
    }
}
