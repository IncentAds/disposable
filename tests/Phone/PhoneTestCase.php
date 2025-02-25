<?php

namespace Incentads\Disposable\Tests\Phone;

use Illuminate\Foundation\Application;
use Incentads\Disposable\DisposableNumbers;
use Incentads\Disposable\Tests\TestCase;

abstract class PhoneTestCase extends TestCase
{
    /**
     * @var string
     */
    protected string $storagePath = __DIR__ . '/numbers.json';

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
        $app['config']->set('disposable.phone.storage', $this->storagePath);
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
        return ['Indisposable' => 'Incentads\Disposable\Facades\DisposableNumbers'];
    }

    /**
     * @return DisposableNumbers
     */
    protected function disposable(): DisposableNumbers
    {
        return $this->app['disposable_phone.numbers'];
    }
}
