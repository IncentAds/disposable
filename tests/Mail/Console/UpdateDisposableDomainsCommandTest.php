<?php

namespace Incentads\Disposable\Tests\Mail\Console;

use Incentads\Disposable\Tests\Mail\EmailTestCase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;

class UpdateDisposableDomainsCommandTest extends EmailTestCase
{
    #[Test]
    public function it_creates_the_file(): void
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
    public function it_overwrites_the_file(): void
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
    public function it_doesnt_overwrite_on_fetch_failure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Source URL is null');

        file_put_contents($this->storagePath, json_encode(['foo']));

        $this->app['config']['disposable.email.sources'] = [null];

        $this->artisan('disposable:update')
            ->assertExitCode(1);

        $this->assertFileExists($this->storagePath);

        $domains = $this->disposable()->getDomains();
        $this->assertEquals(['foo'], $domains);
    }

    #[Test]
    public function it_can_use_a_custom_fetcher(): void
    {
        file_put_contents($this->storagePath, json_encode(['foo']));

        $this->app['config']['disposable.email.sources'] = ['bar'];
        $this->app['config']['disposable.email.fetcher'] = CustomFetcher::class;

        $this->artisan('disposable:update')
            ->assertExitCode(0);

        $this->assertFileExists($this->storagePath);

        $domains = $this->disposable()->getDomains();
        $this->assertEquals(['bar'], array_values($domains));
    }

    #[Test]
    public function custom_fetchers_need_fetcher_contract(): void
    {
        file_put_contents($this->storagePath, json_encode(['foo']));

        $this->app['config']['disposable.email.sources'] = ['bar'];
        $this->app['config']['disposable.email.fetcher'] = InvalidFetcher::class;

        $this->artisan('disposable:update')
            ->assertExitCode(1);

        $this->assertFileExists($this->storagePath);

        $domains = $this->disposable()->getDomains();
        $this->assertNotEquals(['foo'], $domains);
    }

    #[Test]
    public function it_processes_legacy_source_config(): void
    {
        file_put_contents($this->storagePath, json_encode(['foo']));

        $this->app['config']['disposable.email.sources'] = null;
        $this->app['config']['disposable.email.source'] = 'bar';
        $this->app['config']['disposable.email.fetcher'] = CustomFetcher::class;

        $this->artisan('disposable:update')
            ->assertExitCode(0);

        $this->assertFileExists($this->storagePath);

        $domains = $this->disposable()->getDomains();
        $this->assertEquals(['bar'], array_values($domains));
    }
}
