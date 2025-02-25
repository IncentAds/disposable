<?php

namespace Incentads\Disposable\Tests\Unit\Mail;

use Incentads\Disposable\DisposableDomains;
use Incentads\Disposable\Tests\EmailTestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class DisposableDomainsTest extends EmailTestCase
{
    #[Test]
    public function it_can_be_resolved_using_alias(): void
    {
        $this->assertEquals(DisposableDomains::class, get_class($this->app->make('disposable_email.domains')));
    }

    #[Test]
    public function it_can_be_resolved_using_class(): void
    {
        $this->assertEquals(DisposableDomains::class, get_class($this->app->make(DisposableDomains::class)));
    }

    #[Test]
    public function it_can_get_storage_path(): void
    {
        $this->assertEquals(
            $this->app['config']['disposable.email.storage'],
            $this->disposable()->getStoragePath(),
        );
    }

    #[Test]
    public function it_can_set_storage_path(): void
    {
        $this->disposable()->setStoragePath('foo');

        $this->assertEquals('foo', $this->disposable()->getStoragePath());
    }

    #[Test]
    public function it_can_get_include_subdomains(): void
    {
        $this->assertEquals(
            $this->app['config']['disposable.email.include_subdomains'],
            $this->disposable()->getIncludeSubdomains(),
        );
    }

    #[Test]
    public function it_can_set_include_subdomains(): void
    {
        $this->disposable()->setIncludeSubdomains(true);

        $this->assertEquals(true, $this->disposable()->getIncludeSubdomains());
    }

    #[Test]
    public function it_can_get_cache_key(): void
    {
        $this->assertEquals(
            $this->app['config']['disposable.email.cache.key'],
            $this->disposable()->getCacheKey(),
        );
    }

    #[Test]
    public function it_can_set_cache_key(): void
    {
        $this->disposable()->setCacheKey('foo');

        $this->assertEquals('foo', $this->disposable()->getCacheKey());
    }

    #[Test]
    public function it_takes_cached_domains_if_available(): void
    {
        $this->app['cache.store'][$this->disposable()->getCacheKey()] = ['foo'];

        $this->disposable()->bootstrap();

        $domains = $this->disposable()->getDomains();

        $this->assertEquals(['foo'], array_values($domains));
    }

    #[Test]
    public function it_flushes_invalid_cache_values(): void
    {
        $this->app['cache.store'][$this->disposable()->getCacheKey()] = 'foo';

        $this->disposable()->bootstrap();

        $this->assertNotEquals('foo', $this->app['cache.store'][$this->disposable()->getCacheKey()]);
    }

    #[Test]
    public function it_skips_cache_when_configured(): void
    {
        $this->app['config']['disposable.email.cache.enabled'] = false;

        $domains = $this->disposable()->getDomains();

        $this->assertIsArray($domains);
        $this->assertNull($this->app['cache.store'][$this->disposable()->getCacheKey()]);
        $this->assertContains('yopmail.com', $domains);
    }

    #[Test]
    public function it_takes_storage_domains_when_cache_is_not_available(): void
    {
        $this->app['config']['disposable.email.cache.enabled'] = false;

        file_put_contents($this->storagePath, json_encode(['foo']));

        $this->disposable()->bootstrap();

        $domains = $this->disposable()->getDomains();

        $this->assertEquals(['foo'], array_values($domains));
    }

    #[Test]
    public function it_takes_package_domains_when_storage_is_not_available(): void
    {
        $this->app['config']['disposable.email.cache.enabled'] = false;

        $domains = $this->disposable()->getDomains();

        $this->assertIsArray($domains);
        $this->assertContains('yopmail.com', $domains);
    }

    #[Test]
    public function it_can_flush_storage(): void
    {
        file_put_contents($this->storagePath, 'foo');

        $this->disposable()->flushStorage();

        $this->assertFileDoesNotExist($this->storagePath);
    }

    #[Test]
    public function it_doesnt_throw_exceptions_for_flush_storage_when_file_doesnt_exist(): void
    {
        $this->disposable()->flushStorage();

        $this->assertTrue(true);
    }

    #[Test]
    public function it_can_flush_cache(): void
    {
        $this->app['cache.store'][$this->disposable()->getCacheKey()] = 'foo';

        $this->assertEquals('foo', $this->app['cache']->get($this->disposable()->getCacheKey()));

        $this->disposable()->flushCache();

        $this->assertNull($this->app['cache']->get($this->disposable()->getCacheKey()));
    }

    #[Test]
    public function it_can_verify_disposability(): void
    {
        $this->assertTrue($this->disposable()->isDisposable('example@yopmail.com'));
        $this->assertFalse($this->disposable()->isLegit('example@yopmail.com'));
        $this->assertFalse($this->disposable()->isIndisposable('example@yopmail.com'));

        $this->assertFalse($this->disposable()->isDisposable('example@gmail.com'));
        $this->assertTrue($this->disposable()->isLegit('example@gmail.com'));
        $this->assertTrue($this->disposable()->isIndisposable('example@gmail.com'));
    }

    #[Test]
    public function it_checks_the_full_email_domain(): void
    {
        $this->assertTrue($this->disposable()->isDisposable('example@mailinator.com'));
        $this->assertTrue($this->disposable()->isDisposable('example@mail.mailinator.com'));
        $this->assertTrue($this->disposable()->isLegit('example@isnotdisposable.mailinator.com'));
    }

    #[Test]
    public function it_doesnt_check_subdomains_when_not_configured(): void
    {
        $this->disposable()->setIncludeSubdomains(false);

        $this->assertFalse($this->disposable()->isDisposable('example@subdomain.mailinator.com'));
    }

    #[Test]
    public function it_checks_subdomains_when_configured(): void
    {
        $this->disposable()->setIncludeSubdomains(true);

        $this->assertTrue($this->disposable()->isDisposable('example@subdomain.mailinator.com'));

        // Not a subdomain OR root domain!
        $this->assertFalse($this->disposable()->isDisposable('example@subdomainmailinator.com'));
    }

    #[Test]
    public function it_still_checks_root_domains_when_subdomain_checking_is_configured(): void
    {
        $this->disposable()->setIncludeSubdomains(true);

        $this->assertTrue($this->disposable()->isDisposable('example@mailinator.com'));

        // Not a subdomain OR root domain!
        $this->assertFalse($this->disposable()->isDisposable('example@subdomainmailinator.com'));
    }

    #[Test]
    public function it_can_exclude_whitelisted_domains(): void
    {
        $this->disposable()->setWhitelist(['yopmail.com']);
        $this->disposable()->bootstrap();

        $domains = $this->disposable()->getDomains();

        $this->assertIsArray($domains);
        $this->assertNotContains('yopmail.com', $domains);
        $this->assertTrue($this->disposable()->isLegit('example@yopmail.com'));

        $this->disposable()->setIncludeSubdomains(true);
        $this->assertTrue($this->disposable()->isLegit('example@subdomain.yopmail.com'));
    }
    #[Test]
    public function it_has_domain_changed_when_blacklist_differ_domain_list(): void
    {
        $disposableDomainsMock = Mockery::mock(DisposableDomains::class)
            ->makePartial();

        $disposableDomainsMock->shouldReceive('getDomains')->andReturn(['yopmail.com']);
        $disposableDomainsMock->shouldReceive('getBlacklist')->once()->andReturn(['@yop.yopmail.com']);

        $hasChanged = $disposableDomainsMock->hasNewBlackListItem();

        $this->assertTrue($hasChanged);
        Mockery::close();
    }
    #[Test]
    public function it_has_not_domain_changed_when_blacklist_is_the_same_domain_list(): void
    {
        $disposableDomainsMock = Mockery::mock(DisposableDomains::class)
            ->makePartial();

        $disposableDomainsMock->shouldReceive('getDomains')->andReturn(['yopmail.com']);
        $disposableDomainsMock->shouldReceive('getBlacklist')->once()->andReturn(['yopmail.com']);

        $hasChanged = $disposableDomainsMock->hasNewBlackListItem();

        $this->assertFalse($hasChanged);
        Mockery::close();
    }
}
