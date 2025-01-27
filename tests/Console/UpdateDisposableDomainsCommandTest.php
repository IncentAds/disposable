<?php

namespace CristianPeter\LaravelDisposableContactGuard\Tests\Console;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use CristianPeter\LaravelDisposableContactGuard\Contracts\Fetcher;
use CristianPeter\LaravelDisposableContactGuard\Tests\TestCase;

class UpdateDisposableDomainsCommandTest extends TestCase
{
    #[Test]
    public function it_creates_the_file()
    {
        $this->assertFileDoesNotExist($this->storagePath);

        $this->artisan('disposable:update')
            ->assertExitCode(0);

        $this->assertFileExists($this->storagePath);

        $domains = $this->disposable()->getDomains();

        $this->assertIsArray($domains);
        $this->assertContains('yopmail.com', $domains);
    }

    #[Test]
    public function it_overwrites_the_file()
    {
        file_put_contents($this->storagePath, json_encode(['foo']));

        $this->artisan('disposable:update')
            ->assertExitCode(0);

        $this->assertFileExists($this->storagePath);

        $domains = $this->disposable()->getDomains();

        $this->assertIsArray($domains);
        $this->assertContains('yopmail.com', $domains);
        $this->assertNotContains('foo', $domains);
    }

    #[Test]
    public function it_doesnt_overwrite_on_fetch_failure()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Source URL is null');

        file_put_contents($this->storagePath, json_encode(['foo']));

        $this->app['config']['disposable-guard.email.sources'] = [null];

        $this->artisan('disposable:update')
            ->assertExitCode(1);

        $this->assertFileExists($this->storagePath);

        $domains = $this->disposable()->getDomains();
        $this->assertEquals(['foo'], $domains);
    }

    #[Test]
    public function it_can_use_a_custom_fetcher()
    {
        file_put_contents($this->storagePath, json_encode(['foo']));

        $this->app['config']['disposable-guard.email.sources'] = ['bar'];
        $this->app['config']['disposable-guard.email.fetcher'] = CustomFetcher::class;

        $this->artisan('disposable:update')
            ->assertExitCode(0);

        $this->assertFileExists($this->storagePath);

        $domains = $this->disposable()->getDomains();
        $this->assertEquals(['bar'], $domains);
    }

    #[Test]
    public function custom_fetchers_need_fetcher_contract()
    {
        file_put_contents($this->storagePath, json_encode(['foo']));

        $this->app['config']['disposable-guard.email.sources'] = ['bar'];
        $this->app['config']['disposable-guard.email.fetcher'] = InvalidFetcher::class;

        $this->artisan('disposable:update')
            ->assertExitCode(1);

        $this->assertFileExists($this->storagePath);

        $domains = $this->disposable()->getDomains();
        $this->assertNotEquals(['foo'], $domains);
    }

    #[Test]
    public function it_processes_legacy_source_config()
    {
        file_put_contents($this->storagePath, json_encode(['foo']));

        $this->app['config']['disposable-guard.email.sources'] = null;
        $this->app['config']['disposable-guard.email.source'] = 'bar';
        $this->app['config']['disposable-guard.email.fetcher'] = CustomFetcher::class;

        $this->artisan('disposable:update')
            ->assertExitCode(0);

        $this->assertFileExists($this->storagePath);

        $domains = $this->disposable()->getDomains();
        $this->assertEquals(['bar'], $domains);
    }
}

class CustomFetcher implements Fetcher
{
    public function handle($url): array
    {
        return [$url];
    }
}

class InvalidFetcher
{
    public function handle($url)
    {
        return $url;
    }
}
